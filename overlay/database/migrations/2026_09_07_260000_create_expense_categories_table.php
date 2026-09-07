<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kategori biaya GA.
 *
 * Kolom terpentingnya bukan nama, melainkan `source`. Kolom itu yang menentukan dari mana
 * realisasi anggaran kategori ini dihitung, dan itulah yang membedakan aplikasi ini dari
 * spreadsheet: angka realisasi tidak diketik ulang oleh siapa pun, melainkan dijumlahkan
 * dari catatan yang sudah terlanjur ada karena pekerjaan sehari hari.
 *
 * Biaya pemeliharaan sudah tercatat saat perintah kerja ditutup. Biaya BBM sudah tercatat
 * saat pengisian dicatat. Pajak kendaraan sudah tercatat saat dokumen diperpanjang. Ketiganya
 * cukup dijumlahkan. Yang tersisa sebagai `manual` adalah biaya yang memang belum punya
 * modulnya sendiri, dan itu diisi lewat tagihan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();

            /*
             * pemeliharaan, bbm, dokumen_kendaraan, persediaan, manual.
             *
             * Ditulis sebagai teks, bukan enum basis data, karena daftar sumber akan
             * bertambah seiring modul bertambah, dan mengubah enum di PostgreSQL jauh
             * lebih merepotkan daripada menambah satu baris di kelas RealisasiBiaya.
             */
            $table->string('source', 30)->default('manual');

            // Nomor akun di sistem akuntansi perusahaan. Dibiarkan kosong sampai tim
            // finance menyebutkannya, dan tidak pernah dikarang di seeder.
            $table->string('account_code', 30)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
