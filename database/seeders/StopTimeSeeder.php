<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class StopTimeSeeder extends Seeder
{
    public function run()
    {
        if ($this->command) {
            $this->command->info('🚀 Import StopTimeSeeder dimulai (ini akan lama)...');
        }

        $path = public_path('gtfs/transjakarta/stop_times.txt');
        if (!file_exists($path)) {
            if ($this->command) {
                $this->command->error('❌ File stop_times.txt tidak ditemukan!');
            }
            return;
        }

        // HAPUS BARIS INI!
        // DB::table('tb_stop_times')->truncate();

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $counter = 0;

        foreach ($csv as $index => $record) {
            $data[] = [
                'trip_id' => $record['trip_id'],
                'stop_id' => $record['stop_id'],
                'stop_sequence' => $record['stop_sequence'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $counter++;

            if (count($data) >= 2000) {
                DB::table('tb_stop_times')->insertOrIgnore($data);
                $data = [];
                if ($this->command) {
                    $this->command->getOutput()->write('.');
                }
            }

            if ($counter % 10000 == 0 && $this->command) {
                $this->command->line("  ... sudah $counter baris");
            }
        }

        if (!empty($data)) {
            DB::table('tb_stop_times')->insertOrIgnore($data);
        }

        $total = DB::table('tb_stop_times')->count();
        if ($this->command) {
            $this->command->newLine();
            $this->command->info("✅ StopTimeSeeder selesai! Total: $total stop times");
        }
    }
}
