<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tb_stop_times', function (Blueprint $table) {
            $table->decimal('shape_dist_traveled', 12, 6)->nullable()->after('stop_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tb_stop_times', function (Blueprint $table) {
            $table->dropColumn('shape_dist_traveled');
        });
    }
};
