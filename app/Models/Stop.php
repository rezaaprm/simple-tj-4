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

    /**
     * Cari halte terdekat dari koordinat tertentu
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @param float $radius Radius dalam meter (default 5000 = 5km)
     * @return Stop|null
     */
    public static function findNearest($lat, $lng, $radius = 5000)
    {
        // Gunakan formula Haversine di SQL untuk menghitung jarak
        return self::select('*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(stop_lat)) * cos(radians(stop_lon) - radians(?)) + sin(radians(?)) * sin(radians(stop_lat)))) as distance_km',
                [$lat, $lng, $lat]
            )
            ->having('distance_km', '<=', $radius / 1000)
            ->orderBy('distance_km')
            ->first();
    }
}
