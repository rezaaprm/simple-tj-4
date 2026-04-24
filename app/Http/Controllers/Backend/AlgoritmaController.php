<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\PencarianLog; // <-- TAMBAHKAN INI

class AlgoritmaController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data rute terakhir dari session (jika ada)
        $lastRoute = Session::get('last_route_calculation', null);

        // Jika ada parameter rute dari request, simpan ke session
        if ($request->has('route_data')) {
            Session::put('last_route_calculation', $request->route_data);
            $lastRoute = $request->route_data;
        }

        // ==================== TAMBAHKAN INI UNTUK LOG ====================
        // Ambil data dari log pencarian jika ada parameter log_id
        $logData = null;
        $logId = $request->get('log_id');

        if ($logId) {
            try {
                $log = PencarianLog::with(['halteAwal', 'halteTujuan'])->find($logId);

                if ($log) {
                    // Format data log agar sesuai dengan format yang digunakan di view
                    $logData = [
                        'id' => $log->id,
                        'start_stop' => $log->halteAwal->stop_name ?? 'Unknown',
                        'end_stop' => $log->halteTujuan->stop_name ?? 'Unknown',
                        'total_distance' => $log->total_jarak,
                        'total_stops' => $log->node_dikunjungi,
                        'total_transfers' => $log->total_pindah,
                        'execution_time' => $log->waktu_eksekusi_ms,
                        'timestamp' => $log->created_at->toDateTimeString(),
                        // Buat route_path dari halte awal dan tujuan (karena tidak ada detail halte per langkah di log)
                        'route_path' => [
                            ['name' => $log->halteAwal->stop_name ?? 'Unknown', 'order' => 1],
                            ['name' => $log->halteTujuan->stop_name ?? 'Unknown', 'order' => $log->node_dikunjungi ?? 2]
                        ],
                        // Buat koridors dari total_pindah
                        'koridors' => $this->generateKoridorsFromLog($log),
                        'is_from_log' => true
                    ];
                }
            } catch (\Exception $e) {
                // Log error jika ada
                \Illuminate\Support\Facades\Log::error('Error loading log: ' . $e->getMessage());
            }
        }

        // Parameter algoritma Dijkstra
        $params = [
            'transfer_penalty' => 2500,
            'max_walking' => 300,
            'bus_weight' => 800,
            'long_bus_threshold' => 4000,
            'long_bus_penalty' => 100000,
            'walk_multiplier_short' => 5,
            'walk_multiplier_long' => 50,
            'bus_speed' => 25,
            'walk_speed' => 5,
        ];

        return view('layouts_backend.algoritma.index', compact('lastRoute', 'params', 'logData'));
    }

    /**
     * Generate koridor dari data log
     */
    private function generateKoridorsFromLog($log)
    {
        $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#34495e'];
        $koridors = [];

        $totalTransfers = $log->total_pindah ?? 0;

        for ($i = 0; $i <= $totalTransfers; $i++) {
            $koridors[] = [
                'id' => 'log_' . ($i + 1),
                'short_name' => ($i + 1) . ($i == 0 ? '' : ' (Transfer)'),
                'long_name' => $i == 0 ? 'Rute utama' : 'Transfer ke koridor lain',
                'color' => $colors[$i % count($colors)]
            ];
        }

        // Jika tidak ada koridor, tambahkan default
        if (empty($koridors)) {
            $koridors[] = [
                'id' => 'log_default',
                'short_name' => '1',
                'long_name' => 'Rute dari log',
                'color' => '#3498db'
            ];
        }

        return $koridors;
    }

    /**
     * API untuk menerima data rute dari peta
     */
    public function storeRouteCalculation(Request $request)
    {
        $validated = $request->validate([
            'start_stop' => 'required|string',
            'end_stop' => 'required|string',
            'total_distance' => 'required|numeric',
            'total_stops' => 'required|integer',
            'total_transfers' => 'required|integer',
            'execution_time' => 'required|numeric',
            'route_path' => 'required|array',
            'koridors' => 'required|array'
        ]);

        Session::put('last_route_calculation', $validated);

        return response()->json([
            'success' => true,
            'message' => 'Data rute tersimpan'
        ]);
    }

    /**
     * API untuk mengambil data rute terakhir
     */
    public function getLastRouteData()
    {
        $lastRoute = Session::get('last_route_calculation', null);
        return response()->json([
            'success' => true,
            'data' => $lastRoute
        ]);
    }

    public function resetLastRoute()
    {
        Session::forget('last_route_calculation');
        return response()->json(['success' => true]);
    }
}
