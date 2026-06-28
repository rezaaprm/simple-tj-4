<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stop;
use App\Services\PencarianRuteService;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    protected $pencarianRute;

    public function __construct(PencarianRuteService $pencarianRute)
    {
        $this->pencarianRute = $pencarianRute;
    }

    public function cariRute(Request $request)
    {
        $request->validate([
            'id_halte_awal' => 'required',
            'id_halte_tujuan' => 'required|different:id_halte_awal',
            'cari_type' => 'sometimes|in:1,2' // 1 = jarak, 2 = minim transfer
        ]);

        $halteAwal = Stop::find($request->id_halte_awal);
        $halteTujuan = Stop::find($request->id_halte_tujuan);

        // Kirim parameter cari_type ke service
        $hasil = $this->pencarianRute->cariRuteOptimal($halteAwal, $halteTujuan);

        return response()->json($hasil);
    }
}
