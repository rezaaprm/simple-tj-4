<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'tb_routes';
    protected $primaryKey = 'route_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'route_id',
        'route_short_name',
        'route_long_name',
        'route_color'
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class, 'route_id', 'route_id');
    }
}
