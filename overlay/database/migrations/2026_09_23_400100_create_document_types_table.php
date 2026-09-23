<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Profil tiap jenis dokumen, dan sumber kebenaran tunggal modul ini.
 *
 * "Dokumen" di kantor sebenarnya tiga hal yang siklus hidupnya berbeda, dan
 * memaksakan ketiganya ke satu aturan menghasilkan tabel penuh kolom kosong
 * serta form yang menanyakan hal yang tidak relevan:
 *
 * - controlled  : SOP, kebijakan, sertifikat. Punya versi berlaku, masa
 *                 berlaku, dan pengesahan.
 * - term_based  : kontrak, sewa, polis. Yang menonjol para pihak dan tanggal
 *                 berakhir. Revisinya berupa adendum, bukan versi baru.
 * - attachment  : berita acara, faktur, foto. Tidak punya siklus hidup sendiri,
 *                 ikut catatan induknya, dan karena itu tidak diberi nomor.
 *
 * metadata_schema memegang definisi field per jenis. Satu definisi itu dipakai
 * untuk tiga hal sekaligus: membangun form, memvalidasi isian, dan merender
 * filter. Menambah jenis dokumen berarti menambah baris di sini, bukan menulis
 * kode baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->string('lifecycle', 20);
            $table->boolean('is_versioned')->default(true);
            $table->boolean('needs_approval')->default(false);
            $table->boolean('has_validity')->default(false);
            // Null berarti permanen, atau mengikuti catatan induk untuk jenis
            // attachment. Dibedakan dari nol, yang tidak punya arti.
            $table->integer('retention_years')->nullable();
            $table->string('number_prefix', 10)->nullable();
            $table->jsonb('metadata_schema')->default('{}');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE document_types ADD CONSTRAINT document_types_lifecycle_valid
            CHECK (lifecycle IN ('controlled','term_based','attachment'))");
        DB::statement('ALTER TABLE document_types ADD CONSTRAINT document_types_retention_positive
            CHECK (retention_years IS NULL OR retention_years > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
