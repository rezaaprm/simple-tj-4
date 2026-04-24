<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ShapeSeeder extends Seeder
{
    public function run()
    {
        if ($this->command) {
            $this->command->info('🚀 Import ShapeSeeder dimulai...');
        }

        $path = public_path('gtfs/transjakarta/shapes.txt');
        if (!file_exists($path)) {
            if ($this->command) {
                $this->command->error('❌ File shapes.txt tidak ditemukan!');
            }
            return;
        }

        // HAPUS BARIS INI!
        // DB::table('tb_shapes')->truncate();

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $counter = 0;
        $batchSize = 1000;

        foreach ($csv as $index => $record) {
            $shapeId = $record['shape_id'] ?? '';
            $lat = $record['shape_pt_lat'] ?? 0;
            $lon = $record['shape_pt_lon'] ?? 0;
            $sequence = $record['shape_pt_sequence'] ?? 0;

            if (empty($shapeId) || $lat == 0 || $lon == 0) {
                continue;
            }

            $data[] = [
                'shape_id' => $shapeId,
                'shape_pt_lat' => $lat,
                'shape_pt_lon' => $lon,
                'shape_pt_sequence' => $sequence,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $counter++;

            if (count($data) >= $batchSize) {
                DB::table('tb_shapes')->insertOrIgnore($data);
                $data = [];
                if ($this->command) {
                    $this->command->getOutput()->write('.');
                }
            }

            if ($counter % 20000 == 0 && $this->command) {
                $this->command->line("  ... sudah $counter baris");
            }
        }

        if (!empty($data)) {
            DB::table('tb_shapes')->insertOrIgnore($data);
        }

        $total = DB::table('tb_shapes')->count();
        if ($this->command) {
            $this->command->newLine();
            $this->command->info("✅ ShapeSeeder selesai! Total: " . number_format($total) . " titik shapes");
        }
    }
}
