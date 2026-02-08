<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JabatanPegawai;
use App\Models\Jurusan;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan ada User untuk created_by
        $adminId = User::first()?->id ?? User::factory()->create()->id;

        // 2. Ambil ID Jabatan Spesifik (Asumsi tabel jabatans sudah ada isinya)
        $jabKepsek = JabatanPegawai::where('nama', 'LIKE', '%Kepala Sekolah%')->first();
        $jabTU = JabatanPegawai::where('nama', 'LIKE', '%Koordinator Tata Usaha%')->first();

        // 3. Buat Kepala Sekolah (1 Orang)
        Pegawai::create([
            'nama' => 'H. Muhammad Nasir, M.Pd', // Contoh Nama Kepsek
            'peg_slug' => Str::slug('H. Muhammad Nasir, M.Pd') . '-' . Str::random(5),
            'jab_id' => $jabKepsek->id,
            'sp_id' => \App\Models\StatusPegawai::where('nama', 'PNS')->first()?->id,
            'jp_id' => \App\Models\JenisPegawai::where('alias', 'pendidik')->first()?->id,
            'jurusan_id' => Jurusan::inRandomOrder()->first()->id,
            'nip' => '197501012000031001', // Data statis untuk testing login
            'nik' => '170201XXXXXXXXXX',
            'agama' => 'islam',
            'email' => 'kepsek@smkn1rl.sch.id',
            'telepon' => '081122334455',
            'jk' => 'L',
            'jalan'          => 'Jl. Seroja',
            'desa_kelurahan' => 'Air Bang',
            'rt'             => '01',
            'rw'             => '01',
            'kecamatan'      => 'Curup Tengah',
            'kabupaten_kota' => 'Rejang Lebong',
            'provinsi'       => 'Bengkulu',
            'kode_pos'       => '39125',
            'status' => 'aktif',
            't_lahir' => 'Curup',
            'tgl_lahir' => '1975-01-01',
            'created_by' => $adminId,
        ]);

        // 4. Buat Koordinator TU (1 Orang)
        Pegawai::create([
            'nama' => 'Siti Aminah, S.Sos',
            'peg_slug' => Str::slug('Siti Aminah, S.Sos') . '-' . Str::random(5),
            'jab_id' => $jabTU->id,
            'sp_id' => \App\Models\StatusPegawai::where('nama', 'PNS')->first()?->id,
            'jp_id' => \App\Models\JenisPegawai::where('alias', 'kependidikan')->first()?->id,
            'jurusan_id' => Jurusan::inRandomOrder()->first()->id,
            'nip' => '198005122005012003',
            'nik' => '170202XXXXXXXXXX',
            'agama' => 'islam',
            'email' => 'tu@smkn1rl.sch.id',
            'telepon' => '081233445566',
            'jk' => 'P',
            'jalan'          => 'Jl. Pramuka',
            'desa_kelurahan' => 'Air Meles',
            'rt'             => '04',
            'rw'             => '01',
            'kecamatan'      => 'Curup Timur',
            'kabupaten_kota' => 'Rejang Lebong',
            'provinsi'       => 'Bengkulu',
            'kode_pos'       => '39128',
            'status' => 'aktif',
            't_lahir' => 'Rejang Lebong',
            'tgl_lahir' => '1980-05-12',
            'created_by' => $adminId,
        ]);

        // 5. Buat Sisa Pegawai (298 Orang Random)
        // Kita gunakan factory untuk sisanya
        Pegawai::factory()->count(298)->create([
            'created_by' => $adminId
        ]);
    }
}
