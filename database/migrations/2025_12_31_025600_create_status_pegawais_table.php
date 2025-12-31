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
        Schema::create('status_pegawais', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID
            $table->string('nama'); // PNS, PPPK, Honorer
            $table->string('alias'); // Pegawai Negeri Sipil, Pegawai Pemerintah dengan Perjanjian Kerja, Honorer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pegawais');
    }
};
