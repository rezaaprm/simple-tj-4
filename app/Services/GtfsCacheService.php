<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GtfsCacheService
{
    /**
     * Ambil data routes lengkap dengan stops dan shapes (2 arah)
     */
    public function getRoutesWithStopsAndShapes()
    {
        return Cache::remember('gtfs_routes_final', 86400, function () {
            return $this->buildRoutesData();
        });
    }

    /**
     * Bangun data routes dari database
     */
    private function buildRoutesData()
    {
        // 1. Ambil semua routes
        $allRoutes = DB::table('tb_routes')->get();

        // 2. Mapping route + direction ke shape_id
        $routeDirections = DB::table('tb_trips')
            ->select('route_id', 'direction_id', 'shape_id')
            ->distinct()
            ->get();

        $routeToShape = [];
        $routeToTrips = [];

        foreach ($routeDirections as $rd) {
            $direction = $rd->direction_id ?? '0';
            $key = $rd->route_id . '_' . $direction;
            $routeToShape[$key] = $rd->shape_id;
        }

        // 3. Kumpulkan semua trip_id per kombinasi route+direction
        $allTrips = DB::table('tb_trips')
            ->select('route_id', 'direction_id', 'trip_id')
            ->get();

        foreach ($allTrips as $trip) {
            $direction = $trip->direction_id ?? '0';
            $key = $trip->route_id . '_' . $direction;
            if (!isset($routeToTrips[$key])) {
                $routeToTrips[$key] = [];
            }
            $routeToTrips[$key][] = $trip->trip_id;
        }

        // 4. Ambil semua stops
        $stopsData = [];
        $allStops = DB::table('tb_stops')->get();
        foreach ($allStops as $stop) {
            $stopsData[$stop->stop_id] = [
                'id' => $stop->stop_id,
                'name' => $stop->stop_name,
                'lat' => (float) $stop->stop_lat,
                'lng' => (float) $stop->stop_lon,
            ];
        }

        // 5. Ambil semua stop times (urutan halte per trip)
        $stopTimes = DB::table('tb_stop_times')->get();
        $tripToStops = [];
        foreach ($stopTimes as $st) {
            $tripToStops[$st->trip_id][$st->stop_sequence] = $st->stop_id;
        }
        foreach ($tripToStops as $tripId => $stops) {
            ksort($tripToStops[$tripId]);
        }

        // 6. Ambil semua shape points - DIURUTKAN!
        $shapes = DB::table('tb_shapes')
            ->orderBy('shape_id')
            ->orderBy('shape_pt_sequence')
            ->get();

        $shapePoints = [];
        foreach ($shapes as $shape) {
            if ($shape->shape_pt_lat == 0 || $shape->shape_pt_lon == 0) continue;

            $shapePoints[$shape->shape_id][$shape->shape_pt_sequence] = [
                (float) $shape->shape_pt_lat,
                (float) $shape->shape_pt_lon
            ];
        }

        // Urutkan setiap shape berdasarkan sequence
        foreach ($shapePoints as $shapeId => $points) {
            ksort($shapePoints[$shapeId]);
            $shapePoints[$shapeId] = array_values($shapePoints[$shapeId]);
        }

        // 7. Bangun data routes final (2 arah)
        $routesData = [];
        foreach ($allRoutes as $route) {
            foreach (['0', '1'] as $dir) {
                $key = $route->route_id . '_' . $dir;
                $shapeId = $routeToShape[$key] ?? null;

                // Ambil stops untuk arah ini
                $dirStops = [];
                $sampleTrip = $routeToTrips[$key][0] ?? null;
                if ($sampleTrip && isset($tripToStops[$sampleTrip])) {
                    foreach ($tripToStops[$sampleTrip] as $stopId) {
                        if (isset($stopsData[$stopId])) {
                            $dirStops[] = $stopsData[$stopId];
                        }
                    }
                }

                if ($shapeId && isset($shapePoints[$shapeId])) {
                    $routesData[] = [
                        'id' => $key,
                        'short_name' => $route->route_short_name,
                        'long_name' => $route->route_long_name . ($dir == '0' ? ' (A)' : ' (B)'),
                        'color' => $route->route_color ?: '#3498db',
                        'shape' => $shapePoints[$shapeId],
                        'stops' => $dirStops
                    ];
                }
            }
        }

        return $routesData;
    }

    /**
     * Hapus cache
     */
    public function clearCache()
    {
        Cache::forget('gtfs_routes_final');
    }
}
