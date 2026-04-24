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
        ]);

        try {
            $halteAwal = Stop::find($request->id_halte_awal);
            $halteTujuan = Stop::find($request->id_halte_tujuan);

            $hasil = $this->pencarianRute->cariRuteOptimal($halteAwal, $halteTujuan);

            return response()->json($hasil);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
