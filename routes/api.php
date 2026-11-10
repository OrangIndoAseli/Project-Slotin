<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZoneDetectionController;

Route::post('/zone-detection', [
    ZoneDetectionController::class,
    'store'
]);