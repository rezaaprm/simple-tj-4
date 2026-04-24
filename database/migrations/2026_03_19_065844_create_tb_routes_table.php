<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tb_routes', function (Blueprint $table) {
            $table->string('route_id')->primary();
            $table->string('route_short_name');
            $table->string('route_long_name');
            $table->string('route_color')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tb_routes');
    }
};
