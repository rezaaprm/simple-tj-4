<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\TransportasiController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\AlgoritmaController;
use App\Http\Controllers\Backend\HalteController;
use App\Http\Controllers\Backend\KoridorController;
use App\Http\Controllers\Backend\PencarianLogController;
use App\Http\Controllers\Api\HalteController as ApiHalteController;
use App\Http\Controllers\Api\KoridorController as ApiKoridorController;
use App\Http\Controllers\Api\RuteController as ApiRuteController;

use App\Http\Controllers\Backend\AboutController;
use App\Http\Controllers\Backend\DestinasiController;
use App\Http\Controllers\Backend\InfoStatistikController;
use App\Http\Controllers\Backend\GaleriController;
use App\Http\Controllers\Backend\KolaborasiController;
use App\Http\Controllers\Frontend\FrontendController;

use App\Http\Controllers\Api\GeocodingController;

// Welcome
Route::get('/welcome', function () {
    return redirect('/admin/dashboard');
})->name('welcome');

// Frontend
Route::get('/', [FrontendController::class, 'index'])->name('frontend.home');

// Frontend route
// Route::get('/frontend', function () {
//     return view('layouts_frontend.frontend');
// });


// ==================== ROUTE UNTUK CRUD BACKEND ====================
Route::resource('about', AboutController::class);
Route::get('about/{id}/delete', [AboutController::class, 'confirmDelete'])->name('about.confirmDelete');

Route::resource('info_statistik', InfoStatistikController::class);
Route::get('info_statistik/{id}/delete', [InfoStatistikController::class, 'confirmDelete'])->name('info_statistik.confirmDelete');

Route::resource('destinasi', DestinasiController::class);
Route::get('destinasi/{id}/delete', [DestinasiController::class, 'confirmDelete'])->name('destinasi.confirmDelete');

Route::resource('galeri', GaleriController::class);
Route::get('galeri/{id}/delete', [GaleriController::class, 'confirmDelete'])->name('galeri.confirmDelete');

Route::resource('kolaborasi', KolaborasiController::class);
Route::get('kolaborasi/{id}/delete', [KolaborasiController::class, 'confirmDelete'])->name('kolaborasi.confirmDelete');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transjakarta/map', [TransportasiController::class, 'indeksPeta'])->name('transjakarta.map');
    Route::get('/algoritma', [AlgoritmaController::class, 'index'])->name('algoritma');

    // ==================== ROUTE UNTUK FRONTEND ====================
    Route::resource('about', AboutController::class);
    // Route::get('about/{id}/delete', [AboutController::class, 'confirmDelete'])->name('about.confirmDelete');

    Route::resource('info_statistik', InfoStatistikController::class);
    // Route::get('info_statistik/{id}/delete', [InfoStatistikController::class, 'confirmDelete'])->name('info_statistik.confirmDelete');

    Route::resource('destinasi', DestinasiController::class);
    // Route::get('destinasi/{id}/delete', [DestinasiController::class, 'confirmDelete'])->name('destinasi.confirmDelete');

    Route::resource('galeri', GaleriController::class);
    // Route::get('galeri/{id}/delete', [GaleriController::class, 'confirmDelete'])->name('galeri.confirmDelete');

    Route::resource('kolaborasi', KolaborasiController::class);
    // Route::get('kolaborasi/{id}/delete', [KolaborasiController::class, 'confirmDelete'])->name('kolaborasi.confirmDelete');



    // ==================== ROUTE UNTUK ALGORITMA (POST & GET DATA) ====================
    Route::post('/algoritma/store', [AlgoritmaController::class, 'storeRouteCalculation'])->name('algoritma.store');
    Route::get('/algoritma/data', [AlgoritmaController::class, 'getLastRouteData'])->name('algoritma.data');

    Route::controller(HalteController::class)->group(function () {
        Route::get('/halte', 'index')->name('halte.index');
        Route::get('/halte/data', 'data')->name('halte.data');
    });

    Route::controller(KoridorController::class)->group(function () {
        Route::get('/koridor', 'index')->name('koridor.index');
        Route::get('/koridor/data', 'data')->name('koridor.data');
    });

    Route::controller(PencarianLogController::class)->group(function () {
        Route::get('/pencarian/log', 'index')->name('pencarian.log');
        Route::get('/pencarian/data', 'data')->name('pencarian.data');
    });
});

// Routes JSON
Route::get('/routes-json', [TransportasiController::class, 'getRoutesJson']);
Route::get('/api/json/halte', [TransportasiController::class, 'getJsonHalte']);
Route::get('/api/json/shape', [TransportasiController::class, 'getJsonShape']);
Route::get('/api/json/rute', [TransportasiController::class, 'getJsonRute']);
Route::get('/api/json/warna', [TransportasiController::class, 'getJsonWarna']);

Route::get('/api/json/poi', [TransportasiController::class, 'getJsonPoi']);
Route::get('/api/json/poi/kategori/{kategori}', [TransportasiController::class, 'getJsonPoiByCategory']);

// Ambil daftar kategori POI (untuk dropdown)
Route::get('/api/poi/categories', function () {
    $categories = App\Models\Poi::select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');

    return response()->json($categories);
});

// ==================== API ROUTES ====================
Route::prefix('api')->group(function () {

    // Halte search
    Route::get('/halte/cari', [ApiHalteController::class, 'cari']);

    // Koridor shapes
    Route::get('/koridor/{id}/shapes', [ApiKoridorController::class, 'shapes']);

    // Rute search
    Route::post('/rute/cari', [ApiRuteController::class, 'cariRute']);

    // Rute reset
    Route::post('/admin/algoritma/reset', [App\Http\Controllers\Backend\AlgoritmaController::class, 'resetLastRoute']);

    // Log pencarian dari client
    Route::post('/pencarian-log', [App\Http\Controllers\Api\PencarianLogApiController::class, 'store']);

    // API untuk ambil detail log berdasarkan ID
    Route::get('/pencarian-log/{id}', [PencarianLogController::class, 'getLogDetail']);

    // Trips by route (untuk koridor/index.blade.php)
    Route::get('/trips-by-route/{routeId}', function ($routeId) {
        $trip = \App\Models\Trip::where('route_id', $routeId)->first();
        if ($trip) {
            $route = \App\Models\Route::find($routeId);
            return response()->json([
                'trip_id' => $trip->trip_id,
                'warna' => $route->route_color ?? '#3498db'
            ]);
        }
        return response()->json(['trip_id' => null]);
    });

    // Stops by trip (untuk koridor/index.blade.php)
    Route::get('/stops-by-trip/{tripId}', function ($tripId) {
        $stopTimes = \App\Models\StopTime::where('trip_id', $tripId)
            ->orderBy('stop_sequence')
            ->with('stop')
            ->get();
        $stops = [];
        foreach ($stopTimes as $st) {
            if ($st->stop) {
                $stops[] = [
                    'id' => $st->stop->stop_id,
                    'name' => $st->stop->stop_name,
                    'lat' => $st->stop->stop_lat,
                    'lng' => $st->stop->stop_lon
                ];
            }
        }
        return response()->json(['stops' => $stops]);
    });
});


// ==================== ROUTE POI ====================
Route::get('/api/geocode', [GeocodingController::class, 'geocode']);
Route::get('/api/nearest-stop', [GeocodingController::class, 'nearestStop']);
