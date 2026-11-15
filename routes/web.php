<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengembalianController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::resource('kategori', KategoriController::class)->except('show');
    Route::get('/buku/import', [BukuController::class, 'importForm'])->name('buku.import');
    Route::post('/buku/import', [BukuController::class, 'importStore'])->name('buku.import.store');
    Route::get('/buku/import/template', [BukuController::class, 'importTemplate'])->name('buku.import.template');
    Route::resource('buku', BukuController::class);
    Route::get('/anggota/import', [AnggotaController::class, 'importForm'])->name('anggota.import');
    Route::post('/anggota/import', [AnggotaController::class, 'importStore'])->name('anggota.import.store');
    Route::get('/anggota/import/template', [AnggotaController::class, 'importTemplate'])->name('anggota.import.template');
    Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);
    Route::resource('peminjaman', PeminjamanController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::get('/peminjaman/{peminjaman}/slip', [PeminjamanController::class, 'slip'])->name('peminjaman.slip');
    Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('/pengembalian/{peminjaman}', [PengembalianController::class, 'create'])->name('pengembalian.create');
    Route::post('/pengembalian/{peminjaman}', [PengembalianController::class, 'store'])->name('pengembalian.store');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/peminjaman', [LaporanController::class, 'peminjaman'])->name('laporan.peminjaman');
    Route::get('/laporan/belum-kembali', [LaporanController::class, 'belumKembali'])->name('laporan.belum-kembali');
    Route::get('/laporan/buku', [LaporanController::class, 'buku'])->name('laporan.buku');

    Route::get('/pengaturan', [PengaturanController::class, 'edit'])->name('pengaturan.edit');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});
