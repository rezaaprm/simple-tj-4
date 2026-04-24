<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stop;
use Illuminate\Http\Request;

class HalteController extends Controller
{
    public function cari(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'data' => []
                ]);
            }

            $stops = Stop::where('stop_name', 'LIKE', "%{$query}%")
                ->orWhere('stop_id', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get();

            return response()->json([
                'data' => $stops->map(fn($s) => [
                    'id' => $s->stop_id,
                    'id_gtfs' => $s->stop_id,
                    'nama' => $s->stop_name,
                    'lintang' => (float) $s->stop_lat,
                    'bujur' => (float) $s->stop_lon
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
