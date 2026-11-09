
@extends('layouts.app')

@section('title', 'Riwayat Scan')
@section('page-title', 'Riwayat Pemindaian')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-1">Riwayat Pemindaian</h4>
    <p class="text-muted small mb-0">
        Catatan seluruh aktivitas pemindaian zona parkir.
    </p>
</div>

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Total Pemindaian</div>
            <div class="stat-value">{{ $totalScans }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Motor Terdeteksi</div>
            <div class="stat-value">{{ $totalMotorcycles }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Rata-rata Waktu Proses</div>
            <div class="stat-value">
                {{ $averageProcessingTime !== null
                    ? number_format($averageProcessingTime, 0) . ' ms'
                    : '-' }}
            </div>
        </div>
    </div>

</div>

<div class="section-card">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="section-title mb-0">Daftar Riwayat</h5>

        <span class="badge bg-primary bg-opacity-10 text-primary">
            {{ $histories->total() }} Data
        </span>
    </div>

    <div class="table-responsive">

        <table class="table table-hover">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Zona</th>
                    <th>Petugas</th>
                    <th>Jumlah Motor</th>
                    <th>Status</th>
                    <th>Waktu Proses</th>
                    <th>Waktu Scan</th>
                </tr>
            </thead>

            <tbody>

                @forelse($histories as $index => $history)

                    @php
                        $colors = [
                            'KOSONG' => 'success',
                            'TERSEDIA' => 'primary',
                            'PENUH' => 'danger',
                        ];

                        $color = $colors[$history->status] ?? 'secondary';
                    @endphp

                    <tr>

                        <td>
                            {{ $histories->firstItem() + $index }}
                        </td>

                        <td class="fw-semibold">
                            {{ $history->zone->name ?? '-' }}
                        </td>

                        <td>
                            {{ $history->user->name ?? '-' }}
                        </td>

                        <td>
                            {{ $history->motor_count ?? '-' }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $color }}">
                                {{ $history->status }}
                            </span>
                        </td>

                        <td>
                            {{ $history->processing_time_ms !== null
                                ? $history->processing_time_ms . ' ms'
                                : '-' }}
                        </td>

                        <td>
                            {{ $history->scanned_at
                                ? $history->scanned_at->format('d/m/Y H:i:s')
                                : '-' }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada riwayat pemindaian.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-3">
        {{ $histories->links() }}
    </div>

</div>

@endsection
