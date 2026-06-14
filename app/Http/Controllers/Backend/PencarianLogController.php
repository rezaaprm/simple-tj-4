<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PencarianLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PencarianLogController extends Controller
{
    public function index()
    {
        try {
            if (Auth::guard('admin')->check()) {
                $logs = PencarianLog::with(['halteAwal', 'halteTujuan'])
                    ->orderBy('created_at', 'asc')
                    ->paginate(20);
            } elseif (Auth::guard('users')->check()) {
                $logs = PencarianLog::with(['halteAwal', 'halteTujuan'])
                    ->where('user_id', Auth::guard('users')->id())
                    ->orderBy('created_at', 'asc')
                    ->paginate(20);
            } else {
                $logs = collect([]);
            }
        } catch (\Exception $e) {
            $logs = collect([]);
        }

        return view('layouts_backend.pencarian_log.index', compact('logs'));
    }

    public function data(Request $request)
    {
        try {
            if (Auth::guard('admin')->check()) {
                $logs = PencarianLog::with(['halteAwal', 'halteTujuan', 'user'])
                    ->orderBy('created_at', 'asc')
                    ->paginate(20);
            } else {
                $userId = Auth::guard('users')->id();
                if ($userId) {
                    $logs = PencarianLog::with(['halteAwal', 'halteTujuan'])
                        ->where('user_id', $userId)
                        ->orderBy('created_at', 'asc')
                        ->paginate(20);
                } else {
                    $logs = collect([]);
                }
            }
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
            $log = PencarianLog::with(['halteAwal', 'halteTujuan'])->find($id);

            if (!$log) {
                return response()->json([
                    'success' => false,
                    'message' => 'Log tidak ditemukan'
                ], 404);
            }

            // Gunakan data dari JSON jika ada
            $routePath = $log->route_path;
            $koridors = $log->koridors;
            $walkingInfo = $log->walking_info;

            // Jika tidak ada data JSON, gunakan data default
            if (!$routePath) {
                $routePath = [
                    ['order' => 1, 'name' => $log->halteAwal->stop_name ?? 'Unknown'],
                    ['order' => $log->node_dikunjungi ?? 2, 'name' => $log->halteTujuan->stop_name ?? 'Unknown']
                ];
            }

            if (!$koridors) {
                $koridors = [];
                for ($i = 0; $i <= ($log->total_pindah ?? 0); $i++) {
                    $koridors[] = [
                        'short_name' => ($i + 1) . ($i == 0 ? '' : ' (Transfer)'),
                        'color' => $this->getColorByIndex($i)
                    ];
                }
            }

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
                    'preference' => $log->preference ?? 'distance',
                    'route_path' => $routePath,
                    'koridors' => $koridors,
                    'walking_info' => $walkingInfo,
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

    /**
     * Get route data from session by log ID
     */
    public function getFromSession($id)
    {
        try {
            $lastRoute = Session::get('last_route_calculation', null);

            if (!$lastRoute) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data rute tidak ditemukan di session'
                ], 404);
            }

            $lastRoute['id'] = $id;
            $lastRoute['is_from_session'] = true;

            return response()->json([
                'success' => true,
                'data' => $lastRoute
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
