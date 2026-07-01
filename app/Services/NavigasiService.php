<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use SplPriorityQueue;

class NavigasiService
{
    const BUS_SPEED_MPS = 6.94;      // 25 km/jam
    const WALK_SPEED_MPS = 1.4;      // 5 km/jam
    const DEFAULT_TRANSFER_WAIT = 300; // 5 menit

    /**
     * Hitung jarak Haversine (meter)
     */
    public function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Cari rute dengan Dijkstra
     *
     * @param string $stopIdAwal
     * @param string $stopIdTujuan
     * @param bool   $modeWaktu  true = Cari 2 (optimasi waktu), false = Cari 1 (optimasi jarak)
     * @return array
     */
    public function cariRuteTercepat($stopIdAwal, $stopIdTujuan, $modeWaktu = false)
    {
        $cacheKey = $modeWaktu ? 'graf_navigasi_waktu' : 'graf_navigasi_jarak';

        $grafData = Cache::remember($cacheKey, 86400, function () use ($modeWaktu) {
            return $this->bangunGrafVirtual($modeWaktu);
        });

        $graf = $grafData['graf'];
        $virtualToReal = $grafData['virtual_to_real'];
        $realToVirtual = $grafData['real_to_virtual'];

        if (!isset($realToVirtual[$stopIdAwal]) || !isset($realToVirtual[$stopIdTujuan])) {
            return ['jalur' => [], 'rute_terpakai' => [], 'total_bobot' => 0];
        }

        // Dijkstra
        $jarak = [];
        $sebelumnya = [];
        $antrian = new SplPriorityQueue();

        foreach (array_keys($graf) as $node) {
            $jarak[$node] = INF;
            $sebelumnya[$node] = null;
        }

        foreach ($realToVirtual[$stopIdAwal] as $vAwal) {
            $jarak[$vAwal] = 0;
            $antrian->insert($vAwal, 0);
        }

        $tujuanVirtual = null;

        while (!$antrian->isEmpty()) {
            $sekarang = $antrian->extract();

            if (in_array($sekarang, $realToVirtual[$stopIdTujuan])) {
                $tujuanVirtual = $sekarang;
                break;
            }

            if (!isset($graf[$sekarang])) continue;

            foreach ($graf[$sekarang] as $tetangga => $bobot) {
                $alt = $jarak[$sekarang] + $bobot;
                if ($alt < $jarak[$tetangga]) {
                    $jarak[$tetangga] = $alt;
                    $sebelumnya[$tetangga] = $sekarang;
                    $antrian->insert($tetangga, -$alt);
                }
            }
        }

        if (!$tujuanVirtual) {
            return ['jalur' => [], 'rute_terpakai' => [], 'total_bobot' => 0];
        }

        // Rekonstruksi
        $jalurVirtual = [];
        $u = $tujuanVirtual;
        while ($u !== null) {
            array_unshift($jalurVirtual, $u);
            $u = $sebelumnya[$u] ?? null;
        }

        $jalurAsli = [];
        $ruteTerpakai = [];
        foreach ($jalurVirtual as $vNode) {
            $realId = $virtualToReal[$vNode]['stop_id'];
            $routeId = $virtualToReal[$vNode]['route_id'];
            if (empty($jalurAsli) || end($jalurAsli) !== $realId) {
                $jalurAsli[] = $realId;
            }
            $ruteTerpakai[$realId] = $routeId;
        }

        $totalBobot = $jarak[$tujuanVirtual] ?? 0;

        return [
            'jalur' => $jalurAsli,
            'rute_terpakai' => $ruteTerpakai,
            'total_bobot' => $totalBobot,
        ];
    }

