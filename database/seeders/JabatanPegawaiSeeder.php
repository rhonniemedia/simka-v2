<?php

namespace Database\Seeders;

use App\Models\JabatanPegawai;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatan_pegawais = [
            [
                'nama' => 'Kepala Sekolah',
                'kode' => 'kepsek',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Wakil Kepala Sekolah',
                'kode' => 'wakasek',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Koordinator Tata Usaha',
                'kode' => 'koord_tu',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Guru Mata Pelajaran',
                'kode' => 'guru_mapel',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Guru Produktif',
                'kode' => 'guru_produktif',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Guru Bimbingan Konseling',
                'kode' => 'guru_bk',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Tenaga Administrasi',
                'kode' => 'staf',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Satuan Pengamanan',
                'kode' => 'satpam',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Toolman',
                'kode' => 'toolman',
                'status' => 'aktif',
            ],
        ];

        foreach ($jabatan_pegawais as $jabatan) {
            JabatanPegawai::create($jabatan);
        }
    }
}
