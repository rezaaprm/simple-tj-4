<?php

namespace Database\Seeders;

use App\Models\Destinasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DestinasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Destinasi::insert([
            [
                'nama' => 'Monas',
                'kategori' => 'Pusat',
                'gambar' => 'monas.webp'
            ],
            [
                'nama' => 'Ancol',
                'kategori' => 'Utara',
                'gambar' => 'ancol.jpg'
            ],
            [
                'nama' => 'TMII',
                'kategori' => 'Timur',
                'gambar' => 'tmii.jpg',
            ],
            [
                'nama' => 'Kota Tua',
                'kategori' => 'Barat',
                'gambar' => 'kota_tua.jpg',
            ],
            [
                'nama' => 'Ragunan',
                'kategori' => 'Selatan',
                'gambar' => 'ragunan.jpg',
            ]
        ]);
    }
}