    /**
     * Bangun graf virtual (stop@route) dengan bobot berbasis jarak atau waktu
     *
     * @param bool $modeWaktu  true = bobot dalam detik (Cari 2), false = bobot dalam meter (Cari 1)
     * @return array
     */
    private function bangunGrafVirtual($modeWaktu)
    {
        $graf = [];
        $virtualToReal = [];
        $realToVirtual = [];

        // 1. Koordinat halte
        $stops = DB::table('tb_stops')->select('stop_id', 'stop_lat', 'stop_lon')->get();
        $koordinat = [];
        foreach ($stops as $s) {
            $koordinat[$s->stop_id] = ['lat' => $s->stop_lat, 'lon' => $s->stop_lon];
        }

        // 2. Data headway (hanya untuk mode waktu)
        $headways = [];
        if ($modeWaktu) {
            $freqs = DB::table('tb_frequencies')
                ->select('trip_id', 'headway_secs')
                ->get();
            foreach ($freqs as $f) {
                $headways[$f->trip_id] = (int) $f->headway_secs;
            }
        }

        // 3. Ambil segmen perjalanan (stop_sequence berurutan)
        // Menggunakan subquery untuk mengunci hanya 1 trip_id terkecil per route_id & direction_id
        // Ini menyatukan kembali pipa jalur makro (seperti Koridor 1, 10, & 7A) yang sempat patah
        $subQueryTrips = DB::table('tb_trips')
            ->select('route_id', DB::raw('MIN(trip_id) as trip_id'))
            ->groupBy('route_id', 'direction_id');

        $segments = DB::table('tb_stop_times as st1')
            ->join('tb_stop_times as st2', function ($join) {
                $join->on('st1.trip_id', '=', 'st2.trip_id')
                    ->on(DB::raw('st1.stop_sequence + 1'), '=', 'st2.stop_sequence');
            })
            ->joinSub($subQueryTrips, 't_valid', function ($join) {
                $join->on('st1.trip_id', '=', 't_valid.trip_id');
            })
            ->join('tb_trips as t', 'st1.trip_id', '=', 't.trip_id')
            ->select('st1.stop_id as awal', 'st2.stop_id as tujuan', 't.route_id', 'st1.trip_id')
            ->distinct()
            ->get();
        // =========================================================================


        // 4. Buat edge perjalanan (dalam satu koridor)
        foreach ($segments as $seg) {
            $vAwal = $seg->awal . '@' . $seg->route_id;
            $vTujuan = $seg->tujuan . '@' . $seg->route_id;

            // Simpan mapping ke real ID (stop_id, route_id, trip_id untuk headway)
            $virtualToReal[$vAwal] = [
                'stop_id' => $seg->awal,
                'route_id' => $seg->route_id,
                'trip_id' => $seg->trip_id,
            ];
            $virtualToReal[$vTujuan] = [
                'stop_id' => $seg->tujuan,
                'route_id' => $seg->route_id,
                'trip_id' => $seg->trip_id,
            ];

            $realToVirtual[$seg->awal][] = $vAwal;
            $realToVirtual[$seg->tujuan][] = $vTujuan;

            if (!isset($koordinat[$seg->awal]) || !isset($koordinat[$seg->tujuan])) continue;

            $jarak = $this->hitungJarak(
                $koordinat[$seg->awal]['lat'],
                $koordinat[$seg->awal]['lon'],
                $koordinat[$seg->tujuan]['lat'],
                $koordinat[$seg->tujuan]['lon']
            );

            // Bobot perjalanan
            if ($modeWaktu) {
                // Cari 2: waktu tempuh (detik)
                $bobot = $jarak / self::BUS_SPEED_MPS;
            } else {
                // Cari 1: jarak (meter)
                $bobot = $jarak;
            }

            $graf[$vAwal][$vTujuan] = $bobot;
        }

        // 5. Buat edge transfer antar koridor di halte yang sama (DIPERBAIKI UNTUK RUTE PENDEK)
        foreach ($realToVirtual as $stopId => $vNodes) {
            $vNodes = array_unique($vNodes);
            if (count($vNodes) <= 1) continue;

            foreach ($vNodes as $v1) {
                foreach ($vNodes as $v2) {
                    if ($v1 === $v2) continue;

                    // Ambil route_id untuk deteksi keluarga koridor
                    $routeId1 = $virtualToReal[$v1]['route_id'] ?? '';
                    $routeId2 = $virtualToReal[$v2]['route_id'] ?? '';
                    $core1 = $this->getCoreRoute($routeId1);
                    $core2 = $this->getCoreRoute($routeId2);
                    $isSameFamily = ($core1 !== null && $core2 !== null && $core1 === $core2);

                    // JANGAN TIMPA jika jalur utama asli antar peron sudah ada di graf
                    if (isset($graf[$v1][$v2]) && !$isSameFamily) {
                        continue;
                    }

                    if ($modeWaktu) {
                        $tripId1 = $virtualToReal[$v1]['trip_id'] ?? null;
                        $tripId2 = $virtualToReal[$v2]['trip_id'] ?? null;
                        $headway1 = $headways[$tripId1] ?? self::DEFAULT_TRANSFER_WAIT;
                        $headway2 = $headways[$tripId2] ?? self::DEFAULT_TRANSFER_WAIT;

                        if ($isSameFamily) {
                            $bobotTransfer = 0;
                        } else {
                            $bobotTransfer = max($headway1, $headway2) / 2 + 60;
                        }
                        $graf[$v1][$v2] = $bobotTransfer;
                    } else {
                        if ($isSameFamily) {
                            $graf[$v1][$v2] = 0;
                        } else {
                            $graf[$v1][$v2] = 50;
                        }
                    }
                }
            }
        }

        return [
            'graf' => $graf,
            'virtual_to_real' => $virtualToReal,
            'real_to_virtual' => $realToVirtual,
        ];
    }

    /**
     * Hapus cache graf navigasi
     */
    public function clearCache()
    {
        Cache::forget('graf_navigasi_jarak');
        Cache::forget('graf_navigasi_waktu');
    }

    /**
     * Ambil angka dasar dari route_id (misal 13 dari 13B, 10 dari 10D)
     */
    // SEBELUMNYA: private function getCoreRoute($routeId)
    public function getCoreRoute($routeId)
    {
        if (preg_match('/^(\d+)/', $routeId, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
