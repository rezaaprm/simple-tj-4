<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class FrequencySeeder extends Seeder
{
    public function run()
    {
        $path = public_path('gtfs/transjakarta/frequencies.txt');

        if (!file_exists($path)) {
            $this->command->error('File frequencies.txt tidak ditemukan di ' . $path);
            return;
        }

        $this->command->info('Membaca frequencies.txt...');

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $counter = 0;

        foreach ($csv as $record) {
            $data[] = [
                'trip_id'      => $record['trip_id'],
                'start_time'   => $record['start_time'],
                'end_time'     => $record['end_time'],
                'headway_secs' => (int) $record['headway_secs'],
                'exact_times'  => (int) ($record['exact_times'] ?? 0),
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
            $counter++;

            if (count($data) >= 1000) {
                DB::table('tb_frequencies')->insertOrIgnore($data);
                $data = [];
                $this->command->getOutput()->write('.');
            }
        }

        if (!empty($data)) {
            DB::table('tb_frequencies')->insertOrIgnore($data);
        }

        $this->command->newLine();
        $this->command->info("FrequencySeeder selesai! Total: {$counter} record.");
    }
}
