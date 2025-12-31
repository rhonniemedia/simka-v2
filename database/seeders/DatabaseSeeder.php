<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // Create Admin
            StatusPegawaiSeeder::class,  // Create PNS, PPPK, Honorer
            JenisPegawaiSeeder::class,   // Create Pendidik, Kependidikan
            JabatanSeeder::class,        // Create Kepsek, Guru, TU, dll
            JurusanSeeder::class,        // Create TKJ, Akuntansi, dll
            PegawaiSeeder::class,        // Eksekusi logic 1+1+298
        ]);
    }
}
