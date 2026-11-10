
@extends('layouts.app')

@section('title', 'Scan Parkir')
@section('page-title', 'Scan Parkir')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">Pemindaian Area Parkir</h4>
    <p class="text-muted small mb-0">
        Pantau jumlah kendaraan secara real-time menggunakan AI YOLO.
    </p>
</div>

<div class="row g-4">

    <div class="col-lg-8">
        <div class="section-card">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Kamera Pemindaian</h5>
                <span class="badge bg-secondary" id="cameraStatus">
                    Kamera belum aktif
                </span>
            </div>

            <div class="mb-3">
                <label for="zoneSelect" class="form-label fw-semibold">
                    Pilih Zona Parkir
                </label>

                <select id="zoneSelect" class="form-select">
                    <option value="">-- Pilih Zona --</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}">
                            {{ $zone->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="bg-dark rounded-3 overflow-hidden position-relative"
                 style="min-height:350px;">

            <video id="cameraPreview"
                autoplay
                playsinline
                muted
                class="w-100 d-none"
                style="max-height:500px; object-fit:contain;">
            </video>

            <img id="aiPreview"
                class="w-100 d-none"
                alt="Hasil deteksi YOLO"
                style="max-height:500px; object-fit:contain;">

                <div id="cameraPlaceholder"
                     class="d-flex flex-column justify-content-center align-items-center text-white"
                     style="min-height:350px;">

                    <i class="bi bi-camera-video fs-1 mb-3"></i>

                    <h6>Kamera Belum Aktif</h6>

                    <p class="small text-white-50 mb-0">
                        Pilih zona kemudian mulai pemindaian.
                    </p>
                </div>

                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge bg-danger d-none" id="liveBadge">
                        LIVE YOLO
                    </span>
                </div>

            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">

                <button type="button"
                        id="startCamera"
                        class="btn btn-primary-custom">
                    <i class="bi bi-camera-video me-1"></i>
                    Mulai Pemindaian
                </button>

                <button type="button"
                        id="stopCamera"
                        class="btn btn-outline-danger"
                        disabled>
                    <i class="bi bi-stop-circle me-1"></i>
                    Hentikan
                </button>

            </div>

            <div id="cameraMessage"
                 class="alert alert-info mt-3 mb-0 d-none">
            </div>

        </div>
    </div>

    <div class="col-lg-4">
        <div class="section-card">

            <h5 class="section-title">Hasil Deteksi AI</h5>

            <div class="border rounded-3 p-3 text-center mb-3">
                <div class="text-muted small mb-1">
                    Jumlah Motor Terdeteksi
                </div>

                <div class="fw-bold text-primary"
                     style="font-size:3rem;"
                     id="motorCount">
                    0
                </div>

                <div class="small text-muted">
                    Kendaraan pada frame saat ini
                </div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Zona Terpilih</div>
                <div class="fw-semibold" id="selectedZone">
                    Belum dipilih
                </div>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Status Koneksi AI</div>
                <span class="badge bg-secondary" id="aiStatus">
                    Tidak terhubung
                </span>
            </div>

            <div class="mb-3">
                <div class="small text-muted mb-1">Status Pemindaian</div>
                <span class="badge bg-secondary" id="scanStatus">
                    Belum dimulai
                </span>
            </div>

            <div class="border rounded-3 p-3">
                <div class="small text-muted mb-1">
                    Informasi
                </div>

                <p class="small mb-0">
                    Sistem mengirim frame kamera secara berkala ke FastAPI.
                    YOLO memproses setiap frame dan mengembalikan hasil
                    deteksi beserta bounding box.
                </p>
            </div>

        </div>
    </div>

</div>

<canvas id="captureCanvas" class="d-none"></canvas>

@endsection

@push('scripts')
<script>
    // ==========================================
    // KONFIGURASI
    // ==========================================

    const AI_WS_URL = @json(config(
        'services.slotin_ai.ws_url',
        (request()->secure() ? 'wss://' : 'ws://')
            . request()->getHost()
            . ':8001/ws/camera'
    ));

    const video = document.getElementById('cameraPreview');
    const aiPreview = document.getElementById('aiPreview');
    const canvas = document.getElementById('captureCanvas');

    const startButton = document.getElementById('startCamera');
    const stopButton = document.getElementById('stopCamera');

    const cameraStatus = document.getElementById('cameraStatus');
    const aiStatus = document.getElementById('aiStatus');
    const scanStatus = document.getElementById('scanStatus');

    const cameraPlaceholder = document.getElementById('cameraPlaceholder');
    const cameraMessage = document.getElementById('cameraMessage');

    const zoneSelect = document.getElementById('zoneSelect');
    const selectedZone = document.getElementById('selectedZone');

    const motorCount = document.getElementById('motorCount');
    const liveBadge = document.getElementById('liveBadge');

    let cameraStream = null;
    let socket = null;
    let sendInterval = null;
    let isProcessing = false;
    let isStopping = false;

    // ==========================================
    // PESAN
    // ==========================================

    function showMessage(message, type = 'info') {
        cameraMessage.textContent = message;
        cameraMessage.className =
            `alert alert-${type} mt-3 mb-0`;
    }

    function setBadge(element, message, color) {
        element.textContent = message;
        element.className = `badge bg-${color}`;
    }

    // ==========================================
    // PILIH ZONA
    // ==========================================

    zoneSelect.addEventListener('change', function () {
        selectedZone.textContent = this.value
            ? this.options[this.selectedIndex].text
            : 'Belum dipilih';
    });

    // ==========================================
    // KONEKSI WEBSOCKET
    // ==========================================

    function connectAI() {
        return new Promise((resolve, reject) => {

            socket = new WebSocket(AI_WS_URL);

            socket.binaryType = 'arraybuffer';

            socket.onopen = function () {
                setBadge(aiStatus, 'Terhubung', 'success');
                resolve();
            };

            socket.onmessage = function (event) {

                isProcessing = false;

                try {
                    const result = JSON.parse(event.data);

                    motorCount.textContent =
                        result.motor_count ?? 0;

                    if (result.frame) {
                    aiPreview.src = 'data:image/jpeg;base64,' + result.frame;

                    aiPreview.classList.remove('d-none');
                    video.classList.add('d-none');
                    cameraPlaceholder.classList.add('d-none');
                    }

                } catch (error) {
                    console.error('Kesalahan respons AI:', error);
                }
            };

            socket.onerror = function (error) {
                console.error('WebSocket error:', error);

                if (socket.readyState !== WebSocket.OPEN) {
                    reject(new Error('Tidak dapat terhubung ke FastAPI.'));
                }
            };

            socket.onclose = function () {
                setBadge(aiStatus, 'Terputus', 'danger');

                if (!isStopping) {
                    showMessage(
                        'Koneksi AI terputus. Hentikan pemindaian dan coba kembali.',
                        'danger'
                    );
                    stopCamera();
                }
            };

        });
    }

    // ==========================================
    // KIRIM FRAME KE YOLO
    // ==========================================

    function sendFrame() {

        if (!cameraStream ||
            !socket ||
            socket.readyState !== WebSocket.OPEN ||
            isProcessing) {
            return;
        }

        if (!video.videoWidth || !video.videoHeight) {
            return;
        }

        isProcessing = true;

        // Resolusi dikurangi agar streaming lebih ringan.
        const maxWidth = 640;

        const scale = Math.min(
            1,
            maxWidth / video.videoWidth
        );

        canvas.width = Math.round(video.videoWidth * scale);
        canvas.height = Math.round(video.videoHeight * scale);

        const context = canvas.getContext('2d');

        context.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        canvas.toBlob(async function (blob) {

            if (!blob ||
                !socket ||
                socket.readyState !== WebSocket.OPEN) {

                isProcessing = false;
                return;
            }

            try {
                const frameData = await blob.arrayBuffer();

                if (socket.readyState === WebSocket.OPEN) {
                    socket.send(frameData);
                } else {
                    isProcessing = false;
                }

            } catch (error) {
                console.error('Gagal mengirim frame:', error);
                isProcessing = false;
            }

        }, 'image/jpeg', 0.7);
    }

    // ==========================================
    // MULAI KAMERA
    // ==========================================

    async function startCamera() {

        if (!zoneSelect.value) {
            showMessage(
                'Silakan pilih zona parkir terlebih dahulu.',
                'warning'
            );
            return;
        }

        if (!navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia) {

            showMessage(
                'Browser tidak mendukung kamera. Gunakan HTTPS atau localhost.',
                'danger'
            );
            return;
        }

        startButton.disabled = true;

        try {

            showMessage('Mengaktifkan kamera...', 'info');

            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            });

            video.srcObject = cameraStream;

            await video.play();

            setBadge(cameraStatus, 'Kamera aktif', 'success');

            try {
                await connectAI();
            } catch (error) {
                throw error;
            }

            zoneSelect.disabled = true;

            video.classList.remove('d-none');

            liveBadge.classList.remove('d-none');

            scanStatus.textContent = 'Sedang memindai';
            scanStatus.className = 'badge bg-success';

            stopButton.disabled = false;

            showMessage(
                'Kamera terhubung ke YOLO. Deteksi kendaraan sedang berjalan.',
                'success'
            );

            // Kirim frame secara berkala.
            // Frame berikutnya menunggu respons frame sebelumnya.
            sendInterval = setInterval(sendFrame, 100);

        } catch (error) {

            console.error(error);

            let message = 'Gagal mengaktifkan kamera atau koneksi AI.';

            if (error.name === 'NotAllowedError') {
                message = 'Izin kamera ditolak. Izinkan akses kamera.';
            } else if (error.name === 'NotFoundError') {
                message = 'Kamera tidak ditemukan.';
            } else if (error.message) {
                message = error.message;
            }

            showMessage(message, 'danger');

            stopCamera();

            startButton.disabled = false;
        }
    }

    // ==========================================
    // HENTIKAN KAMERA
    // ==========================================

    function stopCamera() {

        isStopping = true;

        if (sendInterval) {
            clearInterval(sendInterval);
            sendInterval = null;
        }

        if (socket) {
            if (socket.readyState === WebSocket.OPEN ||
                socket.readyState === WebSocket.CONNECTING) {
                socket.close();
            }

            socket = null;
        }

        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        video.srcObject = null;

        video.classList.add('d-none');
        aiPreview.classList.add('d-none');

        cameraPlaceholder.classList.remove('d-none');

        liveBadge.classList.add('d-none');

        setBadge(cameraStatus, 'Kamera belum aktif', 'secondary');
        setBadge(aiStatus, 'Tidak terhubung', 'secondary');
        setBadge(scanStatus, 'Belum dimulai', 'secondary');

        motorCount.textContent = '0';

        startButton.disabled = false;
        stopButton.disabled = true;

        zoneSelect.disabled = false;

        isProcessing = false;
        isStopping = false;
    }

    // ==========================================
    // EVENT
    // ==========================================

    startButton.addEventListener('click', startCamera);
    stopButton.addEventListener('click', stopCamera);

    window.addEventListener('beforeunload', function () {

        if (sendInterval) {
            clearInterval(sendInterval);
        }

        if (socket) {
            socket.close();
        }

        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }

    });

</script>
@endpush
