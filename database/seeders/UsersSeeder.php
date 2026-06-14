<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Admin: Reza
        Users::updateOrCreate(
            ['email' => 'reza@admin.com'],
            [
                'role' => 'admin',
                'nama' => 'Reza',
                'password' => Hash::make('password'),
            ]
        );

        // User: Tama
        Users::updateOrCreate(
            ['email' => 'tama@user.com'],
            [
                'role' => 'users',
                'nama' => 'Tama',
                'password' => Hash::make('password'),
            ]
        );
    }
}
