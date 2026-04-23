<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('ujian.index');
});

Route::get('/dashboard', function () {
    return redirect()->route('admin.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('apps_ade')->middleware('auth')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/peserta', [AdminController::class, 'peserta'])->name('admin.peserta');
    Route::get('/soal', [AdminController::class, 'soal'])->name('admin.soal');
    Route::get('/koreksi', [AdminController::class, 'koreksi'])->name('admin.koreksi');
    Route::get('/aktif-peserta', [AdminController::class, 'peserta_aktif'])->name('admin.aktif_peserta');
    Route::get('/reset-peserta', [AdminController::class, 'reset_peserta'])->name('admin.reset_peserta');
    Route::get('/riwayat', [AdminController::class, 'riwayat'])->name('admin.riwayat');
    Route::post('/import-soal', [AdminController::class, 'importSoal'])->name('admin.import-soal');
    Route::put('/aktif-peserta/aktifkan', [AdminController::class, 'aktifkan_seluruh_peserta'])->name('admin.aktif_peserta.aktif');
    Route::put('/aktif-peserta/nonaktifkan', [AdminController::class, 'nonaktifkan_seluruh_peserta'])->name('admin.aktif_peserta.nonaktif');
    Route::get('/aktif-peserta/nonaktifkan-peserta/{id}', [AdminController::class, 'nonaktifkan_peserta'])->name('admin.aktif_peserta.one_nonaktif');
    Route::get('/aktif-peserta/aktifkan-peserta/{id}', [AdminController::class, 'aktifkan_peserta'])->name('admin.aktif_peserta.one_aktif');
    Route::get('/reset/{id}', [AdminController::class, 'reset'])->name('admin.reset');
});

Route::prefix('ujian')->group(function() {
    Route::get('/', function() {
        return view('test.cek');
    })->name('ujian.index');

    Route::post('/cek_peserta', [UserController::class, 'cek_peserta'])->name('ujian.cek_peserta');
    Route::get('/mulai/{id}', [UserController::class, 'mulai_ujian'])->name('ujian.mulai');
    Route::get('/soal/{id}', [UserController::class, 'halaman_soal'])->name('ujian.soal');
    Route::post('/simpan_jawaban', [UserController::class, 'simpan_jawaban'])->name('ujian.simpan_jawaban');
    Route::post('/reset/{id}', [UserController::class, 'reset_ujian'])->name('ujian.reset');
});

require __DIR__.'/auth.php';
