<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GenerateStaticPeta extends Command
{
    protected $signature = 'static:generate-peta';
    protected $description = 'Generate static HTML for /peta page';

    public function handle()
    {
        $this->info('Generating static page for /peta...');

        // Ambil konten dari route /peta (gunakan internal request)
        $response = Http::withOptions([
            'verify' => false,
            'timeout' => 120,
        ])->get(config('app.url') . '/peta');

        if (!$response->successful()) {
            $this->error('Failed to fetch /peta: ' . $response->status());
            return 1;
        }

        $html = $response->body();

        // Simpan ke public/static/peta.html
        $path = public_path('static');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        File::put($path . '/peta.html', $html);

        $this->info('Static page saved to public/static/peta.html');

        return 0;
    }
}
