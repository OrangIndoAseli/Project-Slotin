
import cv2
import base64
import numpy as np

from pathlib import Path
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from ultralytics import YOLO

# Lokasi project ML
BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "models" / "yolov8s.pt"

# Inisialisasi aplikasi

app = FastAPI(title="SLOTIN AI Service")

# Load model satu kali ketika server dijalankan
model = YOLO(str(MODEL_PATH))

CONFIDENCE = 0.15
IOU = 0.60
IMAGE_SIZE = 1280

# Kelas motorcycle pada model COCO pretrained
MOTOR_CLASS = 3


@app.get("/")
def home():
    return {
        "application": "SLOTIN AI",
        "status": "running",
        "model": MODEL_PATH.name
    }


@app.websocket("/ws/camera")
async def camera_stream(websocket: WebSocket):

    await websocket.accept()

    try:
        while True:

            # Menerima frame JPEG dari browser
            frame_bytes = await websocket.receive_bytes()

            # Decode JPEG menjadi frame OpenCV
            array = np.frombuffer(frame_bytes, np.uint8)
            frame = cv2.imdecode(array, cv2.IMREAD_COLOR)

            if frame is None:
                continue

            # Deteksi dan tracking motor
            results = model.track(
                frame,
                persist=True,
                tracker="bytetrack.yaml",
                classes=[MOTOR_CLASS],
                conf=CONFIDENCE,
                iou=IOU,
                imgsz=IMAGE_SIZE,
                verbose=False
            )

            result = results[0]

            # Jumlah motor yang terdeteksi pada frame saat ini
            motor_count = 0

            if result.boxes is not None and len(result.boxes) > 0:

                motor_count = len(result.boxes)

                # Gambar bounding box dan ID tracking
                annotated_frame = result.plot()

            else:
                annotated_frame = frame

            # Encode hasil deteksi menjadi JPEG
            success, encoded_frame = cv2.imencode(
                ".jpg",
                annotated_frame,
                [cv2.IMWRITE_JPEG_QUALITY, 75]
            )

            if not success:
                continue

            # Kirim hasil ke browser
            frame_base64 = base64.b64encode(
                encoded_frame.tobytes()
            ).decode("utf-8")

            await websocket.send_json({
                "motor_count": motor_count,
                "frame": frame_base64
            })

    except WebSocketDisconnect:
        print("Kamera terputus.")

    except Exception as error:
        print(f"Camera error: {error}")

    finally:
        print("Sesi kamera selesai.")
