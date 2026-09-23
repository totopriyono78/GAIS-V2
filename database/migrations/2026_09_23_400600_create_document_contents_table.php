<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Isi berkas dalam bentuk teks, untuk pencarian.
 *
 * Tabelnya dibuat sekarang meski baru diisi nanti, saat pencarian isi berkas
 * dan pembacaan hasil pindai dikerjakan. Kolom yang sementara kosong jauh lebih
 * murah daripada mengubah struktur setelah ada ribuan dokumen.
 *
 * search_vector dibangkitkan PostgreSQL sendiri setiap kali body berubah, jadi
 * tidak ada kemungkinan indeks pencarian ketinggalan dari isinya. Konfigurasi
 * simple dipakai karena PostgreSQL tidak menyediakan kamus bahasa Indonesia.
 * Ia tidak memotong imbuhan, tetapi hasilnya konsisten dan bisa diperkirakan,
 * dan itu lebih baik daripada kamus bahasa lain yang memotong kata Indonesia
 * dengan aturan yang salah.
 *
 * ocr_confidence disimpan karena pembacaan hasil pindai membuat dokumen bisa
 * ditemukan, bukan membuat isinya bisa dipercaya. Angka itu yang nanti dipakai
 * menandai dokumen yang perlu ditinjau orang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_contents', function (Blueprint $table) {
            $table->unsignedBigInteger('document_version_id')->primary();
            $table->foreign('document_version_id')
                ->references('id')->on('document_versions')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('extraction_method', 20)->nullable();
            $table->decimal('ocr_confidence', 5, 2)->nullable();
            $table->integer('page_count')->nullable();
            $table->timestampTz('extracted_at')->nullable();
        });

        DB::statement("ALTER TABLE document_contents ADD CONSTRAINT document_contents_extraction_valid
            CHECK (extraction_method IS NULL OR extraction_method IN
                ('pdftotext','libreoffice','ocr','plaintext','none'))");

        DB::statement("ALTER TABLE document_contents
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (to_tsvector('simple', coalesce(body,''))) STORED");

        DB::statement('CREATE INDEX document_contents_search ON document_contents USING gin (search_vector)');
    }

    public function down(): void
    {
        Schema::dropIfExists('document_contents');
    }
};
