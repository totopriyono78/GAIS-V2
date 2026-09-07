<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Sumber realisasi "manual" berganti nama menjadi "tagihan".
 *
 * Waktu kategori biaya dibuat, enam di antaranya diberi sumber "manual" yang artinya
 * "belum bisa dijumlahkan aplikasi, menunggu modul tagihan". Modul tagihan sekarang ada,
 * jadi kata "manual" berhenti benar: realisasinya dijumlahkan sendiri persis seperti
 * empat sumber lainnya, hanya saja dari faktur rekanan, bukan dari catatan pekerjaan
 * sehari hari.
 *
 * Namanya diganti, bukan dibiarkan, karena kolom sumber inilah yang dibaca orang di layar
 * kategori biaya untuk memutuskan apakah angka realisasinya bisa dipercaya. Nama yang
 * tertinggal satu tahap di belakang kenyataan adalah cara termurah membuat orang berhenti
 * mempercayai seluruh layarnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('expense_categories')->where('source', 'manual')->update(['source' => 'tagihan']);
    }

    public function down(): void
    {
        DB::table('expense_categories')->where('source', 'tagihan')->update(['source' => 'manual']);
    }
};
