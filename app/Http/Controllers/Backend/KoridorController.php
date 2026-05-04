<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Trip;
use App\Models\StopTime;
use Illuminate\Http\Request;

class KoridorController extends Controller
{
    public function index()
    {
        $koridors = Route::orderBy('route_short_name')->get();

        // Ambil data routes lengkap juga dengan shape
        $gtfsCache = new \App\Services\GtfsCacheService();
        $routes = $gtfsCache->getRoutesWithStopsAndShapes();

        return view('layouts_backend.koridor.index', compact('koridors', 'routes'));
    }

    public function data(Request $request)
    {
        $routes = Route::with('trips')->get();

        $result = [];
        foreach ($routes as $route) {
            $trip = $route->trips->first();
            $halteList = [];

            if ($trip) {
                $stopTimes = StopTime::where('trip_id', $trip->trip_id)
                    ->orderBy('stop_sequence')
                    ->with('stop')
                    ->get();

                foreach ($stopTimes as $st) {
                    if ($st->stop) {
                        $halteList[] = [
                            'id' => $st->stop->stop_id,
                            'nama' => $st->stop->stop_name,
                            'urutan' => $st->stop_sequence,
                            'lat' => $st->stop->stop_lat,
                            'lng' => $st->stop->stop_lon
                        ];
                    }
                }
            }

            $result[] = [
                'id' => $route->route_id,
                'nama_pendek' => $route->route_short_name,
                'nama_panjang' => $route->route_long_name,
                'warna' => $route->route_color ?: '#3498db',
                'jumlah_titik' => 0,
                'halte' => $halteList
            ];
        }

        return response()->json($result);
    }
}
