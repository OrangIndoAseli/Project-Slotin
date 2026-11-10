
@extends('layouts.app')

@section('title', 'Riwayat Scan')
@section('page-title', 'Riwayat Pemindaian')

@push('styles')
<style>
    /* HEADER */
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .history-subtitle {
        color: #8b95a7;
        font-size: 13px;
        margin-bottom: 0;
    }

    /* STATISTICS */
    .history-stat {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 14px;
        padding: 20px;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: .2s ease;
    }

    .history-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.05);
    }

    .history-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .icon-blue {
        background: #eaf2ff;
        color: #2864dc;
    }

    .icon-orange {
        background: #fff0e5;
        color: #f07820;
    }

    .icon-purple {
        background: #f1eaff;
        color: #8053d6;
    }

    .history-stat-label {
        color: #8993a3;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .history-stat-value {
        color: #17233c;
        font-size: 25px;
        font-weight: 700;
        line-height: 1.2;
    }

    /* HISTORY CARD */
    .history-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 14px;
        overflow: hidden;
    }

    .history-card-header {
        padding: 20px 22px;
        border-bottom: 1px solid #edf0f4;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .history-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #17233c;
        margin: 0;
    }

    .history-card-description {
        color: #8993a3;
        font-size: 12px;
        margin-top: 4px;
        margin-bottom: 0;
    }

    .history-count {
        background: #edf3ff;
        color: #2864dc;
        border-radius: 20px;
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 600;
    }

    /* TABLE */
    .history-table {
        margin-bottom: 0;
        vertical-align: middle;
    }

    .history-table thead th {
        background: #f8f9fc;
        color: #8993a3;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 15px 18px;
        border-bottom: 1px solid #edf0f4;
        white-space: nowrap;
    }

    .history-table tbody td {
        padding: 16px 18px;
        font-size: 13px;
        color: #4d586b;
        border-bottom: 1px solid #f0f2f6;
        white-space: nowrap;
    }

    .history-table tbody tr:last-child td {
        border-bottom: none;
    }

    .history-table tbody tr:hover {
        background: #fafbfe;
    }

    .history-number {
        color: #9aa3b2 !important;
        font-weight: 600;
    }

    .zone-name {
        color: #17233c;
        font-weight: 700;
    }

    .history-user {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .user-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #edf3ff;
        color: #2864dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .motor-value {
        color: #17233c;
        font-weight: 700;
    }

    /* STATUS */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 20px;
        padding: 6px 11px;
        font-size: 11px;
        font-weight: 700;
    }

    .status-empty {
        background: #e9f8ef;
        color: #198754;
    }

    .status-available {
        background: #eaf2ff;
        color: #2864dc;
    }

    .status-full {
        background: #ffeded;
        color: #dc3545;
    }

    .status-unknown {
        background: #f0f1f3;
        color: #6c757d;
    }

    .processing-value {
        font-weight: 600;
        color: #596579;
    }

    .scan-date {
        color: #344054;
        font-weight: 600;
    }

    .scan-time {
        display: block;
        color: #98a1b0;
        font-size: 11px;
        margin-top: 3px;
    }

    /* EMPTY STATE */
    .history-empty {
        padding: 55px 20px !important;
        text-align: center;
        white-space: normal !important;
    }

    .history-empty i {
        font-size: 38px;
        color: #c5ccd6;
        display: block;
        margin-bottom: 12px;
    }

    /* MINIMALIST PAGINATION */
    .history-pagination {
        padding: 12px 20px;
        border-top: 1px solid #edf0f4;
    }

    .history-pagination nav {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .history-pagination .pagination {
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0;
    }

    .history-pagination .page-item {
        margin: 0;
    }

    .history-pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 29px;
        height: 29px;
        padding: 4px 8px;
        border: 1px solid #e5e7eb;
        border-radius: 6px !important;
        background: #fff;
        color: #64748b;
        font-size: 11px;
        line-height: 1;
        box-shadow: none !important;
    }

    /* Previous dan Next */
    .history-pagination .page-item:first-child .page-link,
    .history-pagination .page-item:last-child .page-link {
        min-width: auto;
        padding: 4px 9px;
        font-size: 11px;
    }

    /* Halaman aktif */
    .history-pagination .page-item.active .page-link {
        background: #11224e;
        border-color: #11224e;
        color: #fff;
    }

    /* Disabled */
    .history-pagination .page-item.disabled .page-link {
        background: #f8fafc;
        color: #cbd5e1;
    }

    /* Hover */
    .history-pagination .page-link:hover {
        background: #f1f5f9;
        color: #11224e;
    }

    /* RESPONSIVE */
    @media (max-width: 576px) {
        .history-stat {
            padding: 15px;
        }

        .history-stat-value {
            font-size: 22px;
        }

        .history-card-header {
            padding: 16px;
        }

        .history-pagination {
            padding: 12px;
        }

        .history-pagination nav {
            justify-content: center;
        }

        .history-pagination .page-link {
            min-width: 27px;
            height: 27px;
            padding: 3px 7px;
        }
    }
