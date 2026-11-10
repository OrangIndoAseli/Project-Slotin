<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\ScanHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoneDetectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'motor_count' => 'required|integer|min:0',
            'processing_time_ms' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {

            $zone = Zone::lockForUpdate()
                ->findOrFail($validated['zone_id']);

            $motorCount = $validated['motor_count'];

            $capacity = $zone->capacity;

            if ($motorCount === 0) {
                $status = 'KOSONG';
            } elseif ($motorCount >= $capacity) {
                $status = 'PENUH';
            } else {
                $status = 'TERSEDIA';
            }

            $now = now();

            // Update tabel zones
            $zone->update([
                'current_count' => $motorCount,
                'status' => $status,
                'last_scanned_at' => $now,
            ]);

            // Simpan riwayat scan
            ScanHistory::create([
                'zone_id' => $zone->id,
                'user_id' => 1,
                'motor_count' => $motorCount,
                'status' => $status,
                'processing_time_ms' =>
                    $validated['processing_time_ms'] ?? null,
                'scanned_at' => $now,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data zona berhasil diperbarui.',
                'data' => [
                    'zone_id' => $zone->id,
                    'zone_name' => $zone->name,
                    'motor_count' => $motorCount,
                    'capacity' => $capacity,
                    'status' => $status,
                    'last_scanned_at' => $now,
                ],
            ]);
        });
    }
}
