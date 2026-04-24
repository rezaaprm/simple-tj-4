<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfoStatistik extends Model
{
    //
    use SoftDeletes, HasFactory;

    //
    protected $table = 'tb_info_statistik';
    protected $primaryKey = 'id_info_statistik';

    //
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'jenis_data',
        'jumlah',
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
