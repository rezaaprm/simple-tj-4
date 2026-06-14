<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tb_users')) {
            Schema::create('tb_users', function (Blueprint $table) {
                $table->id('id_users');
                $table->enum('role', ['admin', 'users'])->default('users');
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('password');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tb_users');
    }
};
