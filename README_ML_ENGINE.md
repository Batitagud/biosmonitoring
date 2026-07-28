# Panduan Menjalankan ML Engine dengan Firebase

## 1. Instal dependensi di Windows

Buka terminal dan jalankan:

```powershell
py -m pip install --upgrade pip
py -m pip install firebase-admin numpy joblib scikit-learn
```

Jika Anda ingin menggunakan pandas juga, Anda bisa menambahkan:

```powershell
py -m pip install pandas
```

## 2. Siapkan file service account Firebase

1. Buka Firebase Console.
2. Buka Project Settings > Service accounts.
3. Generate private key dan simpan file JSON.
4. Simpan file tersebut di folder proyek, misalnya:
   - `firebase-service-account.json`

## 3. Siapkan model ML

Letakkan model Anda di folder proyek, misalnya:
- `model.joblib`
atau
- `model.pkl`

## 4. Set environment variables di Windows

Di PowerShell:

```powershell
$env:FIREBASE_SERVICE_ACCOUNT = "D:\laragon\www\DashboardBioSensor\firebase-service-account.json"
$env:FIREBASE_DATABASE_URL = "https://<project-id>-default-rtdb.firebaseio.com/"
$env:FIREBASE_SENSOR_NODE = "/sensor_data"
$env:FIREBASE_PREDICTION_NODE = "/status_prediksi"
$env:ML_MODEL_PATH = "D:\laragon\www\DashboardBioSensor\model.joblib"
```

## 5. Jalankan service

```powershell
py ml_engine.py
```

Skrip akan terus berjalan hingga Anda tekan Ctrl+C.

## 6. Format data yang diterima dari Firebase

Skrip mengharapkan data di node `/sensor_data` memiliki format seperti:

```json
{
  "gsr_raw": 12.5,
  "suhu_kulit": 32.1,
  "bpm": 78,
  "raw_ibi": 0.9
}
```

## 7. Catatan penting

- Skrip hanya memproses setelah mengumpulkan 30 sampel sensor.
- Setelah 30 sampel terkumpul, fitur yang dibentuk adalah 16 nilai:
  - mean, std, min, max untuk masing-masing 4 variabel.
- Hasil prediksi akan ditulis ke node `/status_prediksi` dengan timestamp.
