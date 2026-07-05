<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CheckImportStatus::class,
        \App\Console\Commands\ClearAllCache::class,
        \App\Console\Commands\ImportShapesOnly::class,
        \App\Console\Commands\GenerateStaticPeta::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('cache:clear-all')->weekly()->sundays()->at('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
