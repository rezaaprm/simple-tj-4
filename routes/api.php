<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HalteController;
use App\Http\Controllers\Api\KoridorController;
use App\Http\Controllers\Api\RuteController;
use App\Http\Controllers\Api\PencarianLogApiController;
use App\Http\Controllers\Api\GeocodingController;

// Route untuk autocomplete halte
Route::get('/halte/cari', [HalteController::class, 'cari']);

// Route untuk mengambil shapes koridor
Route::get('/koridor/{id}/shapes', [KoridorController::class, 'shapes']);

// Route untuk mencari rute
Route::post('/rute/cari', [RuteController::class, 'cariRute']);

// API tambahan untuk log pencarian
Route::post('/pencarian-log', [PencarianLogApiController::class, 'store']);

// API tambahan untuk koridor/index.blade.php
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

// POI routes
Route::get('/geocode', [GeocodingController::class, 'geocode']);
Route::get('/nearest-stop', [GeocodingController::class, 'nearestStop']);

// API untuk mengambil shape berdasarkan routeId (digunakan oleh AJAX di production)
Route::get('/shape/{routeId}', function ($routeId) {
    try {
        $service = app(\App\Services\GtfsCacheService::class);
        $shape = $service->getShapeByRouteId($routeId);
        return response()->json(['success' => true, 'shape' => $shape]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});
