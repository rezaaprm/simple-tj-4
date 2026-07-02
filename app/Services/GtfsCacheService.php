<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GtfsCacheService
{
    /**
     * Ambil data routes lengkap dengan stops dan shapes (2 arah)
     * Data di-cache selama 24 jam untuk performa optimal.
     *
     * @return array
     */
    public function getRoutesWithStopsAndShapes()
    {
        return Cache::remember('gtfs_routes_final', 86400, function () {
            return $this->buildRoutesData();
        });
    }

    /**
     * Bangun data routes dari database (tanpa cache).
     * Proses ini memuat seluruh data GTFS dari tabel:
     * - tb_routes, tb_trips, tb_stops, tb_stop_times, tb_shapes, tb_calendar
     *
     * @return array
     */
    private function buildRoutesData()
    {
        // 1. Ambil semua rute
        $allRoutes = DB::table('tb_routes')->get();

        // 2. Mapping route + direction ke shape_id (Universal tanpa filter kalender)
        $routeDirections = DB::table('tb_trips')
            ->select('route_id', 'direction_id', 'shape_id')
            ->distinct()
            ->get();

        $routeToShape = [];
        foreach ($routeDirections as $rd) {
            $direction = $rd->direction_id ?? '0';
            $key = $rd->route_id . '_' . $direction;
            $routeToShape[$key] = $rd->shape_id;
        }

        // ============================================================
        // 3. Ambil data trips berdasarkan filter kalender dinamis
        // ============================================================
        $useCalendarFilter = config('gtfs.use_calendar_filter', false);
        $routeToTrips = [];

        if ($useCalendarFilter) {
            $today = strtolower(date('l'));
            $todayDate = date('Ymd');

            // Mengambil service_id yang valid & aktif pada hari ini
            $activeServices = DB::table('tb_calendar')
                ->where($today, '=', 1)
                ->where('start_date', '<=', $todayDate)
                ->where('end_date', '>=', $todayDate)
                ->pluck('service_id')
                ->toArray();

            $allTrips = DB::table('tb_trips')
                ->select('route_id', 'direction_id', 'trip_id', 'service_id')
                ->whereIn('service_id', $activeServices)
                ->get();

            // Fallback Pengaman: Jika database calendar Anda belum lengkap/seeder bermasalah,
            // jangan biarkan aplikasi mogok dan menghasilkan data kosong.
            if ($allTrips->isEmpty()) {
                $allTrips = DB::table('tb_trips')
                    ->select('route_id', 'direction_id', 'trip_id')
                    ->get();
            }
        } else {
            $allTrips = DB::table('tb_trips')
                ->select('route_id', 'direction_id', 'trip_id')
                ->get();
        }

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

        // 5. Ambil semua stop times
        // $stopTimes = DB::table('tb_stop_times')->get();
        // $tripToStops = [];
        // foreach ($stopTimes as $st) {
        //     $tripToStops[$st->trip_id][$st->stop_sequence] = $st->stop_id;
        // }
        // foreach ($tripToStops as $tripId => $stops) {
        //     ksort($tripToStops[$tripId]);
        // }

        // // Mapping shape_dist_traveled per trip & sequence
        // $tripShapeDist = [];
        // foreach ($stopTimes as $st) {
        //     if ($st->shape_dist_traveled !== null) {
        //         $tripShapeDist[$st->trip_id][$st->stop_sequence] = (float) $st->shape_dist_traveled;
        //     }
        // }

        // 5. Ambil semua stop times - PAKAI CHUNK (24.567 baris aman)
        $tripToStops = [];
        $tripShapeDist = [];

        DB::table('tb_stop_times')
            ->orderBy('trip_id')
            ->orderBy('stop_sequence')
            ->chunk(5000, function ($stopTimesChunk) use (&$tripToStops, &$tripShapeDist) {
                foreach ($stopTimesChunk as $st) {
                    $tripToStops[$st->trip_id][$st->stop_sequence] = $st->stop_id;
                    if ($st->shape_dist_traveled !== null) {
                        $tripShapeDist[$st->trip_id][$st->stop_sequence] = (float) $st->shape_dist_traveled;
                    }
                }
            });

        // Sort sequence tetap sama
        foreach ($tripToStops as $tripId => $stops) {
            ksort($tripToStops[$tripId]);
        }

        // 6. Ambil semua shape points (diurutkan)
        // $shapes = DB::table('tb_shapes')
        //     ->orderBy('shape_id')
        //     ->orderBy('shape_pt_sequence')
        //     ->get();
        // $shapePoints = [];
        // foreach ($shapes as $shape) {
        //     if ($shape->shape_pt_lat == 0 || $shape->shape_pt_lon == 0) continue;
        //     $shapePoints[$shape->shape_id][$shape->shape_pt_sequence] = [
        //         (float) $shape->shape_pt_lat,
        //         (float) $shape->shape_pt_lon
        //     ];
        // }

        // 6. Ambil semua shape points - PAKAI CHUNK (218.833 titik aman)
        $shapePoints = [];

        DB::table('tb_shapes')
            ->orderBy('shape_id')
            ->orderBy('shape_pt_sequence')
            ->chunk(5000, function ($shapesChunk) use (&$shapePoints) {
                foreach ($shapesChunk as $shape) {
                    if ($shape->shape_pt_lat == 0 || $shape->shape_pt_lon == 0) continue;
                    $shapePoints[$shape->shape_id][$shape->shape_pt_sequence] = [
                        (float) $shape->shape_pt_lat,
                        (float) $shape->shape_pt_lon
                    ];
                }
            });

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
                $dirStops = [];

                // Evaluasi Trip berdasarkan ketersediaan kalender
                if (isset($routeToTrips[$key]) && !empty($routeToTrips[$key])) {
                    $allStopIds = [];
                    $stopOrder = [];
                    $stopShapeDist = []; // buat menyimpan shape_dist ke next stop

                    foreach ($routeToTrips[$key] as $tripId) {
                        if (isset($tripToStops[$tripId])) {
                            $sequences = array_keys($tripToStops[$tripId]);
                            for ($idx = 0; $idx < count($sequences) - 1; $idx++) {
                                $seq = $sequences[$idx];
                                $nextSeq = $sequences[$idx + 1];
                                $stopId = $tripToStops[$tripId][$seq];
                                $nextStopId = $tripToStops[$tripId][$nextSeq];

                                $allStopIds[$stopId] = true;
                                if (!isset($stopOrder[$stopId]) || $seq < $stopOrder[$stopId]) {
                                    $stopOrder[$stopId] = $seq;
                                }

                                // Hitung jarak ke Next Stop (shape_dist_next - shape_dist_current)
                                $currentDist = $tripShapeDist[$tripId][$seq] ?? 0;
                                $nextDist = $tripShapeDist[$tripId][$nextSeq] ?? 0;
                                $distToNext = $nextDist - $currentDist;

                                if ($distToNext > 0) {
                                    if (!isset($stopShapeDist[$stopId]) || $distToNext < $stopShapeDist[$stopId]) {
                                        $stopShapeDist[$stopId] = $distToNext;
                                    }
                                }
                            }
                            // Handle stop terakhir (tidak punya next, pakai haversine nanti)
                            $lastSeq = end($sequences);
                            $lastStopId = $tripToStops[$tripId][$lastSeq];
                            $allStopIds[$lastStopId] = true;
                            if (!isset($stopOrder[$lastStopId]) || $lastSeq < $stopOrder[$lastStopId]) {
                                $stopOrder[$lastStopId] = $lastSeq;
                            }
                        }
                    }

                    $sortedStopIds = array_keys($allStopIds);
                    usort($sortedStopIds, function ($a, $b) use ($stopOrder) {
                        return ($stopOrder[$a] ?? 99999) <=> ($stopOrder[$b] ?? 99999);
                    });

                    $prevStopId = null;
                    foreach ($sortedStopIds as $stopId) {
                        if (isset($stopsData[$stopId])) {
                            $stopData = $stopsData[$stopId];
                            // Tambah shape_dist ke next stop (jika ada)
                            if ($prevStopId && isset($stopShapeDist[$prevStopId])) {
                                $stopsData[$prevStopId]['shape_dist_to_next'] = $stopShapeDist[$prevStopId];
                            }
                            $dirStops[] = $stopData;
                            $prevStopId = $stopId;
                        }
                    }
                    // Handle last stop (tidak punya next)
                    if ($prevStopId && isset($stopsData[$prevStopId])) {
                        $stopsData[$prevStopId]['shape_dist_to_next'] = null;
                    }
                }

                // Fallback hanya berlaku jika hari ini hari libur (Sabtu/Minggu)
                $todayDay = strtolower(date('l')); // Mengambil nama hari (monday, tuesday, dll)
                $isWeekend = ($todayDay === 'saturday' || $todayDay === 'sunday');

                if (empty($dirStops) && $isWeekend) {
                    // Ambil trip acak universal dari rute ini sebagai fallback halte khusus weekend
                    $fallbackTrip = DB::table('tb_trips')
                        ->where('route_id', $route->route_id)
                        ->where('direction_id', $dir)
                        ->first();

                    if ($fallbackTrip && isset($tripToStops[$fallbackTrip->trip_id])) {
                        foreach ($tripToStops[$fallbackTrip->trip_id] as $stopId) {
                            if (isset($stopsData[$stopId])) {
                                $dirStops[] = $stopsData[$stopId];
                            }
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
     * Hapus cache data routes dari storage.
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::forget('gtfs_routes_final');
    }
}
