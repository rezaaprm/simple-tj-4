<?php

namespace App\Http\Controllers;

use App\Services\GtfsCacheService;
use Illuminate\Support\Facades\Log;

class MapController extends Controller
{
    protected $gtfsCache;

    public function __construct(GtfsCacheService $gtfsCache)
    {
        $this->gtfsCache = $gtfsCache;
    }

    public function index()
    {
        try {
            $startTime = microtime(true);

            $routesData = $this->gtfsCache->getRoutesWithStopsAndShapes();

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
            Log::error('MapController error: ' . $e->getMessage());

            return view('map', [
                'routes' => [],
                'totalRoutes' => 0,
                'totalStops' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Ambil semua routes dalam format JSON
     */
    public function getRoutesJson()
    {
        try {
            $routesData = $this->gtfsCache->getRoutesWithStopsAndShapes();
            return response()->json([
                'success' => true,
                'total' => count($routesData),
                'data' => $routesData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API ambil semua halte unik dalam format JSON
     */
    public function getJsonHalte()
    {
        try {
            $routesData = $this->gtfsCache->getRoutesWithStopsAndShapes();

            $allStops = [];
            $uniqueIds = [];

            foreach ($routesData as $route) {
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
}