</style>
@endpush

@section('content')

@php
    $statusClasses = [
        'KOSONG' => 'status-empty',
        'TERSEDIA' => 'status-available',
        'PENUH' => 'status-full',
    ];
@endphp

{{-- HEADER --}}
<div class="history-header">

    <div>
        <h4 class="fw-bold mb-1" style="color: #17233c;">
            Riwayat Pemindaian
        </h4>

        <p class="history-subtitle">
            Catatan seluruh aktivitas pemindaian zona parkir SLOTIN.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
            <i class="bi bi-circle-fill me-1" style="font-size: 7px;"></i>
            Sistem Pemantauan
        </span>
    </div>

</div>

{{-- STATISTICS --}}
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="history-stat">

            <div class="history-stat-icon icon-blue">
                <i class="bi bi-clipboard-data"></i>
            </div>

            <div>
                <div class="history-stat-label">
                    Total Pemindaian
                </div>

                <div class="history-stat-value">
                    {{ number_format($totalScans) }}
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-4">
        <div class="history-stat">

            <div class="history-stat-icon icon-orange">
                <i class="bi bi-bicycle"></i>
            </div>

            <div>
                <div class="history-stat-label">
                    Total Motor Terdeteksi
                </div>

                <div class="history-stat-value">
                    {{ number_format($totalMotorcycles) }}
                </div>
            </div>

        </div>
    </div>

    <div class="col-md-4">
        <div class="history-stat">

            <div class="history-stat-icon icon-purple">
                <i class="bi bi-stopwatch"></i>
            </div>

            <div>
                <div class="history-stat-label">
                    Rata-rata Waktu Proses
                </div>

                <div class="history-stat-value">
                    {{ $averageProcessingTime !== null
                        ? number_format($averageProcessingTime, 0) . ' ms'
                        : '-' }}
                </div>
            </div>

        </div>
    </div>

</div>

{{-- HISTORY TABLE --}}
<div class="history-card">

    <div class="history-card-header">

        <div>
            <h5 class="history-card-title">
                <i class="bi bi-clock-history me-2 text-primary"></i>
                Daftar Riwayat Scan
            </h5>

            <p class="history-card-description">
                Informasi hasil deteksi kendaraan berdasarkan zona parkir.
            </p>
        </div>

        <span class="history-count">
            <i class="bi bi-database me-1"></i>
            {{ number_format($histories->total()) }} Data
        </span>

    </div>

    <div class="table-responsive">

        <table class="table history-table">

            <thead>
                <tr>
                    <th width="60">No.</th>
                    <th>Zona Parkir</th>
                    <th>Petugas</th>
                    <th>Motor Terdeteksi</th>
                    <th>Status Zona</th>
                    <th>Waktu Proses</th>
                    <th>Waktu Pemindaian</th>
                </tr>
            </thead>

            <tbody>

                @forelse($histories as $index => $history)

                    @php
                        $statusClass = $statusClasses[$history->status]
                            ?? 'status-unknown';
                    @endphp

                    <tr>

                        <td class="history-number">
                            {{ $histories->firstItem() + $index }}
                        </td>

                        <td>
                            <span class="zone-name">
                                <i class="bi bi-geo-alt me-1 text-primary"></i>
                                {{ $history->zone->name ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <div class="history-user">

                                <div class="user-avatar">
                                    <i class="bi bi-person"></i>
                                </div>

                                <span>
                                    {{ $history->user->name ?? '-' }}
                                </span>

                            </div>
                        </td>

                        <td>
                            <span class="motor-value">
                                {{ $history->motor_count ?? '-' }}
                            </span>

                            <span class="text-muted small">
                                motor
                            </span>
                        </td>

                        <td>
                            <span class="status-badge {{ $statusClass }}">

                                <i class="bi bi-circle-fill"
                                   style="font-size: 6px;"></i>

                                {{ $history->status ?? 'TIDAK DIKETAHUI' }}

                            </span>
                        </td>

                        <td>
                            <span class="processing-value">
                                {{ $history->processing_time_ms !== null
                                    ? number_format($history->processing_time_ms, 0) . ' ms'
                                    : '-' }}
                            </span>
                        </td>

                        <td>
                            @if($history->scanned_at)

                                <span class="scan-date">
                                    {{ $history->scanned_at->format('d/m/Y') }}
                                </span>

                                <span class="scan-time">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $history->scanned_at->format('H:i:s') }}
                                </span>

                            @else

                                <span class="text-muted">-</span>

                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="history-empty">

                            <i class="bi bi-inbox"></i>

                            <div class="fw-semibold text-dark mb-1">
                                Belum Ada Riwayat Pemindaian
                            </div>

                            <div class="text-muted small">
                                Hasil pemindaian dari kamera akan muncul di sini.
                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    @if($histories->hasPages())

        <div class="history-pagination">
            {{ $histories->links() }}
        </div>

    @endif

</div>

@endsection
