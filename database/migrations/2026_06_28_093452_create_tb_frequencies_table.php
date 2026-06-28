<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_frequencies', function (Blueprint $table) {
            $table->id();
            $table->string('trip_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('headway_secs');
            $table->boolean('exact_times')->default(false);
            $table->timestamps();

            $table->index('trip_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_frequencies');
    }
};
