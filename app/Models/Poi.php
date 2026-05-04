<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poi extends Model
{
    protected $table = 'tb_poi';

    protected $fillable = [
        'name',
        'category',
        'lat',
        'lng',
        'osm_id'
    ];
}
