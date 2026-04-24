<?php

namespace Database\Seeders;

use App\Models\InfoStatistik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

class InfoStatistikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        InfoStatistik::insert([
            [
                'jenis_data' => 'Total Koridor',
                'jumlah' => '243',
                'keterangan' => 'Koridor aktif yang melayani berbagai rute'
            ],
            [
                'jenis_data' => 'Total Halte',
                'jumlah' => '8.651+',
                'keterangan' => 'Halte yang tersebar di seluruh wilayah Jakarta'
            ],
        ]);
    }
}
