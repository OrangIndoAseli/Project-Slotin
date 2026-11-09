
<?php

use Illuminate\Support\Facades\Route;

use App\Models\Zone;
use App\Models\ScanHistory;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Route untuk pengguna yang belum login
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

});

// Route yang hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Halaman Scan
    Route::get('/scan', function () {
        $zones = Zone::orderBy('name')->get();

        return view('scan.index', compact('zones'));
    })->name('scan.index');

    // Halaman Data Zona
    Route::get('/zones', function () {
        $zones = Zone::orderBy('name')->get();

        return view('zones.index', compact('zones'));
    })->name('zones.index');

    // Halaman Riwayat
    Route::get('/history', function () {
        $histories = ScanHistory::with(['zone', 'user'])
            ->latest('scanned_at')
            ->paginate(10);

        $totalScans = ScanHistory::count();

        $totalMotorcycles = ScanHistory::sum('motor_count');

        $averageProcessingTime = ScanHistory::whereNotNull('processing_time_ms')
            ->avg('processing_time_ms');

        return view('history.index', compact(
            'histories',
            'totalScans',
            'totalMotorcycles',
            'averageProcessingTime'
        ));
    })->name('history.index');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

});
