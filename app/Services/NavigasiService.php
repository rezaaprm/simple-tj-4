<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class NavigasiService
{
    /**
     * Hitung jarak Haversine antara dua koordinat (meter)
     */
    public function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Cari rute tercepat antara dua halte
     * 
     * @param string $stopIdAwal ID halte awal
     * @param string $stopIdTujuan ID halte tujuan
     * @return array Daftar ID halte dalam urutan perjalanan
     */
    public function cariRuteTercepat($stopIdAwal, $stopIdTujuan)
    {
        // Ambil graf dari cache (dibangun setiap 24 jam)
        $graf = Cache::remember('graf_navigasi', 86400, function () {
            return $this->bangunGraf();
        });

        // Validasi node ada di graf
        if (!isset($graf[$stopIdAwal]) || !isset($graf[$stopIdTujuan])) {
            return [];
        }

        // Algoritma Dijkstra
        $jarak = [];
        $sebelumnya = [];
        $antrian = new \SplPriorityQueue();

        foreach (array_keys($graf) as $node) {
            $jarak[$node] = INF;
            $sebelumnya[$node] = null;
        }

        $jarak[$stopIdAwal] = 0;
        $antrian->insert($stopIdAwal, 0);

        while (!$antrian->isEmpty()) {
            $sekarang = $antrian->extract();

            if ($sekarang === $stopIdTujuan) {
                break;
            }

            if (isset($graf[$sekarang])) {
                foreach ($graf[$sekarang] as $tetangga) {
                    $alt = $jarak[$sekarang] + $tetangga['bobot'];

                    if ($alt < $jarak[$tetangga['id']]) {
                        $jarak[$tetangga['id']] = $alt;
                        $sebelumnya[$tetangga['id']] = $sekarang;
                        $antrian->insert($tetangga['id'], -$alt);
                    }
                }
            }
        }

        // Susun jalur dari hasil Dijkstra
        return $this->susunJalur($sebelumnya, $stopIdTujuan);
    }

    /**
     * Bangun graf dari database (koneksi antar halte)
     */
    private function bangunGraf()
    {
        $graf = [];

        // Ambil semua koordinat halte
        $semuaHalte = DB::table('tb_stops')->select('stop_id', 'stop_lat', 'stop_lon')->get();
        $koordinat = [];
        foreach ($semuaHalte as $h) {
            $koordinat[$h->stop_id] = [
                'lat' => $h->stop_lat,
                'lon' => $h->stop_lon
            ];
        }

        // Ambil semua koneksi antar halte dari stop_times
        // (halte yang berurutan dalam satu trip)
        $koneksiHalte = DB::table('tb_stop_times as st1')
            ->join('tb_stop_times as st2', function ($join) {
                $join->on('st1.trip_id', '=', 'st2.trip_id')
                    ->on(DB::raw('st1.stop_sequence + 1'), '=', 'st2.stop_sequence');
            })
            ->select('st1.stop_id as awal', 'st2.stop_id as tujuan')
            ->distinct()
            ->get();

        foreach ($koneksiHalte as $kon) {
            if (!isset($koordinat[$kon->awal]) || !isset($koordinat[$kon->tujuan])) {
                continue;
            }

            // Hitung bobot = jarak antar halte (meter)
            $bobot = $this->hitungJarak(
                $koordinat[$kon->awal]['lat'],
                $koordinat[$kon->awal]['lon'],
                $koordinat[$kon->tujuan]['lat'],
                $koordinat[$kon->tujuan]['lon']
            );

            // Tambah koneksi (dua arah)
            if (!isset($graf[$kon->awal])) {
                $graf[$kon->awal] = [];
            }
            $graf[$kon->awal][] = [
                'id' => $kon->tujuan,
                'bobot' => $bobot
            ];

            if (!isset($graf[$kon->tujuan])) {
                $graf[$kon->tujuan] = [];
            }
            $graf[$kon->tujuan][] = [
                'id' => $kon->awal,
                'bobot' => $bobot
            ];
        }

        return $graf;
    }

    /**
     * Susun jalur dari hasil Dijkstra
     */
    private function susunJalur($sebelumnya, $tujuan)
    {
        $jalur = [];
        $u = $tujuan;

        while ($u !== null) {
            array_unshift($jalur, $u);
            $u = $sebelumnya[$u] ?? null;
        }

        // Jika jalur hanya berisi 1 elemen (tujuan saja), berarti tidak ditemukan rute
        if (count($jalur) <= 1) {
            return [];
        }

        return $jalur;
    }

    /**
     * Hapus cache graf navigasi
     */
    public function clearCache()
    {
        Cache::forget('graf_navigasi');
    }
}
