<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class TripSeeder extends Seeder
{
    public function run()
    {
        if ($this->command) {
            $this->command->info('Import TripSeeder');
        }

        $path = public_path('gtfs/transjakarta/trips.txt');
        if (!file_exists($path)) {
            if ($this->command) {
                $this->command->error('File trips.txt tidak ditemukan');
            }
            return;
        }

        // HAPUS BARIS INI!
        // DB::table('tb_trips')->truncate();

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        foreach ($csv as $index => $record) {
            $data[] = [
                'trip_id' => $record['trip_id'],
                'route_id' => $record['route_id'],
                'service_id' => $record['service_id'] ?? '',
                'trip_headsign' => $record['trip_headsign'] ?? '',
                'direction_id' => $record['direction_id'] ?? 0,
                'shape_id' => $record['shape_id'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 100) {
                DB::table('tb_trips')->insertOrIgnore($data);
                $data = [];
                if ($this->command) {
                    $this->command->getOutput()->write('.');
                }
            }
        }

        if (!empty($data)) {
            DB::table('tb_trips')->insertOrIgnore($data);
        }

        $total = DB::table('tb_trips')->count();
        if ($this->command) {
            $this->command->newLine();
            $this->command->info("TripSeeder selesai, Total $total trips");
        }
    }
}
