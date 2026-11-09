<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ScanHistory;

class DashboardController extends Controller
{
    public function index()
    {
        $zones = Zone::orderBy('name')->get();

        $totalZones = $zones->count();

        $monitoredZones = $zones->whereNotNull('current_count');

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
}