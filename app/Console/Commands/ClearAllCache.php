<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GtfsCacheService;
use App\Services\PencarianRuteService;
use App\Services\NavigasiService;

class ClearAllCache extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Hapus semua cache (routes, graf, dll)';

    protected $gtfsCache;
    protected $pencarianRute;
    protected $navigasi;

    public function __construct(
        GtfsCacheService $gtfsCache,
        PencarianRuteService $pencarianRute,
        NavigasiService $navigasi
    ) {
        parent::__construct();
        $this->gtfsCache = $gtfsCache;
        $this->pencarianRute = $pencarianRute;
        $this->navigasi = $navigasi;
    }

    public function handle()
    {
        $this->info('Membersihkan semua cache...');

        $this->gtfsCache->clearCache();
        $this->pencarianRute->clearCache();
        $this->navigasi->clearCache();

        // Juga hapus cache Laravel umum
        \Illuminate\Support\Facades\Cache::flush();

        $this->info('Semua cache berhasil dibersihkan!');

        return Command::SUCCESS;
    }
}
