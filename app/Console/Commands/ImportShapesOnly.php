<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ImportShapesOnly extends Command
{
    protected $signature = 'import:shapes-only 
                            {file : Path ke file shapes.txt}';
    protected $description = 'Import shapes.txt saja';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File $file tidak ditemukan!");
            return Command::FAILURE;
        }

        $this->info('Memulai import shapes...');

        DB::table('tb_shapes')->truncate();
        $this->info('Data shapes lama dihapus');

        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $data = [];
        $counter = 0;

        foreach ($csv as $index => $record) {
            // Skip koordinat 0,0
            if ($record['shape_pt_lat'] == 0 || $record['shape_pt_lon'] == 0) continue;

            $data[] = [
                'shape_id' => $record['shape_id'],
                'shape_pt_lat' => $record['shape_pt_lat'],
                'shape_pt_lon' => $record['shape_pt_lon'],
                'shape_pt_sequence' => $record['shape_pt_sequence'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $counter++;

            if (count($data) >= 1000) {
                DB::table('tb_shapes')->insert($data);
                $data = [];
                $this->output->write('.');
            }
        }

        if (!empty($data)) {
            DB::table('tb_shapes')->insert($data);
        }

        $this->newLine();
        $this->info("Berhasil import: " . number_format($counter) . " titik shapes");

        return Command::SUCCESS;
    }
}
