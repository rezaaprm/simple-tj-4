<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        About::create([
            'judul'         => 'Tentang Transjakarta',
            'deskripsi'     => 'Transjakarta adalah sistem transportasi Bus Rapid Transit (BRT) pertama di Indonesia yang melayani rute-rute di wilayah Jakarta dan sekitarnya. Dengan armada yang modern dan rute yang terintegrasi, Transjakarta menjadi pilihan utama masyarakat Jakarta untuk bepergian sehari-hari.;
                                Kami berkomitmen untuk memberikan layanan transportasi yang cepat, nyaman, aman, dan terjangkau bagi seluruh masyarakat. Dengan integrasi rute dan layanan antar moda transportasi, kami memudahkan Anda bepergian ke berbagai destinasi di Jakarta.;',
            'keterangan'    => implode("\n", [
                'Bus Rapid Transit (BRT)',
                'Rute Terintegrasi',
                'Halte Tersebar di Seluruh Jakarta',
                'Armada Modern & Nyaman',
                'Layanan 24/7',
                'Integrasi dengan Moda Lain',
            ]),
        ]);
    }
}
