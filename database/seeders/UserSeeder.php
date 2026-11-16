<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'user1'],
            [
                'name' => 'admin',
                'email' => 'admin@glowin.com',
                'password' => Hash::make('password'),
                'bio' => 'Halo, saya pengguna Glow-in.',
                'location' => 'Bandung, Indonesia',
            ]
        );
    }
}