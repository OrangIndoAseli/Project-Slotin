<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $table = 'zones';

    protected $fillable = [
        'name',
        'capacity',
        'current_count',
        'status',
        'last_scanned_at',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_count' => 'integer',
        'last_scanned_at' => 'datetime',
    ];

    public function scanHistories(): HasMany
    {
        return $this->hasMany(ScanHistory::class);
    }
}