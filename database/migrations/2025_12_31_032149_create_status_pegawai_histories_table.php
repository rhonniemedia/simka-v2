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
        Schema::create('status_pegawai_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Foreign Keys menggunakan UUID
            $table->foreignUuid('pegawai_id')
                ->constrained('pegawais')
                ->onDelete('cascade')
                ->comment('Referensi ke pegawais table');

            $table->foreignUuid('sp_id')
                ->constrained('status_pegawais')
                ->onDelete('restrict')
                ->comment('Status: PNS/PPPK/Honorer');

            // Periode Penugasan
            $table->date('tmt_mulai')
                ->comment('Tanggal mulai status ini');
            $table->date('tmt_selesai')->nullable()
                ->comment('Tanggal selesai - NULL berarti masih aktif');

            // Dokumen Pendukung
            $table->string('sk_nomor', 100)->nullable()
                ->comment('Nomor SK - Plain text untuk memudahkan pencarian');

            $table->date('sk_tanggal')->nullable()
                ->comment('Tanggal SK');

            $table->text('keterangan')->nullable()
                ->comment('Keterangan tambahan atau catatan');

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
            $table->index(['pegawai_id', 'tmt_mulai'], 'idx_peg_sp_tmt');
            $table->index(['sp_id', 'tmt_mulai'], 'idx_sp_tmt');
            $table->index(['tmt_mulai', 'tmt_selesai'], 'idx_sp_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pegawai_histories');
    }
};
