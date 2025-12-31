<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Admin
        User::create([
            'name' => 'Admin Staff',
            'email' => 'staff@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
