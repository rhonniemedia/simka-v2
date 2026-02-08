<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jabatan_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign Keys menggunakan UUID
            $table->foreignUuid('pegawai_id')
                ->constrained('pegawais')
                ->onDelete('cascade')
                ->comment('Referensi ke pegawais table');

            $table->foreignUuid('jab_id')
                ->constrained('jabatan_pegawais')
                ->onDelete('restrict')
                ->comment('Jabatan yang pernah dijabat');

            // Periode Penugasan
            $table->date('tmt_mulai')
                ->comment('Tanggal mulai jabatan ini');
            $table->date('tmt_selesai')->nullable()
                ->comment('Tanggal selesai - NULL berarti masih aktif');

            // ===== DOKUMEN PENDUKUNG & PENUGASAN (Plain Text) =====
            $table->string('sk_nomor', 100)->nullable()
                ->comment('Nomor SK - Plain text untuk pencarian');
            $table->date('sk_tanggal')->nullable()
                ->comment('Tanggal SK');
            $table->string('unit_kerja', 255)->nullable()
                ->comment('Unit kerja/lokasi penugasan');
            $table->text('keterangan')->nullable()
                ->comment('Keterangan tambahan');

            // Audit Trail
            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk Performance
            $table->index(['pegawai_id', 'tmt_mulai'], 'idx_peg_jab_tmt');
            $table->index(['jab_id', 'tmt_mulai'], 'idx_jab_tmt');
            $table->index(['tmt_mulai', 'tmt_selesai'], 'idx_jab_period');

            // Index untuk query "siapa yang pernah jadi Kepsek?"
            $table->index(['jab_id', 'tmt_selesai'], 'idx_jab_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_histories');
    }
};
