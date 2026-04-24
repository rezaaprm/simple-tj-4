<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    protected $table = 'tb_stops';
    protected $primaryKey = 'stop_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'stop_id',
        'stop_name',
        'stop_lat',
        'stop_lon'
    ];

    public function stopTimes()
    {
        return $this->hasMany(StopTime::class, 'stop_id', 'stop_id');
    }
}
