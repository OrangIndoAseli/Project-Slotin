
@extends('layouts.app')

@section('title', 'Scan Parkir')
@section('page-title', 'Scan Parkir')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">Pemindaian Area Parkir</h4>
    <p class="text-muted small mb-0">
        Gunakan kamera petugas untuk memindai jumlah kendaraan pada zona parkir.
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
                 style="min-height: 350px;">

                <video id="cameraPreview"
                       autoplay
                       playsinline
                       muted
                       class="w-100 d-none"
                       style="max-height: 500px; object-fit: contain;">
                </video>

                <div id="cameraPlaceholder"
                     class="d-flex flex-column justify-content-center align-items-center text-white"
                     style="min-height: 350px;">

                    <i class="bi bi-camera-video fs-1 mb-3"></i>

                    <h6>Kamera Belum Aktif</h6>

                    <p class="small text-white-50 mb-0">
                        Tekan tombol mulai kamera untuk membuka kamera perangkat.
                    </p>

                </div>

            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">

                <button type="button"
                        id="startCamera"
                        class="btn btn-primary-custom">
                    <i class="bi bi-camera-video me-1"></i>
                    Mulai Kamera
                </button>

                <button type="button"
                        id="captureImage"
                        class="btn btn-success"
                        disabled>
                    <i class="bi bi-camera me-1"></i>
                    Ambil Gambar
                </button>

                <button type="button"
                        id="stopCamera"
                        class="btn btn-outline-danger"
                        disabled>
                    <i class="bi bi-stop-circle me-1"></i>
                    Matikan Kamera
                </button>

            </div>

            <div id="cameraMessage" class="alert alert-info mt-3 mb-0 d-none">
            </div>

        </div>
    </div>

    <div class="col-lg-4">
        <div class="section-card">

            <h5 class="section-title">Hasil Pengambilan Gambar</h5>

            <div id="capturePlaceholder"
                 class="border rounded-3 d-flex flex-column justify-content-center align-items-center text-muted"
                 style="height: 230px;">

                <i class="bi bi-image fs-1 mb-2"></i>
                <span class="small">Belum ada gambar</span>

            </div>

            <img id="capturedImage"
                 class="img-fluid rounded-3 border d-none"
                 alt="Hasil tangkapan kamera">

            <canvas id="captureCanvas" class="d-none"></canvas>

            <div class="mt-3">
                <div class="small text-muted mb-1">Zona Terpilih</div>
                <div class="fw-semibold" id="selectedZone">
                    Belum dipilih
                </div>
            </div>

            <div class="mt-3">
                <div class="small text-muted mb-1">Status Gambar</div>
                <span class="badge bg-secondary" id="captureStatus">
                    Belum mengambil gambar
                </span>
            </div>

            <button type="button"
                    id="resetCapture"
                    class="btn btn-outline-secondary w-100 mt-4"
                    disabled>
                <i class="bi bi-arrow-counterclockwise me-1"></i>
                Ulangi Pengambilan
            </button>

            <p class="text-muted small mt-3 mb-0">
                Catatan: Hasil tangkapan saat ini hanya ditampilkan di browser.
                Pengiriman ke sistem YOLO akan ditambahkan pada tahap integrasi.
            </p>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('captureCanvas');
    const capturedImage = document.getElementById('capturedImage');

    const startButton = document.getElementById('startCamera');
    const captureButton = document.getElementById('captureImage');
    const stopButton = document.getElementById('stopCamera');
    const resetButton = document.getElementById('resetCapture');

    const cameraStatus = document.getElementById('cameraStatus');
    const captureStatus = document.getElementById('captureStatus');

    const cameraPlaceholder = document.getElementById('cameraPlaceholder');
    const capturePlaceholder = document.getElementById('capturePlaceholder');

    const cameraMessage = document.getElementById('cameraMessage');
    const zoneSelect = document.getElementById('zoneSelect');
    const selectedZone = document.getElementById('selectedZone');

    let cameraStream = null;

    zoneSelect.addEventListener('change', function () {
        selectedZone.textContent = this.value
            ? this.options[this.selectedIndex].text
            : 'Belum dipilih';
    });

    function showMessage(message, type = 'info') {
        cameraMessage.textContent = message;
        cameraMessage.className = `alert alert-${type} mt-3 mb-0`;
    }

    async function startCamera() {
        if (!zoneSelect.value) {
            showMessage('Silakan pilih zona parkir terlebih dahulu.', 'warning');
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showMessage(
                'Browser tidak mendukung akses kamera atau halaman belum menggunakan koneksi aman.',
                'danger'
            );
            return;
        }

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' }
                },
                audio: false
            });

            video.srcObject = cameraStream;

            video.classList.remove('d-none');
            cameraPlaceholder.classList.add('d-none');

            cameraStatus.textContent = 'Kamera aktif';
            cameraStatus.className = 'badge bg-success';

            startButton.disabled = true;
            captureButton.disabled = false;
            stopButton.disabled = false;

            showMessage('Kamera berhasil diaktifkan.', 'success');

        } catch (error) {
            console.error(error);

            let message = 'Tidak dapat mengakses kamera.';

            if (error.name === 'NotAllowedError') {
                message = 'Izin kamera ditolak. Izinkan akses kamera melalui browser.';
            } else if (error.name === 'NotFoundError') {
                message = 'Perangkat kamera tidak ditemukan.';
            }

            showMessage(message, 'danger');
        }
    }

    function captureFrame() {
        if (!cameraStream || !zoneSelect.value) {
            showMessage('Pilih zona dan aktifkan kamera terlebih dahulu.', 'warning');
            return;
        }

        if (!video.videoWidth || !video.videoHeight) {
            showMessage('Kamera belum siap. Coba beberapa saat lagi.', 'warning');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL('image/jpeg', 0.9);

        capturedImage.src = imageData;
        capturedImage.classList.remove('d-none');
        capturePlaceholder.classList.add('d-none');

        captureStatus.textContent = 'Gambar berhasil diambil';
        captureStatus.className = 'badge bg-success';

        resetButton.disabled = false;

        showMessage('Gambar berhasil diambil.', 'success');
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        video.srcObject = null;
        video.classList.add('d-none');
        cameraPlaceholder.classList.remove('d-none');

        cameraStatus.textContent = 'Kamera belum aktif';
        cameraStatus.className = 'badge bg-secondary';

        startButton.disabled = false;
        captureButton.disabled = true;
        stopButton.disabled = true;
    }

    function resetCapture() {
        capturedImage.src = '';
        capturedImage.classList.add('d-none');
        capturePlaceholder.classList.remove('d-none');

        captureStatus.textContent = 'Belum mengambil gambar';
        captureStatus.className = 'badge bg-secondary';

        resetButton.disabled = true;

        showMessage('Gambar berhasil direset.', 'info');
    }

    startButton.addEventListener('click', startCamera);
    captureButton.addEventListener('click', captureFrame);
    stopButton.addEventListener('click', stopCamera);
    resetButton.addEventListener('click', resetCapture);

    window.addEventListener('beforeunload', function () {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }
    });
</script>
@endpush
