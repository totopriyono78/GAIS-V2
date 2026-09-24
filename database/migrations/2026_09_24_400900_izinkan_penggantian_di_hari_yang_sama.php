<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Melonggarkan document_versions_valid_period dari > menjadi >=.
 *
 * Kejadian yang memicunya: sebuah versi disahkan pagi ini, lalu versi
 * penggantinya disahkan sore ini juga. Pengesahan menutup versi lama pada
 * tanggal berlakunya yang baru, sehingga versi lama punya effective_from dan
 * effective_until yang sama persis. Constraint lama menolaknya, dan pemakainya
 * cuma melihat layar galat.
 *
 * Melonggarkannya aman, dan alasannya ada di semantik daterange yang dipakai
 * constraint satu_versi_berlaku:
 *
 *   daterange('2026-09-24', '2026-09-24', '[)')  ->  rentang kosong
 *
 * Rentang kosong tidak bertindih dengan apa pun, dan operator @> tidak pernah
 * mencocokkannya. Jadi versi yang ditutup di hari yang sama otomatis tidak
 * pernah terjawab sebagai versi yang berlaku pada tanggal mana pun, termasuk
 * pada tanggal itu sendiri. Itu memang yang benar: versi tersebut tidak pernah
 * berlaku sehari penuh. DocumentVersion::berlakuPada() di sisi PHP sudah
 * memakai aturan yang sama, jadi keduanya tetap sejalan.
 *
 * Yang TIDAK dilonggarkan: effective_until tetap tidak boleh lebih awal dari
 * effective_from. Kasus itu bukan penggantian di hari yang sama melainkan
 * tanggal yang mundur, dan sekarang ditolak lebih dulu di
 * PengelolaDokumen dengan pesan yang bisa dibaca.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE document_versions DROP CONSTRAINT IF EXISTS document_versions_valid_period');
        DB::statement('ALTER TABLE document_versions ADD CONSTRAINT document_versions_valid_period
            CHECK (effective_until IS NULL OR effective_from IS NULL OR effective_until >= effective_from)');
    }

    /**
     * Dibalikkan ke aturan lama. Kalau sudah ada versi yang ditutup di hari
     * yang sama, PostgreSQL akan menolak pengembalian ini, dan penolakan itu
     * memang benar: barisnya sah, aturan lamanya yang tidak.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE document_versions DROP CONSTRAINT IF EXISTS document_versions_valid_period');
        DB::statement('ALTER TABLE document_versions ADD CONSTRAINT document_versions_valid_period
            CHECK (effective_until IS NULL OR effective_from IS NULL OR effective_until > effective_from)');
    }
};
