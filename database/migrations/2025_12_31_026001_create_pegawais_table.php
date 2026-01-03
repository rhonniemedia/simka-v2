<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            // ===== PRIMARY KEY menggunakan UUID =====
            $table->uuid('id')->primary();

            // ===== IDENTITAS PUBLIK (Plain) =====
            $table->string('nama')->index()->comment('Nama lengkap pegawai');
            $table->string('peg_slug')->unique()->comment('URL-friendly slug');

            // ===== RELASI (Plain - untuk JOIN) menggunakan UUID =====
            $table->foreignUuid('sp_id')->nullable()
                ->constrained('status_pegawais')
                ->onDelete('restrict')
                ->comment('Status Pegawai: PNS/PPPK/Honorer');

            $table->foreignUuid('jp_id')->nullable()
                ->constrained('jenis_pegawais')
                ->onDelete('restrict')
                ->comment('Jenis Pegawai: Pendidik/Kependidikan');

            $table->foreignUuid('jab_id')->nullable()
                ->constrained('jabatans')
                ->onDelete('restrict')
                ->comment('Jabatan utama pegawai');

            // ===== NOMOR IDENTITAS (Encrypted + Hashed) =====

            // NIP - Encrypted, Searchable, Login, UNIQUE
            $table->text('nip_encrypted')->nullable()
                ->comment('Laravel encrypted NIP');
            $table->string('nip_hash', 64)->nullable()->unique()
                ->comment('SHA256 hash - untuk search, login, dan uniqueness');

            // NIK - Encrypted, Searchable, Login, UNIQUE
            $table->text('nik_encrypted')
                ->comment('Laravel encrypted NIK');
            $table->string('nik_hash', 64)->unique()
                ->comment('SHA256 hash - untuk search, login, dan uniqueness');

            // NUPTK - Encrypted, Searchable, UNIQUE
            $table->text('nuptk_encrypted')->nullable()
                ->comment('Laravel encrypted NUPTK');
            $table->string('nuptk_hash', 64)->nullable()->unique()
                ->comment('SHA256 hash - untuk search dan uniqueness');

            // NPWP - Encrypted, UNIQUE
            $table->text('npwp_encrypted')->nullable()
                ->comment('Laravel encrypted NPWP');
            $table->string('npwp_hash', 64)->nullable()->unique()
                ->comment('SHA256 hash - untuk uniqueness');

            // ===== DATA FINANSIAL (Encrypted Only) =====
            $table->text('no_rek_encrypted')->nullable()
                ->comment('Nomor rekening encrypted');
            $table->text('besaran_gaji_encrypted')->nullable()
                ->comment('Besaran gaji encrypted');

            // ===== DATA KEPEGAWAIAN (Plain) =====
            $table->string('pmk', 50)->nullable()
                ->comment('Pangkat/Golongan');
            $table->date('tmt_mk')->nullable()
                ->comment('TMT Masa Kerja');
            $table->unsignedSmallInteger('pmk_thn')->nullable()
                ->comment('Masa kerja dalam tahun');
            $table->unsignedTinyInteger('pmk_bln')->nullable()
                ->comment('Masa kerja dalam bulan');

            // ===== DATA PRIBADI (Encrypted) =====

            // Tempat Lahir - Plain (untuk surat/laporan)
            $table->string('t_lahir', 100)
                ->comment('Tempat lahir');

            // Tanggal Lahir - ENCRYPTED
            $table->text('tgl_lahir_encrypted')
                ->comment('Tanggal lahir encrypted');

            // Jenis Kelamin - Plain (untuk statistik)
            $table->enum('jk', ['L', 'P'])
                ->comment('Jenis Kelamin: L=Laki-laki, P=Perempuan');

            // Agama - ENCRYPTED
            $table->text('agama_encrypted')
                ->comment('Agama encrypted');

            // Kawin Tanggungan - PLAIN
            $table->string('kawin_tanggungan', 50)->nullable()
                ->comment('Status kawin & jumlah tanggungan - PLAIN TEXT');

            // ===== KONTAK (Encrypted + Hashed) =====

            // Telepon - Encrypted, Masked, UNIQUE
            $table->text('telepon_encrypted')->nullable()
                ->comment('Nomor telepon encrypted');
            $table->string('telepon_hash', 64)->nullable()->unique()
                ->comment('SHA256 hash - untuk uniqueness');
            $table->string('telepon_masked', 20)->nullable()
                ->comment('Format masked: 0812****7890 untuk display list');

            // Email - Encrypted, Searchable, Login, UNIQUE
            $table->text('email_encrypted')->nullable()
                ->comment('Email encrypted');
            $table->string('email_hash', 64)->nullable()->unique()
                ->comment('SHA256 hash - untuk search, login, dan uniqueness');

            // ===== ALAMAT (Encrypted) =====
            $table->text('jalan_encrypted')->nullable()
                ->comment('Alamat jalan encrypted');
            $table->text('desa_kelurahan_encrypted')->nullable()
                ->comment('Desa/Kelurahan encrypted');
            $table->text('rt_encrypted')->nullable()
                ->comment('RT encrypted');
            $table->text('rw_encrypted')->nullable()
                ->comment('RW encrypted');
            $table->text('kecamatan_encrypted')->nullable()
                ->comment('Kecamatan encrypted');
            $table->text('kabupaten_kota_encrypted')->nullable()
                ->comment('Kabupaten/Kota encrypted');
            $table->text('provinsi_encrypted')->nullable()
                ->comment('Provinsi encrypted');
            $table->text('kode_pos_encrypted')->nullable()
                ->comment('Kode pos encrypted');

            // ===== FILE & STATUS =====
            $table->string('foto')->nullable()
                ->comment('Path file foto profil');
            $table->foreignUuid('jurusan_id')->nullable()
                ->constrained('jurusans')
                ->onDelete('restrict')
                ->comment('Jurusan/bidang keahlian');
            $table->enum('status', ['aktif', 'mutasi', 'pensiun', 'draft'])
                ->default('draft')
                ->index()
                ->comment('Status kepegawaian draf');
            $table->date('tmt_status')->nullable()
                ->comment('TMT Status kepegawaian saat ini');

            // ===== AUDIT TRAIL menggunakan UUID =====
            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User yang create record');
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User yang terakhir update');

            $table->timestamps();
            $table->softDeletes();

            // ===== INDEXES untuk Performance =====
            $table->index(['sp_id', 'status'], 'idx_sp_status');
            $table->index(['jp_id', 'status'], 'idx_jp_status');
            $table->index(['jab_id', 'status'], 'idx_jab_status');
            $table->index(['jurusan_id', 'status'], 'idx_jur_status');
            $table->index(['status', 'tmt_status'], 'idx_status_tmt');

            // Index untuk login/search via hash (CRITICAL untuk performance)
            $table->index('nip_hash', 'idx_nip_hash');
            $table->index('nik_hash', 'idx_nik_hash');
            $table->index('email_hash', 'idx_email_hash');
            $table->index('nuptk_hash', 'idx_nuptk_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
