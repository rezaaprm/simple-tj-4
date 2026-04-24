<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_shapes', function (Blueprint $table) {
            $table->id();
            $table->string('shape_id');
            $table->decimal('shape_pt_lat', 10, 8);
            $table->decimal('shape_pt_lon', 11, 8);
            $table->integer('shape_pt_sequence');
            $table->timestamps();

            $table->index(['shape_id', 'shape_pt_sequence']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_shapes');
    }
};
