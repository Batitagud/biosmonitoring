import logging
import os
import sys
import threading
import time
from collections import deque
from datetime import datetime, timezone
from typing import Any, Deque, Dict, List, Tuple

import firebase_admin
import numpy as np
from firebase_admin import credentials, db

try:
    import joblib
except ImportError:  # pragma: no cover
    joblib = None

try:
    import pickle
except ImportError:  # pragma: no cover
    pickle = None


logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
)
logger = logging.getLogger("ml_engine")

REQUIRED_SENSOR_FIELDS = ("gsr_raw", "suhu_kulit", "bpm", "raw_ibi")
WINDOW_SIZE = 30


class FirebaseConfigError(Exception):
    """Raised when Firebase configuration is invalid."""


class ModelLoadError(Exception):
    """Raised when the ML artifacts cannot be loaded."""


class SensorDataError(ValueError):
    """Raised when incoming sensor data is malformed."""


class ArtifactConfigError(Exception):
    """Raised when the loaded feature order does not match the training schema."""


def normalize_db_path(path: str) -> str:
    """Remove leading/trailing slashes for Firebase database paths."""
    return path.strip("/")


def get_env_or_default(name: str, default: str) -> str:
    return os.getenv(name, default)


def initialize_firebase(service_account_path: str, database_url: str) -> Any:
    """Initialize Firebase Admin SDK using a service account JSON file."""
    if not service_account_path:
        raise FirebaseConfigError("Path to the Firebase service account JSON is required.")

    if not os.path.exists(service_account_path):
        raise FileNotFoundError(f"Service account file not found: {service_account_path}")

    if not database_url:
        raise FirebaseConfigError("Firebase Realtime Database URL is required.")

    try:
        if not firebase_admin._apps:
            cred = credentials.Certificate(service_account_path)
            firebase_admin.initialize_app(cred, {"databaseURL": database_url})
        else:
            firebase_admin.get_app()
    except Exception as exc:  # pragma: no cover - defensive
        raise FirebaseConfigError(f"Failed to initialize Firebase Admin SDK: {exc}") from exc

    logger.info("Firebase Admin SDK initialized successfully.")
    return db.reference()


def load_pickle_like_artifact(path: str) -> Any:
    """Load an artifact from .pkl/.joblib using joblib or pickle."""
    if not path:
        raise ModelLoadError("Artifact path is required.")

    if not os.path.exists(path):
        raise FileNotFoundError(f"Artifact file not found: {path}")

    try:
        if joblib is not None:
            return joblib.load(path)
        if pickle is not None:
            with open(path, "rb") as handle:
                return pickle.load(handle)
        raise ModelLoadError("Neither joblib nor pickle is available.")
    except Exception as exc:  # pragma: no cover - defensive
        raise ModelLoadError(f"Failed to load artifact from {path}: {exc}") from exc


def build_expected_feature_names() -> List[str]:
    """Create the expected 16 feature names based on the training schema."""
    names: List[str] = []
    for field in REQUIRED_SENSOR_FIELDS:
        names.extend([f"{field}_mean", f"{field}_std", f"{field}_min", f"{field}_max"])
    return names


def load_ml_artifacts(artifacts_dir: str) -> Dict[str, Any]:
    """Load the model, scaler, encoder, and feature-name metadata from disk."""
    if not artifacts_dir:
        raise ModelLoadError("Artifacts directory is required.")

    if not os.path.isdir(artifacts_dir):
        raise FileNotFoundError(f"Artifacts directory not found: {artifacts_dir}")

    model_path = os.path.join(artifacts_dir, "best_stress_model.pkl")
    scaler_path = os.path.join(artifacts_dir, "scaler.pkl")
    encoder_path = os.path.join(artifacts_dir, "encoder.pkl")
    features_path = os.path.join(artifacts_dir, "features.pkl")

    artifacts = {
        "model": load_pickle_like_artifact(model_path),
        "scaler": load_pickle_like_artifact(scaler_path),
        "encoder": load_pickle_like_artifact(encoder_path),
        "features": load_pickle_like_artifact(features_path),
    }

    feature_names = artifacts["features"]
    if isinstance(feature_names, (list, tuple, np.ndarray)):
        feature_names = [str(name) for name in list(feature_names)]
    else:
        raise ArtifactConfigError("features.pkl must contain a list/array of feature names.")

    expected_names = build_expected_feature_names()
    if feature_names != expected_names:
        raise ArtifactConfigError(
            f"Feature order mismatch. Expected {expected_names}, got {feature_names}"
        )

    logger.info("All ML artifacts loaded successfully from %s", artifacts_dir)
    return artifacts


def parse_sensor_sample(payload: Dict[str, Any]) -> Dict[str, float]:
    """Validate and convert incoming Firebase payload into a 4-feature sensor sample."""
    if not isinstance(payload, dict):
        raise SensorDataError("Incoming payload must be a dictionary.")

    sample: Dict[str, float] = {}
    for field in REQUIRED_SENSOR_FIELDS:
        if field not in payload:
            raise SensorDataError(f"Missing required field: {field}")

        try:
            sample[field] = float(payload[field])
        except (TypeError, ValueError) as exc:
            raise SensorDataError(f"Invalid numeric value for {field}: {payload[field]}") from exc

    return sample


