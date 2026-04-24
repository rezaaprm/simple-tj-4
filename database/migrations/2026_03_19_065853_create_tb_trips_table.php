<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_trips', function (Blueprint $table) {
            $table->string('trip_id')->primary();
            $table->string('route_id');
            $table->string('service_id');
            $table->string('trip_headsign')->nullable();
            $table->integer('direction_id')->nullable();
            $table->string('shape_id')->nullable();
            $table->timestamps();

            $table->index('route_id');
            $table->index('shape_id');
            $table->index('direction_id');

            $table->foreign('route_id')->references('route_id')->on('tb_routes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_trips');
    }
};
