<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ScanHistory;

class DashboardController extends Controller
{
    /**
     * Menentukan status zona berdasarkan waktu deteksi terakhir.
     */
    private function getZones()
    {
        $zones = Zone::orderBy('name')->get();

        foreach ($zones as $zone) {

            $lastScanned = $zone->last_scanned_at;

            // Kamera dianggap terputus jika tidak ada update 15 detik.
            $isDisconnected = !$lastScanned ||
                $lastScanned->lt(now()->subSeconds(15));

            if ($isDisconnected) {
                $zone->status = 'TIDAK TERPANTAU';
            }
        }

        return $zones;
    }

    /**
     * Dashboard utama.
     */
    public function index()
    {
        $zones = $this->getZones();

        $monitoredZones = $zones->filter(function ($zone) {
            return $zone->status !== 'TIDAK TERPANTAU'
                && $zone->current_count !== null;
        });

        $totalZones = $zones->count();

        $totalMotorcycles = $monitoredZones->sum('current_count');

        $availableCapacity = $monitoredZones->sum(function ($zone) {
            return max(0, $zone->capacity - $zone->current_count);
        });

        $emptyZones = $zones->where('status', 'KOSONG')->count();

        $availableZones = $zones->where('status', 'TERSEDIA')->count();

        $fullZones = $zones->where('status', 'PENUH')->count();

        $unmonitoredZones = $zones->where(
            'status',
            'TIDAK TERPANTAU'
        )->count();

        $recentScans = ScanHistory::with(['zone', 'user'])
            ->latest('scanned_at')
            ->take(5)
            ->get();

        return view('dashboard.app', compact(
            'zones',
            'totalZones',
            'totalMotorcycles',
            'availableCapacity',
            'emptyZones',
            'availableZones',
            'fullZones',
            'unmonitoredZones',
            'recentScans'
        ));
    }

    /**
     * Endpoint data dashboard realtime.
     */
    public function realtime()
    {
        $zones = $this->getZones();

        $monitoredZones = $zones->filter(function ($zone) {
            return $zone->status !== 'TIDAK TERPANTAU'
                && $zone->current_count !== null;
        });

        return response()->json([
            'success' => true,

            'statistics' => [
                'totalZones' => $zones->count(),

                'totalMotorcycles' => $monitoredZones->sum('current_count'),

                'availableCapacity' => $monitoredZones->sum(function ($zone) {
                    return max(
                        0,
                        $zone->capacity - $zone->current_count
                    );
                }),

                'emptyZones' => $zones->where('status', 'KOSONG')->count(),

                'availableZones' => $zones->where('status', 'TERSEDIA')->count(),

                'fullZones' => $zones->where('status', 'PENUH')->count(),

                'unmonitoredZones' => $zones->where(
                    'status',
                    'TIDAK TERPANTAU'
                )->count(),
            ],

            'zones' => $zones->map(function ($zone) {
                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'capacity' => $zone->capacity,
                    'current_count' => $zone->current_count,
                    'status' => $zone->status,
                    'last_scanned_at' => $zone->last_scanned_at
                        ? $zone->last_scanned_at->format('d/m/Y H:i:s')
                        : '-',
                ];
            })->values(),

            'updated_at' => now()->format('H:i:s'),
        ]);
    }
}
