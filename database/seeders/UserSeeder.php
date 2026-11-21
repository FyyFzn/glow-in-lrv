<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['username' => 'user1'],
            [
                'name' => 'admin',
                'email' => 'admin@glowin.com',
                'password' => Hash::make('password'),
            ]
        );

        DB::table('profiles')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'bio' => 'Halo, saya pengguna Glow-in.',
                'location' => 'Bandung, Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
