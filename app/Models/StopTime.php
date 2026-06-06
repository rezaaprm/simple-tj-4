<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StopTime extends Model
{
    protected $table = 'tb_stop_times';

    protected $fillable = [
        'trip_id',
        'stop_id',
        'stop_sequence',
        'shape_dist_traveled',
    ];

    public function stop()
    {
        return $this->belongsTo(Stop::class, 'stop_id', 'stop_id');
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id', 'trip_id');
    }
}
