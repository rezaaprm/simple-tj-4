<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeri extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_galeri';
    protected $primaryKey = 'id_galeri';

    protected $fillable = [
        'judul',
        'kategori',
        'gambar'
    ];
}
