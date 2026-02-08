<?php

namespace Database\Seeders;

use App\Models\JenisPegawai;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JenisPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis = [
            [
                'nama' => 'Tenaga Pendidik',
                'alias' => 'pendidik',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Tenaga Kependidikan',
                'alias' => 'kependidikan',
                'status' => 'aktif',
            ],
        ];

        foreach ($jenis as $item) {
            JenisPegawai::create($item);
        }
    }
}
