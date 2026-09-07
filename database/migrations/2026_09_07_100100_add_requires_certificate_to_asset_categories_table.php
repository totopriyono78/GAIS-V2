<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            /*
             * Nomor sertifikat tanah dan bangunan hanya berlaku untuk sebagian kategori.
             * Penandanya diletakkan di kategori, bukan ditulis di kode sebagai daftar
             * kode kategori, supaya tim GA bisa menambah kategori tanah atau bangunan
             * baru sendiri tanpa menunggu pengembang.
             */
            $table->boolean('requires_certificate')->default(false)->after('depreciation_method');
        });

        // Kategori bawaan yang memang butuh sertifikat: tanah dan kedua jenis bangunan.
        DB::table('asset_categories')
            ->where(function ($query) {
                $query->whereIn('tax_group', ['bangunan_permanen', 'bangunan_tidak_permanen'])
                    ->orWhereIn('code', ['TNH', 'CONTOH-TNH']);
            })
            ->update(['requires_certificate' => true]);
    }

    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('requires_certificate');
        });
    }
};
