<?php

namespace App\Services;

use App\Models\Stop;
use App\Models\PencarianLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PencarianRuteService
{
    protected $navigasiService;

    public function __construct(NavigasiService $navigasiService)
    {
        $this->navigasiService = $navigasiService;
    }

    public function cariRuteOptimal(Stop $asal, Stop $tujuan): array
    {
        $waktuMulai = microtime(true);

        if (!$asal || !$tujuan) {
            Log::warning('Halte tidak ditemukan');
            return $this->emptyResult();
        }

        Log::info('Mencari rute', [
            'dari' => $asal->stop_name . ' (ID: ' . $asal->stop_id . ')',
            'ke' => $tujuan->stop_name . ' (ID: ' . $tujuan->stop_id . ')'
        ]);

        $jalurId = $this->navigasiService->cariRuteTercepat($asal->stop_id, $tujuan->stop_id);

        if (empty($jalurId)) {
            Log::warning('Tidak ada jalur ditemukan');
            return $this->emptyResult();
        }

        // Data Stops sekali diambil
        $stops = Stop::whereIn('stop_id', $jalurId)->get()->keyBy('stop_id');

        $rute = $this->rekonstruksiRuteFromStops($jalurId, $stops);
        $totalJarak = $this->hitungTotalJarakFromStops($jalurId, $stops);

        $this->simpanLog([
            'id_halte_awal' => $asal->stop_id,
            'id_halte_tujuan' => $tujuan->stop_id,
            'waktu_eksekusi_ms' => (microtime(true) - $waktuMulai) * 1000,
            'node_dikunjungi' => count($jalurId),
            'total_jarak' => $totalJarak,
            'total_waktu' => $totalJarak / 8.33 * 3.6,
            'total_pindah' => 0, // TODO: hitung pindah koridor jika diperlukan
            'algoritma' => 'Dijkstra',
        ]);

        return [
            'rute' => $rute,
            'ringkasan' => [
                'total_halte' => $rute->count(),
                'total_waktu' => $totalJarak / 8.33 * 3.6,
                'total_waktu_menit' => round(($totalJarak / 8.33 * 3.6) / 60, 1),
                'total_pindah' => 0,
                'total_jarak' => $totalJarak,
                'total_jarak_km' => round($totalJarak / 1000, 2),
            ]
        ];
    }

    private function rekonstruksiRuteFromStops(array $jalurId, Collection $stops): Collection
    {
        $rute = collect();
        foreach ($jalurId as $index => $id) {
            if (isset($stops[$id])) {
                $s = $stops[$id];
                $rute->push([
                    'id' => $s->stop_id,
                    'nama' => $s->stop_name,
                    'lintang' => (float) $s->stop_lat,
                    'bujur' => (float) $s->stop_lon,
                    'urutan' => $index + 1
                ]);
            }
        }
        return $rute;
    }

    private function hitungTotalJarakFromStops(array $jalurId, Collection $stops): float
    {
        if (count($jalurId) < 2) return 0;
        $total = 0;
        for ($i = 0; $i < count($jalurId) - 1; $i++) {
            $dari = $stops[$jalurId[$i]] ?? null;
            $ke = $stops[$jalurId[$i + 1]] ?? null;
            if ($dari && $ke) {
                $total += $this->navigasiService->hitungJarak(
                    $dari->stop_lat,
                    $dari->stop_lon,
                    $ke->stop_lat,
                    $ke->stop_lon
                );
            }
        }
        return $total;
    }

    private function emptyResult(): array
    {
        return [
            'rute' => collect([]),
            'ringkasan' => [
                'total_halte' => 0,
                'total_waktu' => 0,
                'total_waktu_menit' => 0,
                'total_pindah' => 0,
                'total_jarak' => 0,
                'total_jarak_km' => 0,
            ]
        ];
    }

    private function simpanLog(array $data): void
    {
        try {
            PencarianLog::create($data);
        } catch (\Exception $e) {
            Log::warning('Gagal menyimpan log: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        $this->navigasiService->clearCache();
    }
}
