<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::resource('products', ProductController::class);

Route::prefix('pegawais')->name('pegawais.')->group(function () {
    // --- RUTE KHUSUS DRAF & WIZARD (Manual) ---

    // Menampilkan daftar draf di dropdown
    Route::get('/drafts', [PegawaiController::class, 'listDrafts'])->name('drafts');

    // Melanjutkan pengisian draf yang terhenti
    Route::get('/{id}/resume', [PegawaiController::class, 'resume'])->name('resume');

    // Proses Simpan Step 1 (Membuat record draf atau update jika ada ID)
    Route::post('/store-step1/{id?}', [PegawaiController::class, 'storeStep1'])->name('store-step1');

    // Proses Update Step 2 & 3
    Route::put('/{id}/update-step/{nextStep}', [PegawaiController::class, 'updateStep'])->name('update-step');

    // Proses Finalisasi (Step 4 - Mengubah status ke 'aktif')
    Route::put('/{id}/finalize', [PegawaiController::class, 'finalize'])->name('finalize');

    // --- RUTE STANDAR (Resource) ---
    Route::resource('/', PegawaiController::class)->parameters(['' => 'pegawai']);
});
