<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kolaborasi;

class KolaborasiSeeder extends Seeder
{
    public function run(): void
    {
        Kolaborasi::insert([
            [
                'judul' => 'Billboards City Vision',
                'kategori' => 'Kolaborasi',
                'deskripsi' => 'Kolaborasi dengan City Vision melalui media billboard strategis untuk meningkatkan visibilitas layanan dan memperkuat konektivitas informasi transportasi publik.',
                'gambar' => 'colab-tj-1.jpg',
            ],
            [
                'judul' => 'Next Level Cheese',
                'kategori' => 'Kolaborasi',
                'deskripsi' => 'Kolaborasi kreatif dengan brand Next Level Cheese dalam menghadirkan kampanye unik yang menggabungkan pengalaman kuliner dengan mobilitas urban.',
                'gambar' => 'colab-tj-2.jpg',
            ],
        ]);
    }
}
