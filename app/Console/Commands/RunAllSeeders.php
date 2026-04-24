<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunAllSeeders extends Command
{
    protected $signature = 'seed:all';
    protected $description = 'Jalankan semua seeder secara berurutan';

    public function handle()
    {
        $this->info('🌱 MENJALANKAN SEMUA SEEDER...');
        $this->line(str_repeat('=', 50));

        $seeders = [
            'RouteSeeder',
            'StopSeeder',
            'TripSeeder',
            'StopTimeSeeder',
            'ShapeSeeder',
            'PencarianLogSeeder',
        ];

        foreach ($seeders as $seeder) {
            $this->info("➡️  Menjalankan $seeder...");
            $this->call("db:seed", ['--class' => $seeder]);
            $this->line("");
        }

        $this->info('✅ SEMUA SEEDER SELESAI!');
        return Command::SUCCESS;
    }
}
