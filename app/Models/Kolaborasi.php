<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kolaborasi extends Model
{
    protected $table = 'tb_kolaborasi';
    protected $primaryKey = 'id_kolaborasi';

    protected $fillable = [
        'judul',
        'kategori',
        'deskripsi',
        'gambar'
    ];
}
