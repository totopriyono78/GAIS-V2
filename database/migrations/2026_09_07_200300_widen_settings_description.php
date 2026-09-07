<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Keterangan pengaturan dilebarkan menjadi teks tanpa batas.
 *
 * Batas 255 karakter dulu dipilih tanpa alasan khusus, dan sekarang menggigit: kebijakan
 * penyusutan perlu dijelaskan berikut dasar pajaknya, dan penjelasan yang dipotong di
 * tengah kalimat lebih buruk daripada tidak ada penjelasan sama sekali. Kolom ini memang
 * ditulis untuk dibaca orang, jadi panjangnya seharusnya ditentukan oleh apa yang perlu
 * dikatakan, bukan oleh angka yang kebetulan dipakai saat tabelnya dibuat.
 *
 * Kotak isian di layar Pengaturan sudah lama menerima 2000 karakter, jadi tanpa migrasi
 * ini keterangan panjang yang diketik dari layar pun akan ditolak basis data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keterangan yang melebihi 255 karakter dipangkas dulu, kalau tidak kolomnya
        // tidak bisa dipersempit kembali.
        DB::table('settings')
            ->whereRaw('length(description) > 255')
            ->update(['description' => DB::raw('substring(description from 1 for 255)')]);

        Schema::table('settings', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->change();
        });
    }
};
