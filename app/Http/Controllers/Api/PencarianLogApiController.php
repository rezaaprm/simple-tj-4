<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PencarianLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PencarianLogApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_halte_awal' => 'required|string',
                'id_halte_tujuan' => 'required|string',
                'waktu_eksekusi_ms' => 'required|numeric',
                'node_dikunjungi' => 'required|integer',
                'total_jarak' => 'required|numeric',
                'total_waktu' => 'required|integer',
                'total_pindah' => 'required|integer',
                'algoritma' => 'required|string|max:20',
                'preference' => 'nullable|string|max:20',
                'route_path_json' => 'nullable|string',
                'koridors_json' => 'nullable|string',
                'walking_info_json' => 'nullable|string',
            ]);

            $log = PencarianLog::create([
                'id_halte_awal' => $validated['id_halte_awal'],
                'id_halte_tujuan' => $validated['id_halte_tujuan'],
                'waktu_eksekusi_ms' => $validated['waktu_eksekusi_ms'],
                'node_dikunjungi' => $validated['node_dikunjungi'],
                'total_jarak' => $validated['total_jarak'],
                'total_waktu' => $validated['total_waktu'],
                'total_pindah' => $validated['total_pindah'],
                'algoritma' => $validated['algoritma'],
                'preference' => $validated['preference'] ?? null,
                'bobot_preferensi' => null,
                'route_path_json' => $validated['route_path_json'] ?? null,
                'koridors_json' => $validated['koridors_json'] ?? null,
                'walking_info_json' => $validated['walking_info_json'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Log pencarian berhasil disimpan',
                'data' => $log
            ], 201);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan log pencarian: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan log: ' . $e->getMessage()
            ], 500);
        }
    }
}
