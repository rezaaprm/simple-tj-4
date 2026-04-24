<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Trip;
use App\Models\Shape;

class KoridorController extends Controller
{
    public function shapes($id)
    {
        try {
            $route = Route::find($id);
            if (!$route) {
                return response()->json(['error' => 'Not found'], 404);
            }

            $trip = Trip::where('route_id', $route->route_id)->first();
            if (!$trip || !$trip->shape_id) {
                return response()->json(['error' => 'No shape'], 404);
            }

            $shapes = Shape::where('shape_id', $trip->shape_id)
                ->orderBy('shape_pt_sequence')
                ->get()
                ->map(fn($s) => [(float) $s->shape_pt_lat, (float) $s->shape_pt_lon])
                ->toArray();

            return response()->json([
                'data' => [
                    'id' => $route->route_id,
                    'nama' => $route->route_short_name,
                    'warna' => $route->route_color ?: '#3498db',
                    'shape' => $shapes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
