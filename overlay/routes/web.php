<?php

use App\Http\Controllers\BeritaAcaraAsetController;
use App\Http\Controllers\CetakLabelAsetController;
use App\Http\Controllers\KartuRiwayatAsetController;
use Illuminate\Support\Facades\Route;

/*
 * GAIS tidak punya halaman publik. Seluruh aplikasi ada di panel /admin,
 * jadi alamat pangkal langsung dialihkan ke sana supaya pengguna cukup
 * mengingat satu alamat. Pengguna yang belum masuk akan diarahkan Filament
 * ke halaman login.
 */
Route::redirect('/', '/admin');

/*
 * Halaman cetak berada di luar panel karena tampilannya harus polos, tanpa
 * menu dan tanpa sidebar, supaya yang keluar dari printer hanya labelnya.
 * Izin tetap diperiksa di dalam controller.
 */
Route::middleware('auth')->group(function () {
    Route::get('cetak/label-aset', CetakLabelAsetController::class)->name('gais.cetak.label-aset');

    /*
     * Kartu riwayat aset ikut berada di luar panel karena sering dilampirkan ke
     * berita acara serah terima atau pelepasan, jadi harus bisa langsung dicetak.
     */
    Route::get('aset/{asset}/riwayat', KartuRiwayatAsetController::class)->name('gais.aset.riwayat');

    /*
     * Berita acara mutasi (bam) dan berita acara serah terima (bast). Keduanya berkas
     * PDF yang langsung terunduh, jadi tidak ada halaman yang perlu ditampilkan.
     */
    Route::get('aset/serah-terima/{transfer}/berita-acara/{jenis}', BeritaAcaraAsetController::class)
        ->whereIn('jenis', ['bam', 'bast'])
        ->name('gais.aset.berita-acara');
});
