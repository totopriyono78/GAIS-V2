<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu area yang diperiksa dalam satu putaran.
 *
 * Nama area dan nama petugas disalin ke sini saat daftarnya disusun, sama seperti lembar
 * opname menyalin kode dan nama barang. Alasannya sama pula: lembar pemeriksaan bulan lalu
 * harus tetap terbaca sebagaimana ia dicetak, meskipun areanya kelak diganti nama, dipindah
 * penanggung jawabnya, atau dinonaktifkan.
 *
 * Salinan nama petugas bukan sekadar kerapian. Kalau area yang bulan lalu ditemukan kotor
 * berpindah penanggung jawab bulan ini, catatan lama tidak boleh ikut berpindah menuduh orang
 * yang saat itu belum bertugas di sana.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_inspection_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaning_inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_area_id')->nullable()->constrained('service_areas')->nullOnDelete();

            // Salinan saat daftar disusun.
            $table->string('area_code', 30);
            $table->string('area_name', 150);
            $table->string('staff_name', 150)->nullable();

            // bersih, kurang, kotor. Kosong berarti belum sempat diperiksa.
            $table->string('result', 20)->nullable();
            $table->boolean('checked')->default(false);

            $table->text('notes')->nullable();

            // Satu foto per area sudah cukup. Yang dibutuhkan pengawas adalah bukti bahwa
            // temuannya nyata, bukan album.
            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();

            $table->timestamps();

            $table->unique(['cleaning_inspection_id', 'service_area_id']);
            $table->index(['cleaning_inspection_id', 'checked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_inspection_lines');
    }
};
