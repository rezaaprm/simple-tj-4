<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Poi;
use App\Services\PoiGeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PoiController extends Controller
{
    protected $poiService;

    public function __construct(PoiGeocodingService $poiService)
    {
        $this->poiService = $poiService;
    }

    /**
     * Display a listing of POI.
     */
    public function index()
    {
        $pois = Poi::orderBy('id', 'asc')->paginate(20);
        return view('layouts_backend.poi.index', compact('pois'));
    }

    /**
     * Show form for creating new POI.
     */
    public function create()
    {
        $readonly = false;
        return view('layouts_backend.poi.create', compact('readonly'));
    }

    /**
     * Store a newly created POI with validation (max 2km from nearest stop).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $bounds = $this->getStopBounds();
        $errors = [];

        // Cek latitude
        if ($request->lat < $bounds['min_lat'] || $request->lat > $bounds['max_lat']) {
            $errors['lat'] = 'Latitude terlalu jauh dari jangkauan halte TransJakarta.';
        }

        // Cek longitude
        if ($request->lng < $bounds['min_lng'] || $request->lng > $bounds['max_lng']) {
            $errors['lng'] = 'Longitude terlalu jauh dari jangkauan halte TransJakarta.';
        }

        // Jika ada error koordinat, kembalikan
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Validasi jarak ke halte terdekat (maksimal 2 km)
        $nearest = $this->poiService->findNearestStop($request->lat, $request->lng, 2000);

        if (!$nearest) {
            return back()
                ->withErrors([
                    'lat' => 'Lokasi (latitude/longitude) terlalu jauh dari halte TransJakarta. Maksimal jarak 2 km.',
                    'lng' => 'Lokasi (latitude/longitude) terlalu jauh dari halte TransJakarta. Maksimal jarak 2 km.'
                ])
                ->withInput();
        }

        Log::info('POI baru: ' . $request->name . ' - jarak ke halte terdekat: ' . $nearest['distance_km'] . ' km');

        Poi::create([
            'name' => $request->name,
            'category' => $request->category,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'osm_id' => null,
        ]);

        return redirect()->route('admin.poi.index')
            ->with('success', 'POI berhasil ditambahkan (jarak ke halte: ' . $nearest['distance_km'] . ' km)');
    }

    /**
     * Show form for editing POI.
     */
    public function edit($id)
    {
        $data = Poi::findOrFail($id);
        $readonly = false;
        return view('layouts_backend.poi.edit', compact('data', 'readonly'));
    }

    /**
     * Update POI with same distance validation.
     */
    public function update(Request $request, $id)
    {
        $poi = Poi::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $bounds = $this->getStopBounds();
        $errors = [];

        if ($request->lat < $bounds['min_lat'] || $request->lat > $bounds['max_lat']) {
            $errors['lat'] = 'Latitude terlalu jauh dari jangkauan halte TransJakarta.';
        }

        if ($request->lng < $bounds['min_lng'] || $request->lng > $bounds['max_lng']) {
            $errors['lng'] = 'Longitude terlalu jauh dari jangkauan halte TransJakarta.';
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $nearest = $this->poiService->findNearestStop($request->lat, $request->lng, 2000);

        if (!$nearest) {
            return back()
                ->withErrors([
                    'lat' => 'Lokasi (latitude/longitude) terlalu jauh dari halte TransJakarta. Maksimal jarak 2 km.',
                    'lng' => 'Lokasi (latitude/longitude) terlalu jauh dari halte TransJakarta. Maksimal jarak 2 km.'
                ])
                ->withInput();
        }

        $poi->update([
            'name' => $request->name,
            'category' => $request->category,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        return redirect()->route('admin.poi.index')
            ->with('success', 'POI berhasil diperbarui (jarak ke halte: ' . $nearest['distance_km'] . ' km)');
    }

    /**
     * Show confirmation delete page.
     */
    public function confirmDelete($id)
    {
        $data = Poi::findOrFail($id);
        $readonly = true;
        return view('layouts_backend.poi.delete', compact('data', 'readonly'));
    }

    /**
     * Delete POI.
     */
    public function destroy($id)
    {
        $poi = Poi::findOrFail($id);
        $poi->delete();
        return redirect()->route('admin.poi.index')->with('success', 'POI berhasil dihapus');
    }

    /**
     * Get bounding box (min/max lat/lng) of all stops.
     *
     * @return array
     */
    private function getStopBounds()
    {
        $bounds = DB::table('tb_stops')
            ->select(
                DB::raw('MIN(stop_lat) as min_lat'),
                DB::raw('MAX(stop_lat) as max_lat'),
                DB::raw('MIN(stop_lon) as min_lng'),
                DB::raw('MAX(stop_lon) as max_lng')
            )
            ->first();

        return [
            'min_lat' => (float) $bounds->min_lat,
            'max_lat' => (float) $bounds->max_lat,
            'min_lng' => (float) $bounds->min_lng,
            'max_lng' => (float) $bounds->max_lng,
        ];
    }
}
