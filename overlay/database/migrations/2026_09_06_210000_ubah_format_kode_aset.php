<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Format kode aset diubah mengikuti pola yang lazim dipakai BUMN:
 * [Kode Departemen] - [Kode Akun COA] - [Tahun Perolehan] - [Nomor Urut].
 *
 * Akibatnya awalan kode per kategori tidak dipakai lagi, karena segmen kedua
 * sekarang diambil dari nomor akun kategori dan segmen pertama dari kode
 * departemen pemakai. Kolom kelompok pajak ditambahkan supaya masa manfaat
 * bawaan bisa diturunkan dari PMK 72 Tahun 2023, bukan diketik manual.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            // bangunan_permanen, bangunan_tidak_permanen, kelompok_1 sampai kelompok_4, tidak_disusutkan
            $table->string('tax_group', 30)->nullable()->after('depreciation_method');
        });

        if (Schema::hasColumn('asset_categories', 'code_prefix')) {
            Schema::table('asset_categories', function (Blueprint $table) {
                $table->dropColumn('code_prefix');
            });
        }

        Schema::table('assets', function (Blueprint $table) {
            // Kode gabungan bisa panjang kalau kode departemen dan nomor akun panjang.
            $table->string('code', 80)->change();
        });
    }

    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('tax_group');
            $table->string('code_prefix', 20)->default('GA');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->string('code', 50)->change();
        });
    }
};
