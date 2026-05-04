<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_poi', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Nama tempat
            $table->string('category')->index(); // Kategori (bank, hospital, dll)
            $table->decimal('lat', 10, 8);       // Latitude (Y)
            $table->decimal('lng', 11, 8);       // Longitude (X)
            $table->string('osm_id')->nullable(); // ID dari OSM
            $table->timestamps();

            // Index untuk pencarian cepat
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_poi');
    }
};
