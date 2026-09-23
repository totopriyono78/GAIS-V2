<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inilah yang membuat modul dokumen menjadi tempat penyimpanan bersama,
 * bukan modul kesekian yang punya lemari sendiri.
 *
 * GAIS sekarang menyimpan lampiran di sepuluh tabel berbeda, masing masing
 * dengan kolom file_path sendiri. Akibatnya kewajiban seperti "simpan sepuluh
 * tahun lalu musnahkan dengan berita acara" harus ditulis sepuluh kali, dan
 * pasti ada yang terlewat.
 *
 * Lewat tabel ini, modul mana pun bisa menautkan dokumen tanpa tabel lampiran
 * sendiri. Bagi pemakainya, layar Vendor atau layar Aset tetap menampilkan
 * daftar lampirannya seperti biasa, hanya sumber datanya yang jadi satu.
 *
 * Sepuluh tabel lampiran yang sudah ada sengaja dibiarkan dulu. Modul baru
 * memakai tabel ini, yang lama dicicil setelah modul dokumen terbukti stabil.
 *
 * Arti tiap relasi:
 * - attachment : lampiran biasa, dokumen ikut catatan induknya
 * - reference  : dirujuk, tetapi berdiri sendiri
 * - amendment  : adendum kontrak
 * - supersedes : menggantikan dokumen lain
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->restrictOnDelete();
            $table->string('linkable_type', 150);
            $table->unsignedBigInteger('linkable_id');
            $table->string('relation', 20)->default('attachment');
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->unique(
                ['document_id', 'linkable_type', 'linkable_id', 'relation'],
                'document_links_unik',
            );
            $table->index(['linkable_type', 'linkable_id']);
        });

        DB::statement("ALTER TABLE document_links ADD CONSTRAINT document_links_relation_valid
            CHECK (relation IN ('attachment','reference','supersedes','amendment'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('document_links');
    }
};
