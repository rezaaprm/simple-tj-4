<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequency extends Model
{
    protected $table = 'tb_frequencies';
    protected $fillable = [
        'trip_id',
        'start_time',
        'end_time',
        'headway_secs',
        'exact_times',
    ];

    protected $casts = [
        'exact_times' => 'boolean',
    ];
}
