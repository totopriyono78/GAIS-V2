<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Perintah kerja boleh tidak menyebut aset.
 *
 * Waktu perintah kerja dibangun, satu satunya cara membuatnya adalah dari kartu aset,
 * jadi aset selalu ada dan kolomnya dibuat wajib isi. Permintaan perbaikan mengubah
 * kenyataan itu: lampu mati di koridor, keran bocor di toilet umum, dan bau di ruang
 * panel bukan kerusakan satu aset yang terdaftar. Tiketnya boleh tidak menyebut aset,
 * dan perintah kerja yang lahir darinya pun tidak punya aset untuk disebut.
 *
 * Memaksa pemohon memilih aset hanya akan membuat orang menunjuk aset terdekat yang
 * kebetulan ada di daftar, dan riwayat pemeliharaan aset itu terisi pekerjaan yang
 * tidak pernah menyentuhnya. Lebih baik kolomnya boleh kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Perintah kerja tanpa aset tidak bisa dikembalikan ke keadaan wajib isi tanpa
        // membuang datanya. Yang dibuang hanya baris yang memang tidak punya aset, dan
        // itu disebutkan di sini supaya turunnya migrasi ini tidak terasa seperti
        // kehilangan data yang tidak dijelaskan siapa pun.
        DB::table('work_orders')->whereNull('asset_id')->delete();

        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('asset_id')->nullable(false)->change();
        });
    }
};
