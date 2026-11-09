<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanHistory extends Model
{
    protected $table = 'scan_histories';

    protected $fillable = [
        'zone_id',
        'user_id',
        'motor_count',
        'status',
        'image_path',
        'processing_time_ms',
        'scanned_at',
    ];

    protected $casts = [
        'motor_count' => 'integer',
        'processing_time_ms' => 'integer',
        'scanned_at' => 'datetime',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}