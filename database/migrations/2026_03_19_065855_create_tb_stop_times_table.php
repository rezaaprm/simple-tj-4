<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_stop_times', function (Blueprint $table) {
            $table->id();
            $table->string('trip_id');
            $table->string('stop_id');
            $table->integer('stop_sequence');
            $table->timestamps();

            $table->index(['trip_id', 'stop_sequence']);
            $table->index('stop_id');

            $table->foreign('trip_id')->references('trip_id')->on('tb_trips')->onDelete('cascade');
            $table->foreign('stop_id')->references('stop_id')->on('tb_stops')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_stop_times');
    }
};
