<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class About extends Model
{
    //
    use SoftDeletes, HasFactory;

    //
    protected $table = 'tb_about';
    protected $primaryKey = 'id_about';

    //
    protected $keyType = 'int';
    public $incrementing = true;

    //
    protected $fillable = [
        'judul',
        'deskripsi',
        'keterangan',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
