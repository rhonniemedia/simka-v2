<?php

use App\Models\JenisPegawai;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PensiunController;
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

// Route::middleware(['auth'])->group(function () {

// | MASTER
Route::prefix('master')->name('master.')->group(function () {
    Route::resource('schools', SekolahController::class)->names('schools');
    Route::resource('ranks', PangkatController::class)->names('ranks');
    Route::resource('employee-statuses', StatusPegawaiController::class)->names('employee-statuses');
    Route::resource('employee-types', JenisPegawaiController::class)->names('employee-types');
    Route::resource('positions', JabatanController::class)->names('positions');
});

// | EMPLOYEES
Route::prefix('employees')->name('employees.')->group(function () {
    Route::resource('data', PegawaiController::class)->names('data');
    Route::resource('mutations', MutasiController::class)->names('mutations');
    Route::resource('documents', DokumenController::class)->names('documents');
});

// | CAREER
Route::prefix('career')->name('career.')->group(function () {
    Route::resource('promotions', KepangkatanController::class)->names('promotions');
    Route::resource('salary-increments', GajiBerkalaController::class)->names('salary-increments');
    Route::resource('retirements', PensiunController::class)->names('retirements');
});

// | SUPPORT
Route::prefix('support')->name('support.')->group(function () {
    Route::resource('education-history', PendidikanController::class)->names('education-history');
    Route::resource('family-data', KeluargaController::class)->names('family-data');
});
// });
