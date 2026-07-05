<?php

namespace App\Services;

use App\Models\Poi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PoiGeocodingService
{
    /**
     * Cari POI dari DATABASE
     *
     * @param string $query
     * @return array|null
     */
    public function geocodePoi(string $query): ?array
    {
        if (strlen($query) > 50 || strpos($query, '→') !== false) {
            return null;
        }

        $cacheKey = 'poi_search_' . md5($query);

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            $pois = Poi::where('name', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            if ($pois->isNotEmpty()) {
                return $pois->map(function ($poi) {
                    return [
                        'name' => $poi->name,
                        'lat' => (float) $poi->lat,
                        'lng' => (float) $poi->lng,
                        'category' => $poi->category,
                        'osm_id' => $poi->osm_id,
                    ];
                })->toArray();
            }

            return [];
        });
    }

    /**
     * Cari halte terdekat dengan radius bertahap (2km, 5km, 10km)
     * optimasi dengan bounding box terlebih dahulu untuk mempersempit pencarian.
     *
     * @param float $lat
     * @param float $lng
     * @param float $radiusMeters Radius awal (default 2000 = 2km)
     * @return array|null Berisi 'stop', 'distance', 'distance_km'
     */
    public function findNearestStop(float $lat, float $lng, float $radiusMeters = 2000): ?array
    {
        $radii = [2000, 5000, 10000]; // 2km, 5km, 10km
        // Jika parameter radius berbeda, tetap pakai nilai parameter
        $firstRadius = $radiusMeters;
        if (!in_array($firstRadius, $radii)) {
            array_unshift($radii, $firstRadius);
            $radii = array_unique($radii);
            sort($radii);
        }

        foreach ($radii as $radius) {
            // Hitung bounding box (1 derajat ≈ 111 km)
            $delta = $radius / 111000;
            $minLat = $lat - $delta;
            $maxLat = $lat + $delta;
            $minLng = $lng - $delta;
            $maxLng = $lng + $delta;

            $nearestStop = DB::select("
                SELECT *, 
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(stop_lat)) * 
                        cos(radians(stop_lon) - radians(?)) + 
                        sin(radians(?)) * sin(radians(stop_lat))
                    )) as distance_km
                FROM tb_stops
                WHERE stop_lat BETWEEN ? AND ?
                  AND stop_lon BETWEEN ? AND ?
                HAVING distance_km <= ?
                ORDER BY distance_km ASC
                LIMIT 1
            ", [$lat, $lng, $lat, $minLat, $maxLat, $minLng, $maxLng, $radius / 1000]);

            if (!empty($nearestStop)) {
                $stop = $nearestStop[0];
                $distance = $stop->distance_km * 1000;
                return [
                    'stop' => $stop,
                    'distance' => $distance,
                    'distance_km' => round($distance / 1000, 2)
                ];
            }
        }

        return null;
    }

    /**
     * Hitung jarak Haversine (meter) untuk fallback jika diperlukan
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Hapus cache yang berkaitan dengan POI
     *
     * @return void
     */
    public function clearCache()
    {
        try {
            $keys = DB::table('cache')
                ->where('key', 'like', 'poi_search_%')
                ->orWhere('key', 'like', 'poi_with_nearest_%')
                ->pluck('key');
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        } catch (\Exception $e) {
            Cache::flush();
        }
    }

    /**
     * Cari POI dengan informasi halte terdekat (1 request untuk semua POI)
     *
     * @param string $query
     * @return array|null
     */
    public function geocodePoiWithNearestStop(string $query): ?array
    {
        if (strlen($query) > 50 || strpos($query, '→') !== false) {
            return null;
        }

        $cacheKey = 'poi_with_nearest_' . md5($query);

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            $pois = Poi::where('name', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            if ($pois->isEmpty()) {
                return [];
            }

            $results = [];
            foreach ($pois as $poi) {
                $nearest = $this->findNearestStop($poi->lat, $poi->lng);

                $results[] = [
                    'name' => $poi->name,
                    'lat' => (float) $poi->lat,
                    'lng' => (float) $poi->lng,
                    'category' => $poi->category,
                    'nearest_stop' => $nearest ? [
                        'name' => $nearest['stop']->stop_name,
                        'distance_km' => $nearest['distance_km']
                    ] : null
                ];
            }

            return $results;
        });
    }
}
