"""
===========================================
 Biosensor Monitor Notification Demo
===========================================

Demo untuk presentasi skripsi.
"""

import time
import winsound
from winotify import Notification

APP_NAME = "Biosensor Monitor"


def show_notification(status, confidence):

    if status == "STRESS":

        winsound.PlaySound(
            "SystemExclamation",
            winsound.SND_ALIAS | winsound.SND_ASYNC
        )

        toast = Notification(
            app_id=APP_NAME,
            title="🚨 STRESS DETECTED",
            msg=(
                f"Employee stress detected\n\n"
                f"Confidence : {confidence}%\n\n"
                f"Recommendation:\n"
                f"• Take a short break\n"
                f"• Drink water\n"
                f"• Relax for 5–10 minutes"
            ),
        )

    elif status == "FATIGUE":

        winsound.PlaySound(
            "SystemHand",
            winsound.SND_ALIAS | winsound.SND_ASYNC
        )

        toast = Notification(
            app_id=APP_NAME,
            title="⚠ FATIGUE DETECTED",
            msg=(
                f"Employee fatigue detected\n\n"
                f"Confidence : {confidence}%\n\n"
                f"Recommendation:\n"
                f"• Stop working temporarily\n"
                f"• Recovery is recommended"
            ),
        )

    else:
        return

    toast.show()


print("=" * 45)
print(" BIOSENSOR MONITOR DEMO")
print("=" * 45)

print("\nMonitoring sensor...")
time.sleep(3)

print("Normal Condition")
time.sleep(3)

print("\nMachine Learning Prediction...")
time.sleep(2)

print("Prediction : STRESS")
show_notification("STRESS", "96.82")

time.sleep(8)

print("\nPrediction : FATIGUE")
show_notification("FATIGUE", "98.17")

time.sleep(8)

print("\nDemo Finished.")