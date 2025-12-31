<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusan = [
            [
                'nama' => 'Umum',
                'alias' => 'umum',
            ],
            [
                'nama' => 'Desain Pemodelan dan Informasi Bangunan',
                'alias' => 'dpib',
            ],
            [
                'nama' => 'Teknik Elektronika',
                'alias' => 'elektronika',
            ],
            [
                'nama' => 'Teknik Ketenagalistrikan',
                'alias' => 'listrik',
            ],
            [
                'nama' => 'Teknik Jaringan Komputer dan Telekomunikasi',
                'alias' => 'tjkt',
            ],
            [
                'nama' => 'Teknik Mesin',
                'alias' => 'mesin',
            ],
            [
                'nama' => 'Teknik Pengelasan dan Fabrikasi Logam',
                'alias' => 'las',
            ],
            [
                'nama' => 'Teknik Otomotif',
                'alias' => 'otomotif',
            ],
        ];

        foreach ($jurusan as $item) {
            Jurusan::create($item);
        }
    }
}
