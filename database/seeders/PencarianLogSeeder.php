<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PencarianLogSeeder extends Seeder
{
    public function run()
    {
        if ($this->command) {
            $this->command->info('Import PencarianLogSeeder');
        }

        // langsung insert kalau kosong, tanpa truncate
        if (DB::table('tb_pencarian_log')->count() > 0) {
            if ($this->command) {
                $this->command->warn('Tabel tb_pencarian_log sudah berisi, Seeder dilewati');
            }
            return;
        }

        $stops = DB::table('tb_stops')->limit(20)->pluck('stop_id')->toArray();

        if (empty($stops)) {
            if ($this->command) {
                $this->command->warn('Tidak ada data stops, lewati');
            }
            return;
        }

        $data = [];
        $totalLogs = 50;

        for ($i = 0; $i < $totalLogs; $i++) {
            $awal = $stops[array_rand($stops)];
            $tujuan = $stops[array_rand($stops)];

            while ($awal == $tujuan) {
                $tujuan = $stops[array_rand($stops)];
            }

            $jarak = rand(1000, 15000);
            $waktu = $jarak / 8.33 * 3.6;
            $pindah = rand(0, 3);

            $data[] = [
                'id_halte_awal' => $awal,
                'id_halte_tujuan' => $tujuan,
                'waktu_eksekusi_ms' => rand(50, 500) + ($pindah * 50),
                'node_dikunjungi' => rand(10, 200),
                'total_jarak' => $jarak,
                'total_waktu' => $waktu,
                'total_pindah' => $pindah,
                'algoritma' => 'Dijkstra',
                'bobot_preferensi' => json_encode([
                    'bobot_waktu' => 0.4,
                    'bobot_jarak' => 0.3,
                    'bobot_pindah' => 0.3
                ]),
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
                'updated_at' => now(),
            ];

            if (($i + 1) % 10 == 0 && $this->command) {
                $this->command->getOutput()->write('.');
            }
        }

        DB::table('tb_pencarian_log')->insert($data);

        $total = DB::table('tb_pencarian_log')->count();
        if ($this->command) {
            $this->command->newLine();
            $this->command->info("PencarianLogSeeder selesai! Total: $total logs (dummy data)");
        }
    }
}
