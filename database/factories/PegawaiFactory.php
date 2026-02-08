<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\JabatanPegawai;
use App\Models\Jurusan;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use App\Models\JenisPegawai;
use App\Models\StatusPegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

class PegawaiFactory extends Factory
{
    protected $model = Pegawai::class;

    public function definition(): array
    {
        $nama = $this->faker->name();

        return [
            // Identitas Dasar (Plain)
            'nama'     => $nama,
            'peg_slug' => Str::slug($nama) . '-' . Str::random(5),

            // Relasi (Mengambil ID yang sudah ada atau buat baru)
            'sp_id'    => StatusPegawai::inRandomOrder()->first()?->id ?? StatusPegawai::factory(),
            'jp_id'    => JenisPegawai::inRandomOrder()->first()?->id ?? JenisPegawai::factory(),
            'jab_id'   => JabatanPegawai::inRandomOrder()->first()?->id ?? JabatanPegawai::factory(),

            /**
             * KOLOM VIRTUAL (Diproses oleh Model setAttribute)
             * Kita cukup mengirim nama kolom "aslinya". 
             * Model akan mengubahnya menjadi _encrypted & _hash secara otomatis.
             */
            'nip'          => $this->faker->unique()->numerify('##################'),
            'nik'          => $this->faker->unique()->numerify('################'),
            'nuptk'        => $this->faker->unique()->numerify('################'),
            'npwp'         => $this->faker->unique()->numerify('###############'),
            'no_rek'       => $this->faker->bankAccountNumber(),
            'besaran_gaji' => $this->faker->numberBetween(3000000, 15000000),
            'telepon'      => '08' . $this->faker->randomElement(['12', '13', '52', '21', '53']) . $this->faker->numerify('########'),
            'email'        => $this->faker->unique()->safeEmail(),
            'tgl_lahir'    => $this->faker->date('Y-m-d', '2000-01-01'),
            'agama'        => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),

            // Alamat (Virtual)
            'jalan'          => $this->faker->streetAddress(),
            'desa_kelurahan' => $this->faker->streetName(), // streetName menghasilkan nama daerah/desa
            'rt'             => $this->faker->numerify('##'),
            'rw'             => $this->faker->numerify('##'),
            'kecamatan'      => $this->faker->city(), // Di Faker id_ID, city() sering menghasilkan nama area/kecamatan
            'kabupaten_kota' => $this->faker->city(),
            'provinsi'       => $this->faker->state(), // Format khusus id_ID
            'kode_pos'       => $this->faker->postcode(),

            // Data Kepegawaian (Plain)
            'pmk'        => $this->faker->randomElement(['ya', 'tidak']),
            'pmk_tmt'     => $this->faker->date(),
            't_lahir'    => $this->faker->city(),
            'jk'         => $this->faker->randomElement(['L', 'P']),
            'kawin_tanggungan' => $this->faker->randomElement(['K/0', 'K/1', 'TK/0']),
            'jurusan_id' => Jurusan::inRandomOrder()->first()?->id ?? Jurusan::factory(),
            'status'     => 'aktif',
            'tmt_status' => $this->faker->date(),
            'foto'       => null,

            // Audit (Mengambil User Random)
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'updated_by' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}
