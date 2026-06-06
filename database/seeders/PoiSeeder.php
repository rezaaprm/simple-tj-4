<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poi;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class PoiSeeder extends Seeder
{
    public function run()
    {
        // Gunakan file CSV (bukan Excel)
        $filePath = storage_path('app/poi_jakarta.csv');

        if (!file_exists($filePath)) {
            $this->command->error('File CSV tidak ditemukan di ' . $filePath);
            $this->command->info('File ada di folder storage/app/poi_jakarta.csv');
            return;
        }

        $this->command->info('Membaca file: ' . $filePath);

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);

        // Header CSV: X,Y,osm_id,code,fclass,name
        $data = [];
        $batchSize = 500;
        $count = 0;

        DB::beginTransaction();

        foreach ($csv as $index => $row) {
            // Skip jika name kosong
            if (empty($row['name']) && empty($row['fclass'])) {
                continue;
            }

            $name = !empty($row['name']) ? $row['name'] : $row['fclass'];

            $data[] = [
                'name' => $name,
                'category' => $row['fclass'] ?? 'unknown',
                'lat' => (float) $row['Y'],  // Y = latitude
                'lng' => (float) $row['X'],  // X = longitude
                'osm_id' => trim($row['osm_id'] ?? '', '"'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $count++;

            if (count($data) >= $batchSize) {
                Poi::insert($data);
                $data = [];
                $this->command->info("Imported {$count} records.");
            }
        }

        // Insert sisa data
        if (!empty($data)) {
            Poi::insert($data);
        }

        DB::commit();

        $total = Poi::count();
        $this->command->newLine();
        $this->command->info("POI Seeder selesai, Total {$total} POI diimport.");
    }
}
