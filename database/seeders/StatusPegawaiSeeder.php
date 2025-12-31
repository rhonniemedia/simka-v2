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
            ],
            [
                'nama' => 'PPPK',
                'alias' => 'Pegawai Pemerintah dengan Perjanjian Kerja',
            ],
            [
                'nama' => 'Honorer',
                'alias' => 'Pegawai Honorer',
            ],
        ];

        foreach ($statuses as $status) {
            StatusPegawai::create($status);
        }
    }
}
