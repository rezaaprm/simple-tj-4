<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Stop;
use Illuminate\Http\Request;

class HalteController extends Controller
{
    public function index()
    {
        $halte = Stop::orderBy('stop_name')->paginate(100);

        // Ambil SEMUA data halte untuk map (seperti di Projek 5)
        $allStops = Stop::select('stop_id', 'stop_name', 'stop_lat', 'stop_lon')
            ->orderBy('stop_name')
            ->get()
            ->map(function ($stop) {
                return [
                    'id' => $stop->stop_id,
                    'name' => $stop->stop_name,
                    'lat' => (float)$stop->stop_lat,
                    'lng' => (float)$stop->stop_lon,
                ];
            });

        return view('layouts_backend.halte.index', compact('halte', 'allStops'));
    }

    public function data(Request $request)
    {
        $stops = Stop::orderBy('stop_name')->paginate(50);

        if ($request->ajax()) {
            return response()->json([
                'data' => $stops->map(fn($s) => [
                    'id' => $s->stop_id,
                    'nama' => $s->stop_name,
                    'kode' => '',
                    'lintang' => $s->stop_lat,
                    'bujur' => $s->stop_lon,
                    'id_gtfs' => $s->stop_id
                ]),
                'next_page_url' => $stops->nextPageUrl()
            ]);
        }

        return view('layouts_backend.halte.data', compact('stops'));
    }
}
