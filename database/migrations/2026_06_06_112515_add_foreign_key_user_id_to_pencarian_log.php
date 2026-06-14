<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tb_pencarian_log', function (Blueprint $table) {
            // Cek apakah foreign key belum ada
            if (!Schema::hasColumn('tb_pencarian_log', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            $table->foreign('user_id')->references('id_users')->on('tb_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tb_pencarian_log', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
