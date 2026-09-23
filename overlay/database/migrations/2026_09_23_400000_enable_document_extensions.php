<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tiga ekstensi PostgreSQL yang dibutuhkan modul dokumen.
 *
 * btree_gist : dipakai constraint satu_versi_berlaku, yang menggabungkan
 *              perbandingan biasa (document_id) dengan perbandingan rentang
 *              tanggal dalam satu exclusion constraint.
 * pg_trgm    : pencarian sebagian kata pada judul dan nomor dokumen, sudah
 *              berguna sejak awal, sebelum pencarian isi berkas dibangun.
 * unaccent   : menormalkan huruf beraksen saat pencarian isi berkas nanti.
 *
 * Ini migrasi pertama di GAIS yang memakai DB::statement. Tiga hal di modul ini
 * memang tidak bisa ditulis lewat Blueprint: exclusion constraint, kolom
 * tsvector yang dibangkitkan otomatis, dan tabel berpartisi.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
    }

    public function down(): void
    {
        // Ekstensi sengaja tidak dilepas. Ia milik basis data, bukan milik modul
        // ini, dan melepasnya bisa merusak indeks modul lain yang memakainya.
    }
};
