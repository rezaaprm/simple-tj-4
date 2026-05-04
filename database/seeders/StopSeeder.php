<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class StopSeeder extends Seeder
{
    public function run()
    {
        if ($this->command) {
            $this->command->info('Import StopSeeder');
        }

        $path = public_path('gtfs/transjakarta/stops.txt');
        if (!file_exists($path)) {
            if ($this->command) {
                $this->command->error('File stops.txt tidak ditemukan');
            }
            return;
        }


        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        foreach ($csv as $index => $record) {
            $data[] = [
                'stop_id' => $record['stop_id'],
                'stop_name' => $record['stop_name'],
                'stop_lat' => $record['stop_lat'],
                'stop_lon' => $record['stop_lon'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 500) {
                DB::table('tb_stops')->insertOrIgnore($data);
                $data = [];
                if ($this->command) {
                    $this->command->getOutput()->write('.');
                }
            }
        }

        if (!empty($data)) {
            DB::table('tb_stops')->insertOrIgnore($data);
        }

        $total = DB::table('tb_stops')->count();
        if ($this->command) {
            $this->command->newLine();
            $this->command->info("StopSeeder selesai, Total $total stops");
        }
    }
}
