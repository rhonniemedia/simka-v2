<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            [
                'nama' => 'Kepala Sekolah',
                'kode' => 'kepsek',
            ],
            [
                'nama' => 'Wakil Kepala Sekolah',
                'kode' => 'wakasek',
            ],
            [
                'nama' => 'Koordinator Tata Usaha',
                'kode' => 'koord_tu',
            ],
            [
                'nama' => 'Guru Mata Pelajaran',
                'kode' => 'guru_mapel',
            ],
            [
                'nama' => 'Guru Produktif',
                'kode' => 'guru_produktif',
            ],
            [
                'nama' => 'Guru Bimbingan Konseling',
                'kode' => 'guru_bk',
            ],
            [
                'nama' => 'Tenaga Administrasi',
                'kode' => 'staf',
            ],
            [
                'nama' => 'Satuan Pengamanan',
                'kode' => 'satpam',
            ],
            [
                'nama' => 'Toolman',
                'kode' => 'toolman',
            ],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::create($jabatan);
        }
    }
}
