<?php

namespace Database\Seeders;

use App\Models\Galeri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GaleriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Galeri::insert([
            [
                'judul' => 'Armada Transjakarta',
                'kategori' => 'Armada',
                'gambar' => 'galeri-tj-armada-1.jpg'
            ],
            [
                'judul' => 'Halte Modern',
                'kategori' => 'Halte',
                'gambar' => 'galeri-tj-halte-1.jpg'
            ],
            [
                'judul' => 'Pelayanan Prima',
                'kategori' => 'Pelanggan',
                'gambar' => 'galeri-tj-pelayanan-1.jpg'
            ],
            [
                'judul' => 'Interior Nyaman',
                'kategori' => 'Interior',
                'gambar' => 'galeri-tj-interior-1.jpg'
            ],
        ]);
    }
}
