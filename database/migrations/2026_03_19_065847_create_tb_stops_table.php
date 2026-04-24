<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_stops', function (Blueprint $table) {
            $table->string('stop_id')->primary();
            $table->string('stop_name');
            $table->decimal('stop_lat', 10, 8);
            $table->decimal('stop_lon', 11, 8);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_stops');
    }
};
