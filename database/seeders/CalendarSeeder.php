<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class CalendarSeeder extends Seeder
{
    public function run()
    {
        $path = public_path('gtfs/transjakarta/calendar.txt');

        if (!file_exists($path)) {
            $this->command->error('File calendar.txt tidak ditemukan');
            return;
        }

        $this->command->info('Membaca calendar.txt.');

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        foreach ($csv as $record) {
            $data[] = [
                'service_id' => $record['service_id'],
                'monday' => $record['monday'],
                'tuesday' => $record['tuesday'],
                'wednesday' => $record['wednesday'],
                'thursday' => $record['thursday'],
                'friday' => $record['friday'],
                'saturday' => $record['saturday'],
                'sunday' => $record['sunday'],
                'start_date' => $record['start_date'],
                'end_date' => $record['end_date'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Hapus data lama (kalau ada)
        DB::table('tb_calendar')->truncate();

        // Insert data baru
        DB::table('tb_calendar')->insert($data);

        $this->command->info('CalendarSeeder selesai, Total: ' . count($data) . ' record');
    }
}
