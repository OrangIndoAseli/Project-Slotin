
@extends('layouts.app')

@section('title', 'Data Zona')
@section('page-title', 'Data Zona Parkir')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Data Zona Parkir</h4>
        <p class="text-muted small mb-0">
            Informasi kapasitas dan kondisi setiap zona parkir.
        </p>
    </div>
</div>

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Total Zona</div>
            <div class="stat-value">{{ $zones->count() }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Total Kapasitas</div>
            <div class="stat-value">{{ $zones->sum('capacity') }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Zona Tidak Terpantau</div>
            <div class="stat-value">
                {{ $zones->where('status', 'TIDAK TERPANTAU')->count() }}
            </div>
        </div>
    </div>

</div>

<div class="section-card">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="section-title mb-0">Daftar Zona</h5>

        <span class="badge bg-primary bg-opacity-10 text-primary">
            {{ $zones->count() }} Zona
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Zona</th>
                    <th>Jumlah Motor</th>
                    <th>Kapasitas</th>
                    <th>Penggunaan</th>
                    <th>Status</th>
                    <th>Terakhir Dipindai</th>
                </tr>
            </thead>

            <tbody>

                @forelse($zones as $index => $zone)

                    @php
                        $colors = [
                            'KOSONG' => 'success',
                            'TERSEDIA' => 'primary',
                            'PENUH' => 'danger',
                            'TIDAK TERPANTAU' => 'secondary',
                        ];

                        $color = $colors[$zone->status] ?? 'secondary';

                        $percentage = $zone->current_count !== null && $zone->capacity > 0
                            ? min(100, ($zone->current_count / $zone->capacity) * 100)
                            : 0;
                    @endphp

                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td class="fw-semibold">
                            {{ $zone->name }}
                        </td>

                        <td>
                            {{ $zone->current_count ?? '-' }}
                        </td>

                        <td>
                            {{ $zone->capacity }} Motor
                        </td>

                        <td style="min-width: 130px;">
                            @if($zone->current_count !== null)
                                <div class="progress" style="height: 7px;">
                                    <div class="progress-bar bg-{{ $color }}"
                                         style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                                <small class="text-muted">
                                    {{ number_format($percentage, 0) }}%
                                </small>
                            @else
                                <small class="text-muted">Belum tersedia</small>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                {{ $zone->status }}
                            </span>
                        </td>

                        <td>
                            @if($zone->last_scanned_at)
                                {{ $zone->last_scanned_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-muted">Belum dipindai</span>
                            @endif
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada data zona.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection
