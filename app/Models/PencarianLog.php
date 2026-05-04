<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencarianLog extends Model
{
    protected $table = 'tb_pencarian_log';

    protected $fillable = [
        'id_halte_awal',
        'id_halte_tujuan',
        'waktu_eksekusi_ms',
        'node_dikunjungi',
        'total_jarak',
        'total_waktu',
        'total_pindah',
        'algoritma',
        'preference',
        'bobot_preferensi',
        'route_path_json',
        'koridors_json',
        'walking_info_json',
    ];

    protected $casts = [
        'bobot_preferensi' => 'array'
    ];

    public function halteAwal(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'id_halte_awal', 'stop_id');
    }

    public function halteTujuan(): BelongsTo
    {
        return $this->belongsTo(Stop::class, 'id_halte_tujuan', 'stop_id');
    }

    // Accessor untuk mengurai JSON otomatis
    public function getRoutePathAttribute()
    {
        return $this->route_path_json ? json_decode($this->route_path_json, true) : null;
    }

    public function getKoridorsAttribute()
    {
        return $this->koridors_json ? json_decode($this->koridors_json, true) : null;
    }

    public function getWalkingInfoAttribute()
    {
        return $this->walking_info_json ? json_decode($this->walking_info_json, true) : null;
    }
}
