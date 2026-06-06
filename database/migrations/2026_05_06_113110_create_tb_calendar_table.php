<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_calendar', function (Blueprint $table) {
            $table->string('service_id')->primary();
            $table->boolean('monday')->default(0);
            $table->boolean('tuesday')->default(0);
            $table->boolean('wednesday')->default(0);
            $table->boolean('thursday')->default(0);
            $table->boolean('friday')->default(0);
            $table->boolean('saturday')->default(0);
            $table->boolean('sunday')->default(0);
            $table->string('start_date', 8);
            $table->string('end_date', 8);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_calendar');
    }
};
