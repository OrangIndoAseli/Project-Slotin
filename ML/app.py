
import cv2
import base64
import numpy as np
import httpx
import time

from pathlib import Path
from fastapi import FastAPI, WebSocket, WebSocketDisconnect
from fastapi.responses import HTMLResponse
from ultralytics import YOLO

# ==========================================
# KONFIGURASI
# ==========================================

BASE_DIR = Path(__file__).resolve().parent
MODEL_PATH = BASE_DIR / "models" / "yolov8s.pt"

LARAVEL_API_URL = "http://127.0.0.1:8000/api/zone-detection"

CONFIDENCE = 0.15
IOU = 0.60
IMAGE_SIZE = 1280

MOTOR_CLASS = 3
DATABASE_UPDATE_INTERVAL = 5

app = FastAPI(title="SLOTIN AI Service")

model = YOLO(str(MODEL_PATH))


# ==========================================
# HALAMAN CAMERA STATION
# ==========================================

@app.get("/", response_class=HTMLResponse)
def camera_station():

    return """
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, initial-scale=1.0">

        <title>SLOTIN Camera Station</title>

        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: Arial, sans-serif;
                background: #101827;
                color: white;
                padding: 20px;
            }

            .container {
                max-width: 500px;
                margin: auto;
            }

            .header {
                margin-bottom: 20px;
            }

            .header h2 {
                color: #ff8c32;
                margin-bottom: 5px;
            }

            .header p {
                color: #aab4c3;
                font-size: 13px;
            }

            .status {
                background: #263449;
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 15px;
                font-size: 13px;
            }

            .camera-box {
                background: #263449;
                border-radius: 14px;
                overflow: hidden;
                margin-bottom: 15px;
                min-height: 220px;
            }

            video, #aiPreview {
                width: 100%;
                display: block;
                background: #000;
                aspect-ratio: 4 / 3;
                object-fit: contain;
            }

            #aiPreview {
                display: none;
            }

            .section {
                margin-bottom: 15px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-size: 14px;
            }

            select {
                width: 100%;
                padding: 13px;
                border-radius: 9px;
                border: none;
                background: #263449;
                color: white;
                font-size: 15px;
            }

            .stats {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 18px;
            }

            .stat-card {
                background: #263449;
                padding: 18px;
                border-radius: 12px;
            }

            .stat-card h3 {
                font-size: 28px;
                margin-bottom: 5px;
                color: #ff8c32;
            }

            .stat-card p {
                font-size: 12px;
                color: #aab4c3;
            }

            button {
                width: 100%;
                padding: 15px;
                border: none;
                border-radius: 10px;
                font-size: 15px;
                font-weight: bold;
                cursor: pointer;
                margin-bottom: 10px;
            }

            #startButton {
                background: #f87b1b;
                color: white;
            }

            #stopButton {
                background: #b31312;
                color: white;
                display: none;
            }

            button:disabled {
                opacity: 0.5;
            }

            .footer {
                text-align: center;
                color: #738196;
                font-size: 11px;
                margin-top: 15px;
            }
        </style>
    </head>

    <body>

        <div class="container">

            <div class="header">
                <h2>SLOTIN CAMERA</h2>
                <p>Smart Parking Monitoring System</p>
            </div>

            <div class="status" id="connectionStatus">
                Status: Belum terhubung
            </div>

            <div class="camera-box">

                <video id="cameraPreview"
                       autoplay
                       playsinline
                       muted>
                </video>

                <img id="aiPreview" alt="YOLO Detection Preview">

            </div>

            <div class="section">

                <label>Pilih Zona Parkir</label>

                <select id="zoneSelect">
                    <option value="1">Zona A</option>
                    <option value="2">Zona B</option>
                    <option value="3">Zona C</option>
                    <option value="4">Zona D</option>
                </select>

            </div>

            <div class="stats">

                <div class="stat-card">
                    <h3 id="motorCount">0</h3>
                    <p>Motor Terdeteksi</p>
                </div>

                <div class="stat-card">
                    <h3 id="processingTime">--</h3>
                    <p>Processing Time (ms)</p>
                </div>

            </div>

            <button id="startButton">
                MULAI KAMERA
            </button>

            <button id="stopButton">
                BERHENTI
            </button>

            <div class="footer">
                SLOTIN AI Camera Station
            </div>

        </div>

        <script>

            const video = document.getElementById("cameraPreview");
            const aiPreview = document.getElementById("aiPreview");

            const zoneSelect = document.getElementById("zoneSelect");

            const startButton = document.getElementById("startButton");
            const stopButton = document.getElementById("stopButton");

            const connectionStatus = document.getElementById("connectionStatus");

            const motorCount = document.getElementById("motorCount");
            const processingTime = document.getElementById("processingTime");

            let stream = null;
            let socket = null;
            let captureInterval = null;

            let canvas = document.createElement("canvas");
            let context = canvas.getContext("2d");

            function updateStatus(message) {
                connectionStatus.textContent = "Status: " + message;
            }

            async function startCamera() {

                const zoneId = zoneSelect.value;

                if (!zoneId) {
                    alert("Pilih zona parkir terlebih dahulu.");
                    return;
                }

                startButton.disabled = true;

                try {

                    if (!navigator.mediaDevices ||
                        !navigator.mediaDevices.getUserMedia) {

                        throw new Error(
                            "Browser tidak mendukung kamera atau halaman belum menggunakan HTTPS."
                        );
                    }

                    updateStatus("Meminta izin kamera...");

                    stream = await navigator.mediaDevices.getUserMedia({
                        audio: false,
                        video: {
                            facingMode: "environment",
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        }
                    });

                    video.srcObject = stream;

                    await video.play();

                    const protocol =
                        window.location.protocol === "https:"
                            ? "wss:"
                            : "ws:";

                    const wsUrl =
                        protocol +
                        "//" +
                        window.location.host +
                        "/ws/camera?zone_id=" +
                        encodeURIComponent(zoneId);

                    updateStatus("Menghubungkan ke SLOTIN AI...");

                    socket = new WebSocket(wsUrl);

                    socket.onopen = function() {

                        updateStatus("Kamera terhubung ke YOLO");

                        startButton.style.display = "none";
                        stopButton.style.display = "block";

                        zoneSelect.disabled = true;

                        // Kirim frame setiap 500 ms
                        captureInterval = setInterval(sendFrame, 500);
                    };

                    socket.onmessage = function(event) {

                        const data = JSON.parse(event.data);

                        if (data.frame) {

                            aiPreview.src =
                                "data:image/jpeg;base64," + data.frame;

                            aiPreview.style.display = "block";
                            video.style.display = "none";
                        }

                        if (data.motor_count !== undefined) {
                            motorCount.textContent = data.motor_count;
                        }

                        if (data.processing_time_ms !== undefined) {
                            processingTime.textContent =
                                data.processing_time_ms;
                        }

                    };

                    socket.onerror = function() {
                        updateStatus("Terjadi kesalahan WebSocket");
                    };

                    socket.onclose = function() {
                        updateStatus("Koneksi terputus");
                    };

                } catch (error) {

                    updateStatus(error.message);

                    alert(error.message);

                    stopCamera();
                }
            }

            function sendFrame() {

                if (!stream ||
                    !socket ||
                    socket.readyState !== WebSocket.OPEN) {
                    return;
                }

                if (video.readyState < 2) {
                    return;
                }

                // Hindari antrean frame yang terlalu banyak
                if (socket.bufferedAmount > 1000000) {
                    return;
                }

                canvas.width = 640;
                canvas.height = 480;

                context.drawImage(
                    video,
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                canvas.toBlob(function(blob) {

                    if (!blob) return;

                    if (socket &&
                        socket.readyState === WebSocket.OPEN) {

                        socket.send(blob);
                    }

                }, "image/jpeg", 0.75);
            }

            function stopCamera() {

                if (captureInterval) {
                    clearInterval(captureInterval);
                    captureInterval = null;
                }

                if (socket) {
                    socket.close();
                    socket = null;
                }

                if (stream) {

                    stream.getTracks().forEach(function(track) {
                        track.stop();
                    });

                    stream = null;
                }

                video.srcObject = null;

                video.style.display = "block";
                aiPreview.style.display = "none";

                motorCount.textContent = "0";
                processingTime.textContent = "--";

                startButton.disabled = false;
                startButton.style.display = "block";

                stopButton.style.display = "none";

                zoneSelect.disabled = false;

                updateStatus("Kamera berhenti");
            }

            startButton.addEventListener("click", startCamera);
            stopButton.addEventListener("click", stopCamera);

            window.addEventListener("beforeunload", function() {
                stopCamera();
            });

        </script>

    </body>
    </html>
    """


