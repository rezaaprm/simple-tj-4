<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pencarian_log', function (Blueprint $table) {
            $table->id();
            $table->string('id_halte_awal');
            $table->string('id_halte_tujuan');
            $table->float('waktu_eksekusi_ms');
            $table->integer('node_dikunjungi');
            $table->float('total_jarak');
            $table->integer('total_waktu');
            $table->integer('total_pindah');
            $table->string('algoritma', 20)->default('Dijkstra');
            $table->json('bobot_preferensi')->nullable();
            $table->timestamps();

            $table->index('created_at');

            // Foreign keys akan ditambahkan di migration terpisah
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pencarian_log');
    }
};
