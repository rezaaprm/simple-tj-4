<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeding penting karena foreign key constraints
     */
    public function run(): void
    {
        if ($this->command) {
            $this->command->info('🌱 MEMULAI DATABASE SEEDER...');
            $this->command->line(str_repeat('=', 50));
        }

        // 1. Seed tabel independen dulu
        if ($this->command) $this->command->info('1. Seeding Routes...');
        $this->call(RouteSeeder::class);

        if ($this->command) $this->command->info('2. Seeding Stops...');
        $this->call(StopSeeder::class);

        // 2. Seed tabel yang bergantung pada routes
        if ($this->command) $this->command->info('3. Seeding Trips...');
        $this->call(TripSeeder::class);

        // 3. Seed tabel yang bergantung pada trips dan stops
        if ($this->command) $this->command->info('4. Seeding StopTimes...');
        $this->call(StopTimeSeeder::class);

        // 4. Seed shapes (independen)
        if ($this->command) $this->command->info('5. Seeding Shapes...');
        $this->call(ShapeSeeder::class);

        // 5. Seed pencarian log (opsional - dummy data)
        if ($this->command) $this->command->info('6. Seeding PencarianLog...');
        $this->call(PencarianLogSeeder::class);

        if ($this->command) {
            $this->command->line(str_repeat('=', 50));
            $this->command->info('✅ SEMUA SEEDER SELESAI!');
        }
    }
}
