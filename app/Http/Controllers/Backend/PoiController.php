<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Poi;
use App\Services\PoiGeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Validasi jarak ke halte terdekat (maksimal 2 km)
        $nearest = $this->poiService->findNearestStop($request->lat, $request->lng, 2000);

        if (!$nearest) {
            return back()
                ->withErrors(['lat' => 'Lokasi terlalu jauh dari halte TransJakarta. Maksimal jarak 2 km.'])
                ->withInput();
        }

        // Tambahkan info jarak ke nearest_stop (opsional untuk debugging)
        Log::info('POI baru: ' . $request->name . ' - jarak ke halte terdekat: ' . $nearest['distance_km'] . ' km');

        Poi::create([
            'name' => $request->name,
            'category' => $request->category,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'osm_id' => null, // bisa diisi manual jika perlu
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

        // Validasi jarak (kecuali jika tidak mengubah koordinat, tetap divalidasi ulang)
        $nearest = $this->poiService->findNearestStop($request->lat, $request->lng, 2000);
        if (!$nearest) {
            return back()
                ->withErrors(['lat' => 'Lokasi terlalu jauh dari halte TransJakarta. Maksimal jarak 2 km.'])
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
}
