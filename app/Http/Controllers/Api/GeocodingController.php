<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PoiGeocodingService;
use Illuminate\Http\Request;

class GeocodingController extends Controller
{
    protected $geocoder;

    public function __construct(PoiGeocodingService $geocoder)
    {
        $this->geocoder = $geocoder;
    }

    /**
     * Cari POI berdasarkan query
     */
    public function geocode(Request $request)
    {
        $query = $request->query('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $pois = $this->geocoder->geocodePoiWithNearestStop($query);

        return response()->json($pois ?? []);
    }

    /**
     * Cari halte terdekat dari koordinat
     */
    public function nearestStop(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!$lat || !$lng) {
            return response()->json(['error' => 'Latitude and longitude required'], 400);
        }

        $nearest = $this->geocoder->findNearestStop($lat, $lng);

        if (!$nearest) {
            return response()->json(['error' => 'No stop found within radius'], 404);
        }

        return response()->json([
            'stop' => $nearest['stop'],
            'distance_meters' => $nearest['distance'],
            'distance_km' => $nearest['distance_km']
        ]);
    }
}