def build_feature_vector(window: Deque[Dict[str, float]]) -> np.ndarray:
    """Create a 16-feature vector from 30 sensor samples using mean/std/min/max."""
    if len(window) != WINDOW_SIZE:
        raise ValueError(f"Expected {WINDOW_SIZE} samples, received {len(window)}")

    matrix = np.array(
        [[sample[field] for field in REQUIRED_SENSOR_FIELDS] for sample in window],
        dtype=np.float64,
    )

    features: List[float] = []
    for column_index in range(matrix.shape[1]):
        column = matrix[:, column_index]
        features.extend(
            [
                float(np.mean(column)),
                float(np.std(column, ddof=0)),
                float(np.min(column)),
                float(np.max(column)),
            ]
        )

    return np.array(features, dtype=np.float64)


def scale_features(scaler: Any, features: np.ndarray) -> np.ndarray:
    """Apply the scaler to the 16-feature vector."""
    try:
        scaled = scaler.transform(features.reshape(1, -1))
    except Exception as exc:  # pragma: no cover - defensive
        raise RuntimeError(f"Feature scaling failed: {exc}") from exc
    return np.asarray(scaled, dtype=np.float64)


def infer_prediction(model: Any, scaler: Any, features: np.ndarray, encoder: Any) -> Tuple[int, str]:
    """Run model inference and decode the prediction label."""
    try:
        scaled_features = scale_features(scaler, features)
        prediction = model.predict(scaled_features)[0]
    except Exception as exc:  # pragma: no cover - defensive
        raise RuntimeError(f"Model inference failed: {exc}") from exc

    numeric_label = int(prediction)
    try:
        decoded_label = encoder.inverse_transform(np.array([numeric_label]))[0]
    except Exception:
        decoded_label = str(numeric_label)

    return numeric_label, str(decoded_label)


def push_prediction(prediction_ref: Any, predicted_label: str, features: np.ndarray) -> None:
    """Write the prediction result and timestamp to Firebase."""
    timestamp = datetime.now(timezone.utc).isoformat()
    payload = {
        "timestamp": timestamp,
        "predicted_label": predicted_label,
        "features": features.tolist(),
    }
    try:
        prediction_ref.push(payload)
        logger.info("Prediction pushed to Firebase: %s", payload)
    except Exception as exc:  # pragma: no cover - defensive
        logger.exception("Failed to push prediction to Firebase: %s", exc)


def handle_sensor_event(
    event: Any,
    window_buffer: Deque[Dict[str, float]],
    artifacts: Dict[str, Any],
    prediction_ref: Any,
) -> None:
    """Process a single incoming Firebase event."""
    try:
        payload = event.data
        if payload is None:
            return
        if not isinstance(payload, dict):
            logger.warning("Skipping non-object payload: %s", type(payload).__name__)
            return
        sample = parse_sensor_sample(payload)
    except Exception as exc:
        logger.warning("Skipping invalid sensor payload: %s", exc)
        return

    window_buffer.append(sample)
    logger.info("Buffer size: %d", len(window_buffer))

    if len(window_buffer) < WINDOW_SIZE:
        return

    try:
        features = build_feature_vector(window_buffer)
        predicted_class, predicted_label = infer_prediction(
            model=artifacts["model"],
            scaler=artifacts["scaler"],
            features=features,
            encoder=artifacts["encoder"],
        )
        logger.info("Prediction result: class=%s label=%s", predicted_class, predicted_label)
        push_prediction(prediction_ref, predicted_label, features)
    except Exception as exc:
        logger.exception("Prediction pipeline failed: %s", exc)
    finally:
        window_buffer.clear()


def run_service(
    service_account_path: str,
    database_url: str,
    sensor_node: str,
    prediction_node: str,
    artifacts_dir: str,
) -> None:
    """Start the streaming service and keep it alive until interrupted."""
    root_ref = initialize_firebase(service_account_path, database_url)
    artifacts = load_ml_artifacts(artifacts_dir)

    sensor_ref = root_ref.child(normalize_db_path(sensor_node))
    prediction_ref = root_ref.child(normalize_db_path(prediction_node))

    window_buffer: Deque[Dict[str, float]] = deque(maxlen=WINDOW_SIZE)

    def on_event(event: Any) -> None:
        try:
            handle_sensor_event(event, window_buffer, artifacts, prediction_ref)
        except Exception as exc:
            logger.exception("Unhandled error in listener callback: %s", exc)

    try:
        listener = sensor_ref.listen(on_event)
        logger.info("Listening for sensor data on /%s", normalize_db_path(sensor_node))
        logger.info("Prediction results will be written to /%s", normalize_db_path(prediction_node))
        logger.info("Press Ctrl+C to stop the service.")

        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        logger.info("Stop signal received. Shutting down service...")
    finally:
        try:
            sensor_ref.off(listener)
        except Exception:
            pass


def main() -> None:
    service_account_path = get_env_or_default("FIREBASE_SERVICE_ACCOUNT", "firebase-service-account.json")
    database_url = get_env_or_default("FIREBASE_DATABASE_URL", "")
    sensor_node = get_env_or_default("FIREBASE_SENSOR_NODE", "/sensor_data")
    prediction_node = get_env_or_default("FIREBASE_PREDICTION_NODE", "/status_prediksi")
    artifacts_dir = get_env_or_default("ML_ARTIFACTS_DIR", os.path.dirname(os.path.abspath(__file__)))

    if not database_url:
        logger.error("Set FIREBASE_DATABASE_URL before running the service.")
        sys.exit(1)

    try:
        run_service(
            service_account_path=service_account_path,
            database_url=database_url,
            sensor_node=sensor_node,
            prediction_node=prediction_node,
            artifacts_dir=artifacts_dir,
        )
    except Exception as exc:
        logger.exception("Service crashed: %s", exc)
        sys.exit(1)


if __name__ == "__main__":
    main()
