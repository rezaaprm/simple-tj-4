<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckImportStatus extends Command
{
    protected $signature = 'check:import-status';
    protected $description = 'Cek status import data GTFS';

    public function handle()
    {
        $this->info('CEK STATUS IMPORT DATA GTFS');
        $this->line(str_repeat('=', 50));

        // 1. ROUTES
        $routes = DB::table('tb_routes')->count();
        $this->info("1. ROUTES:");
        $this->line("   Total: " . number_format($routes));

        $sample = DB::table('tb_routes')->limit(3)->get();
        foreach ($sample as $r) {
            $this->line("   - {$r->route_short_name}: {$r->route_long_name}");
        }
        $this->line("");

        // 2. STOPS
        $stops = DB::table('tb_stops')->count();
        $this->info("2. STOPS:");
        $this->line("   Total: " . number_format($stops));

        $sampleStops = DB::table('tb_stops')->limit(3)->get();
        foreach ($sampleStops as $s) {
            $this->line("   - {$s->stop_name}");
        }
        $this->line("");

        // 3. TRIPS
        $trips = DB::table('tb_trips')->count();
        $this->info("3. TRIPS:");
        $this->line("   Total: " . number_format($trips));

        // 4. STOP TIMES
        $stopTimes = DB::table('tb_stop_times')->count();
        $this->info("4. STOP TIMES:");
        $this->line("   Total: " . number_format($stopTimes));

        // 5. SHAPES
        $shapes = DB::table('tb_shapes')->count();
        $this->info("5. SHAPES:");
        $this->line("   Total titik: " . number_format($shapes));

        // Hitung shape per route_id
        $shapePerId = DB::table('tb_shapes')
            ->select('shape_id', DB::raw('count(*) as total'))
            ->groupBy('shape_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $this->line("   Top 5 shape_id dengan titik terbanyak:");
        foreach ($shapePerId as $s) {
            $this->line("     - {$s->shape_id}: {$s->total} titik");
        }
        $this->line("");

        // 6. PENCARIAN LOG
        $logs = DB::table('tb_pencarian_log')->count();
        $this->info("6. PENCARIAN LOG:");
        $this->line("   Total: " . number_format($logs));

        // 7. RINGKASAN
        $this->info("RINGKASAN:");
        $this->table(
            ['Tabel', 'Jumlah', 'Keterangan'],
            [
                ['tb_routes', number_format($routes), $routes > 0 ? '✓ OK' : '✗ KOSONG'],
                ['tb_stops', number_format($stops), $stops > 0 ? '✓ OK' : '✗ KOSONG'],
                ['tb_trips', number_format($trips), $trips > 0 ? '✓ OK' : '✗ KOSONG'],
                ['tb_stop_times', number_format($stopTimes), $stopTimes > 0 ? '✓ OK' : '✗ KOSONG'],
                ['tb_shapes', number_format($shapes), $shapes > 0 ? '✓ OK' : '✗ KOSONG'],
                ['tb_pencarian_log', number_format($logs), '✓ ' . ($logs > 0 ? 'OK' : 'KOSONG')],
            ]
        );

        // 8. REKOMENDASI
        $this->newLine();
        $this->info("REKOMENDASI:");

        if ($routes == 0) $this->warn("   • Jalankan: php artisan db:seed --class=RouteSeeder");
        if ($stops == 0) $this->warn("   • Jalankan: php artisan db:seed --class=StopSeeder");
        if ($trips == 0) $this->warn("   • Jalankan: php artisan db:seed --class=TripSeeder");
        if ($stopTimes == 0) $this->warn("   • Jalankan: php artisan db:seed --class=StopTimeSeeder");
        if ($shapes == 0) $this->warn("   • Jalankan: php artisan db:seed --class=ShapeSeeder");

        if ($routes > 0 && $stops > 0 && $trips > 0 && $stopTimes > 0 && $shapes > 0) {
            $this->info("   ✓ SEMUA DATA SUDAH LENGKAP!");
        }

        return Command::SUCCESS;
    }
}
