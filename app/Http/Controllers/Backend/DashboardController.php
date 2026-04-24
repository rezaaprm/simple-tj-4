<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\PencarianLog;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlah_koridor = Route::count();
        $jumlah_halte = Stop::count();
        $jumlah_perjalanan = Trip::count();

        try {
            $jumlah_pencarian = PencarianLog::count();
            $rata_waktu = PencarianLog::avg('waktu_eksekusi_ms');
            $pencarian_hari_ini = PencarianLog::whereDate('created_at', today())->count();
        } catch (\Exception $e) {
            $jumlah_pencarian = 0;
            $rata_waktu = 0;
            $pencarian_hari_ini = 0;
        }

        return view('layouts_backend.dashboard', compact(
            'jumlah_koridor',
            'jumlah_halte',
            'jumlah_perjalanan',
            'jumlah_pencarian',
            'rata_waktu',
            'pencarian_hari_ini'
        ));
    }
}
