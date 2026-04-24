<?php

namespace App\Http\Controllers;

use App\Services\GtfsCacheService;
use Illuminate\Support\Facades\Log;

class TransportasiController extends Controller
{
    protected $gtfsCache;

    public function __construct(GtfsCacheService $gtfsCache)
    {
        $this->gtfsCache = $gtfsCache;
    }

    public function indeksPeta()
    {
        try {
            $startTime = microtime(true);

            // Ambil data dari cache
            $routesData = $this->gtfsCache->getRoutesWithStopsAndShapes();

            // DEBUG: Cek apakah data terisi
            if (empty($routesData)) {
                Log::error('routesData kosong!');
            } else {
                Log::info('routesData terisi: ' . count($routesData) . ' rute');
            }

            $totalRoutes = count($routesData);
            $totalStops = 0;

            foreach ($routesData as $route) {
                $totalStops += count($route['stops']);
            }

            $loadTime = round(microtime(true) - $startTime, 2);

            return view('map', [
                'routes' => $routesData,
                'totalRoutes' => $totalRoutes,
                'totalStops' => $totalStops,
                'loadTime' => $loadTime
            ]);
        } catch (\Exception $e) {
            Log::error('TransportasiController error: ' . $e->getMessage());

            return view('map', [
                'routes' => [],
                'totalRoutes' => 0,
                'totalStops' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getRoutesJson()
    {
        try {
            $routes = $this->gtfsCache->getRoutesWithStopsAndShapes();
            return response()->json([
                'success' => true,
                'total' => count($routes),
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getJsonHalte()
    {
        try {
            $routes = $this->gtfsCache->getRoutesWithStopsAndShapes();

            $allStops = [];
            $uniqueIds = [];

            foreach ($routes as $route) {
                foreach ($route['stops'] as $stop) {
                    if (!in_array($stop['id'], $uniqueIds)) {
                        $uniqueIds[] = $stop['id'];
                        $allStops[] = [
                            'id' => $stop['id'],
                            'name' => $stop['name'],
                            'lat' => $stop['lat'],
                            'lng' => $stop['lng']
                        ];
                    }
                }
            }

            return response()->json([
                'total' => count($allStops),
                'data' => $allStops
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getJsonShape()
    {
        try {
            $routes = $this->gtfsCache->getRoutesWithStopsAndShapes();

            $shapes = [];
            foreach ($routes as $route) {
                $shapes[] = [
                    'id' => $route['id'],
                    'koridor' => $route['short_name'],
                    'arah' => $route['long_name'],
                    'total_titik' => count($route['shape']),
                    'shape' => $route['shape']
                ];
            }

            return response()->json([
                'total_koridor' => count($shapes),
                'data' => $shapes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getJsonRute()
    {
        try {
            $routes = $this->gtfsCache->getRoutesWithStopsAndShapes();

            $rutes = [];
            foreach ($routes as $route) {
                $rutes[] = [
                    'id' => $route['id'],
                    'koridor' => $route['short_name'],
                    'nama' => $route['long_name'],
                    'warna' => $route['color'],
                    'jumlah_halte' => count($route['stops']),
                    'jumlah_titik' => count($route['shape'])
                ];
            }

            return response()->json([
                'total' => count($rutes),
                'data' => $rutes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getJsonWarna()
    {
        try {
            $routes = $this->gtfsCache->getRoutesWithStopsAndShapes();

            $warna = [];
            foreach ($routes as $route) {
                $warna[] = [
                    'id' => $route['id'],
                    'koridor' => $route['short_name'],
                    'nama' => $route['long_name'],
                    'warna' => $route['color'],
                    'warna_hex' => $route['color']
                ];
            }

            return response()->json([
                'total' => count($warna),
                'data' => $warna
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
