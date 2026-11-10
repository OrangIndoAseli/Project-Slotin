
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- HEADER DASHBOARD --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #11224e;">
            Dashboard Overview
        </h4>
        <p class="text-muted mb-0">
            Ringkasan kondisi parkir SLOTIN secara real-time.
        </p>
    </div>

    <a href="{{ route('scan.index') }}"
       class="btn text-white mt-3 mt-md-0"
       style="background: #f87b1b;">
        <i class="bi bi-camera-video me-2"></i>
        Mulai Scan
    </a>
</div>


{{-- STATISTIC CARDS --}}
<div class="row g-3 mb-4">

    {{-- TOTAL ZONA --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-2">Total Zona</p>
                        <h3 class="fw-bold mb-0">
                            {{ $totalZones }}
                        </h3>
                    </div>

                    <div class="rounded-3 p-3"
                         style="background: #e8edff; color: #263b75;">
                        <i class="bi bi-geo-alt fs-3"></i>
                    </div>
                </div>

                <small class="text-muted mt-3 d-block">
                    Zona parkir terdaftar
                </small>
            </div>
        </div>
    </div>


    {{-- TOTAL MOTOR --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-2">Motor Terdeteksi</p>
                        <h3 class="fw-bold mb-0">
                            {{ $totalMotorcycles }}
                        </h3>
                    </div>

                    <div class="rounded-3 p-3"
                         style="background: #fff0e5; color: #f87b1b;">
                        <i class="bi bi-bicycle fs-3"></i>
                    </div>
                </div>

                <small class="text-muted mt-3 d-block">
                    Total motor pada zona terpantau
                </small>
            </div>
        </div>
    </div>


    {{-- KAPASITAS TERSEDIA --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-2">Slot Tersedia</p>
                        <h3 class="fw-bold mb-0">
                            {{ $availableCapacity }}
                        </h3>
                    </div>

                    <div class="rounded-3 p-3"
                         style="background: #e6f7ed; color: #198754;">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                </div>

                <small class="text-muted mt-3 d-block">
                    Sisa kapasitas zona terpantau
                </small>
            </div>
        </div>
    </div>


    {{-- ZONA TIDAK TERPANTAU --}}
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-2">Belum Terpantau</p>
                        <h3 class="fw-bold mb-0">
                            {{ $unmonitoredZones }}
                        </h3>
                    </div>

                    <div class="rounded-3 p-3"
                         style="background: #f1f1f1; color: #6c757d;">
                        <i class="bi bi-camera-video-off fs-3"></i>
                    </div>
                </div>

                <small class="text-muted mt-3 d-block">
                    Zona belum menerima hasil scan
                </small>
            </div>
        </div>
    </div>

</div>


{{-- STATUS ZONA --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 px-4 pt-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1" style="color: #11224e;">
                    Status Zona Parkir
                </h5>
                <p class="text-muted small mb-0">
                    Informasi kapasitas dan kondisi setiap zona.
                </p>
            </div>

            <a href="{{ route('zones.index') }}"
               class="text-decoration-none small fw-semibold"
               style="color: #f87b1b;">
                Lihat Semua Zona
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <div class="card-body px-4 pb-4">

        <div class="row g-3">

            @forelse($zones as $zone)

                @php
                    $isMonitored = !is_null($zone->current_count);

                    $currentCount = $zone->current_count ?? 0;

                    $capacity = $zone->capacity;

                    $percentage = $capacity > 0
                        ? min(100, ($currentCount / $capacity) * 100)
                        : 0;

                    $statusColors = [
                        'KOSONG' => 'success',
                        'TERSEDIA' => 'primary',
                        'PENUH' => 'danger',
                        'TIDAK TERPANTAU' => 'secondary',
                    ];

                    $statusColor = $statusColors[$zone->status] ?? 'secondary';
                @endphp

                <div class="col-12 col-md-6 col-xl-3">

                    <div class="border rounded-3 p-3 h-100">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <div>
                                <h6 class="fw-bold mb-0">
                                    {{ $zone->name }}
                                </h6>

                                <small class="text-muted">
                                    Kapasitas {{ $capacity }} motor
                                </small>
                            </div>

                            <span class="badge bg-{{ $statusColor }}">
                                {{ $zone->status }}
                            </span>

                        </div>


                        @if($isMonitored)

                            <div class="d-flex justify-content-between align-items-end mb-2">

                                <div>
                                    <h3 class="fw-bold mb-0"
                                        style="color: #11224e;">
                                        {{ $currentCount }}
                                    </h3>

                                    <small class="text-muted">
                                        Motor terdeteksi
                                    </small>
                                </div>

                                <small class="text-muted">
                                    {{ $capacity > 0 ? round($percentage) : 0 }}%
                                </small>

                            </div>


                            <div class="progress"
                                 style="height: 8px;">

                                <div class="progress-bar bg-{{ $statusColor }}"
                                     role="progressbar"
                                     style="width: {{ $percentage }}%;"
                                     aria-valuenow="{{ $percentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>

                            </div>

                            <small class="text-muted d-block mt-2">
                                Sisa {{ max(0, $capacity - $currentCount) }} slot
                            </small>

                        @else

                            <div class="text-center py-3">

                                <i class="bi bi-camera-video-off text-secondary fs-2"></i>

                                <p class="text-muted small mt-2 mb-0">
                                    Belum ada data scan
                                </p>

                            </div>

                        @endif


                        <div class="border-top mt-3 pt-2">

                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>

                                @if($zone->last_scanned_at)
                                    {{ \Carbon\Carbon::parse($zone->last_scanned_at)->format('d/m/Y H:i') }}
                                @else
                                    Belum pernah discan
                                @endif
                            </small>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12 text-center py-4">

                    <i class="bi bi-geo-alt text-muted fs-1"></i>

                    <p class="text-muted mt-2 mb-0">
                        Belum ada zona parkir terdaftar.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>


{{-- RINGKASAN STATUS --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>

                <div>
                    <h4 class="fw-bold mb-0">{{ $emptyZones }}</h4>
                    <small class="text-muted">Zona Kosong</small>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-car-front fs-4"></i>
                </div>

                <div>
                    <h4 class="fw-bold mb-0">{{ $availableZones }}</h4>
                    <small class="text-muted">Zona Tersedia</small>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-circle fs-4"></i>
                </div>

                <div>
                    <h4 class="fw-bold mb-0">{{ $fullZones }}</h4>
                    <small class="text-muted">Zona Penuh</small>
                </div>
            </div>
        </div>
    </div>

</div>


{{-- RIWAYAT SCAN TERBARU --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-0 px-4 pt-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center">

            <div>
                <h5 class="fw-bold mb-1" style="color: #11224e;">
                    Riwayat Scan Terbaru
                </h5>

                <p class="text-muted small mb-0">
                    Lima aktivitas pemindaian terakhir.
                </p>
            </div>

            <a href="{{ route('history.index') }}"
               class="text-decoration-none small fw-semibold"
               style="color: #f87b1b;">
                Lihat Semua Riwayat
                <i class="bi bi-arrow-right ms-1"></i>
            </a>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead style="background: #f8f9fc;">

                    <tr>
                        <th class="px-4 py-3">Zona</th>
                        <th class="py-3">Petugas</th>
                        <th class="py-3">Motor Terdeteksi</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Waktu Scan</th>
                        <th class="py-3">Processing</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($recentScans as $scan)

                        @php
                            $historyStatusColors = [
                                'KOSONG' => 'success',
                                'TERSEDIA' => 'primary',
                                'PENUH' => 'danger',
                            ];

                            $historyColor = $historyStatusColors[$scan->status] ?? 'secondary';
                        @endphp

                        <tr>

                            <td class="px-4 fw-semibold">
                                {{ $scan->zone->name ?? 'Zona tidak ditemukan' }}
                            </td>

                            <td>
                                {{ $scan->user->name ?? 'Tidak diketahui' }}
                            </td>

                            <td>
                                <i class="bi bi-bicycle me-1 text-muted"></i>
                                {{ $scan->motor_count }} motor
                            </td>

                            <td>
                                <span class="badge bg-{{ $historyColor }}">
                                    {{ $scan->status }}
                                </span>
                            </td>

                            <td>
                                {{ $scan->scanned_at
                                    ? \Carbon\Carbon::parse($scan->scanned_at)->format('d/m/Y H:i')
                                    : '-' }}
                            </td>

                            <td>
                                {{ $scan->processing_time_ms ?? '-' }}
                                @if(!is_null($scan->processing_time_ms))
                                    ms
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="text-center py-5">

                                <i class="bi bi-clock-history fs-1 text-muted"></i>

                                <p class="text-muted mt-2 mb-0">
                                    Belum ada riwayat pemindaian.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
