<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'tb_trips';
    protected $primaryKey = 'trip_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'trip_id',
        'route_id',
        'service_id',
        'trip_headsign',
        'direction_id',
        'shape_id'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', 'route_id');
    }

    public function stopTimes()
    {
        return $this->hasMany(StopTime::class, 'trip_id', 'trip_id')
            ->orderBy('stop_sequence');
    }
}
