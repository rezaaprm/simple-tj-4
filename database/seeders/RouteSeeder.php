<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class RouteSeeder extends Seeder
{
    public function run()
    {
        if ($this->command) {
            $this->command->info('Import RouteSeeder');
        }

        $path = public_path('gtfs/transjakarta/routes.txt');
        if (!file_exists($path)) {
            if ($this->command) {
                $this->command->error('File routes.txt tidak ditemukan');
            }
            return;
        }

        // Gunakan insertOrIgnore agar tidak error jika data sudah ada
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        foreach ($csv as $index => $record) {
            $data[] = [
                'route_id' => $record['route_id'],
                'route_short_name' => $record['route_short_name'],
                'route_long_name' => $record['route_long_name'],
                'route_color' => $record['route_color'] ? '#' . $record['route_color'] : '#3498db',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($data) >= 100) {
                DB::table('tb_routes')->insertOrIgnore($data);
                $data = [];
                if ($this->command) {
                    $this->command->getOutput()->write('.');
                }
            }
        }

        if (!empty($data)) {
            DB::table('tb_routes')->insertOrIgnore($data);
        }

        $total = DB::table('tb_routes')->count();
        if ($this->command) {
            $this->command->newLine();
            $this->command->info("RouteSeeder selesai, Total $total routes");
        }
    }
}
