<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'puffingdev@gmail.com'],
            [
                'name' => 'Puffing Dev',
                'password' => Hash::make('krabipatie'),
                'plain_password' => 'krabipatie',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Manager account
        User::updateOrCreate(
            ['email' => 'manager@farm.com'],
            [
                'name' => 'Farm Manager',
                'password' => Hash::make('password'),
                'plain_password' => 'password',
                'role' => 'manager',
                'email_verified_at' => now(),
            ]
        );
    }
}