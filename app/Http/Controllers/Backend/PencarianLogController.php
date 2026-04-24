<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PencarianLog;
use Illuminate\Http\Request;

class PencarianLogController extends Controller
{
    public function index()
    {
        try {
            $logs = PencarianLog::with(['halteAwal', 'halteTujuan'])
                ->orderBy('created_at', 'asc') // Ubah 'desc' atau 'asc'
                ->paginate(20);
        } catch (\Exception $e) {
            $logs = collect([]);
        }

        return view('layouts_backend.pencarian_log.index', compact('logs'));
    }

    public function data(Request $request)
    {
        try {
            $logs = PencarianLog::with(['halteAwal', 'halteTujuan'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'message' => 'Tabel log belum tersedia']);
        }
    }

    /**
     * Get log detail by ID for API
     */
    public function getLogDetail($id)
    {
        try {
            $log = PencarianLog::with(['halteAwal', 'halteTujuan'])
                ->find($id);

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log tidak ditemukan'
                ], 404);
            }

            // Hitung koridor yang dilewati (simulasi)
            // Karena di log tidak menyimpan koridor, kita hitung dari jarak dan pindah
            $koridors = [];
            for ($i = 0; $i <= ($log->total_pindah ?? 0); $i++) {
                $koridors[] = [
                    'id' => 'log_' . ($i + 1),
                    'short_name' => ($i + 1) . ($i == 0 ? ' (Awal)' : ' (Transfer ' . $i . ')'),
                    'long_name' => 'Dari log ID #' . $log->id,
                    'color' => $this->getColorByIndex($i)
                ];
            }

            // Buat route path simulasi (dari halte awal ke tujuan)
            $routePath = [
                [
                    'id' => $log->id_halte_awal,
                    'name' => $log->halteAwal->stop_name ?? 'Unknown',
                    'order' => 1
                ],
                [
                    'id' => $log->id_halte_tujuan,
                    'name' => $log->halteTujuan->stop_name ?? 'Unknown',
                    'order' => $log->node_dikunjungi ?? 2
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $log->id,
                    'start_stop' => $log->halteAwal->stop_name ?? 'Unknown',
                    'end_stop' => $log->halteTujuan->stop_name ?? 'Unknown',
                    'total_distance' => $log->total_jarak,
                    'total_stops' => $log->node_dikunjungi,
                    'total_transfers' => $log->total_pindah,
                    'execution_time' => $log->waktu_eksekusi_ms,
                    'route_path' => $routePath,
                    'koridors' => $koridors,
                    'timestamp' => $log->created_at->toDateTimeString(),
                    'is_from_log' => true
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getColorByIndex($index)
    {
        $colors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#34495e'];
        return $colors[$index % count($colors)];
    }
}
