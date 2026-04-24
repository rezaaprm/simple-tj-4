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
        'bobot_preferensi'
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
}
