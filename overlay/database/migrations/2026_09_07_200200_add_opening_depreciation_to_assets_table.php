<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Akumulasi penyusutan yang sudah berjalan sebelum aplikasi ini dipakai.
 *
 * Perusahaan yang mulai memakai GAIS hari ini punya aset yang dibeli bertahun tahun
 * lalu, dan penyusutannya sudah dicatat di tempat lain. Kalau dibiarkan kosong, GAIS
 * akan menghitungnya sendiri dari jadwal, yang untuk kebanyakan aset justru angka yang
 * benar. Kolom ini untuk aset yang bukunya berkata lain, misalnya karena pernah ada
 * revaluasi atau karena dulu memakai metode yang berbeda. Diisi berarti dipakai apa
 * adanya; dikosongkan berarti mengikuti hitungan jadwal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('opening_accumulated_depreciation', 18, 2)->nullable();
            $table->text('opening_depreciation_note')->nullable();
        });

        /*
         * Nilai sisa berubah arti mulai kiriman ini: kosong berarti mengikuti kebijakan
         * penyusutan, terisi berarti keputusan yang disengaja untuk barang itu.
         *
         * Kolomnya dulu wajib diisi dan bawaannya nol, jadi kolomnya perlu dibuat boleh
         * kosong lebih dulu sebelum artinya bisa berubah. Bawaan nol juga ikut dibuang:
         * selama bawaannya masih nol, tiap aset baru akan lahir dengan jawaban yang tidak
         * pernah diketik siapa pun, dan jawaban itu akan membekukan kebijakan "ikut
         * kategori" tanpa ada yang tahu.
         */
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('residual_value', 18, 2)->nullable()->default(null)->change();
        });

        /*
         * Nol yang sudah terlanjur tersimpan ikut dikosongkan. Tidak ada angka yang
         * berubah hari ini, karena kebijakan bawaannya memang nol; yang berubah hanya
         * artinya, dari jawaban menjadi belum menjawab.
         */
        DB::table('assets')->where('residual_value', 0)->update(['residual_value' => null]);
    }

    public function down(): void
    {
        // Kolom dikembalikan wajib diisi, jadi yang kosong harus diisi nol dulu.
        DB::table('assets')->whereNull('residual_value')->update(['residual_value' => 0]);

        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('residual_value', 18, 2)->default(0)->nullable(false)->change();
            $table->dropColumn(['opening_accumulated_depreciation', 'opening_depreciation_note']);
        });
    }
};