# ==========================================
# API STATUS
# ==========================================

@app.get("/health")
def health():

    return {
        "application": "SLOTIN AI",
        "status": "running",
        "model": MODEL_PATH.name
    }


# ==========================================
# WEBSOCKET CAMERA + YOLO
# ==========================================

@app.websocket("/ws/camera")
async def camera_stream(websocket: WebSocket):

    await websocket.accept()

    zone_id = websocket.query_params.get("zone_id")

    try:
        zone_id = int(zone_id)

        if zone_id <= 0:
            raise ValueError()

    except (TypeError, ValueError):

        await websocket.send_json({
            "success": False,
            "message": "Zone ID tidak valid."
        })

        await websocket.close(code=1008)
        return

    print(f"Kamera terhubung ke Zona ID: {zone_id}")

    last_database_update = 0

    async with httpx.AsyncClient(timeout=5.0) as client:

        try:

            while True:

                frame_bytes = await websocket.receive_bytes()

                array = np.frombuffer(frame_bytes, np.uint8)

                frame = cv2.imdecode(
                    array,
                    cv2.IMREAD_COLOR
                )

                if frame is None:
                    continue

                start_time = time.perf_counter()

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

                processing_time = (
                    time.perf_counter() - start_time
                ) * 1000

                result = results[0]

                motor_count = (
                    len(result.boxes)
                    if result.boxes is not None
                    else 0
                )

                annotated_frame = result.plot()

                current_time = time.monotonic()

                # Update database setiap 5 detik
                if (
                    current_time - last_database_update
                    >= DATABASE_UPDATE_INTERVAL
                ):

                    # Batasi percobaan agar tidak mengirim
                    # request berulang pada setiap frame
                    last_database_update = current_time

                    payload = {
                        "zone_id": zone_id,
                        "motor_count": motor_count,
                        "processing_time_ms": round(
                            processing_time, 2
                        )
                    }

                    try:

                        response = await client.post(
                            LARAVEL_API_URL,
                            json=payload
                        )

                        response.raise_for_status()

                        print(
                            f"Zona {zone_id} | "
                            f"Motor: {motor_count} | "
                            f"Database berhasil diperbarui"
                        )

                    except httpx.HTTPError as error:

                        print(
                            f"Gagal mengirim data ke Laravel: {error}"
                        )

                success, encoded_frame = cv2.imencode(
                    ".jpg",
                    annotated_frame,
                    [cv2.IMWRITE_JPEG_QUALITY, 75]
                )

                if not success:
                    continue

                frame_base64 = base64.b64encode(
                    encoded_frame.tobytes()
                ).decode("utf-8")

                await websocket.send_json({

                    "zone_id": zone_id,

                    "motor_count": motor_count,

                    "processing_time_ms": round(
                        processing_time, 2
                    ),

                    "frame": frame_base64

                })

        except WebSocketDisconnect:

            print(
                f"Kamera Zona {zone_id} terputus."
            )

        except Exception as error:

            print(f"Camera error: {error}")

        finally:

            print(
                f"Sesi kamera Zona {zone_id} selesai."
            )
