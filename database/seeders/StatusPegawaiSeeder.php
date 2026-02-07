<?php

namespace Database\Seeders;

use App\Models\StatusPegawai;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusPegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'nama' => 'PNS',
                'alias' => 'Pegawai Negeri Sipil',
                'status' => 'aktif',
            ],
            [
                'nama' => 'PPPK',
                'alias' => 'Pegawai Pemerintah dengan Perjanjian Kerja',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Honorer',
                'alias' => 'Pegawai Honorer',
                'status' => 'aktif',
            ],
        ];

        foreach ($statuses as $status) {
            StatusPegawai::create($status);
        }
    }
}
