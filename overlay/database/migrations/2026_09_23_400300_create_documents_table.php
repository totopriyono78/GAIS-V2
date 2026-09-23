<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris per dokumen, apa pun jenisnya.
 *
 * current_version_id menunjuk versi yang BERLAKU, bukan yang terakhir diunggah.
 * Perbedaan itu yang menentukan modul ini benar atau tidak: kalau keduanya
 * disamakan, seseorang mengunggah draf revisi dan seluruh perusahaan langsung
 * melihat draf itu sebagai SOP yang berlaku.
 *
 * confidentiality adalah dimensi yang berdiri sendiri dari izin. Seseorang bisa
 * punya izin documents.read dan tetap tidak boleh membuka dokumen berklasifikasi
 * restricted, karena yang menentukan adalah clearance_level miliknya. Jangan
 * pernah melebur keduanya menjadi nama izin baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->nullable()
                ->constrained('document_categories')->restrictOnDelete();
            $table->string('document_number', 60)->nullable()->unique();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->string('confidentiality', 20)->default('internal');
            // Diisi foreign key-nya di migrasi berikutnya, setelah tabel versi ada.
            $table->unsignedBigInteger('current_version_id')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('owner_department_id')->nullable()
                ->constrained('departments')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->timestampTz('archived_at')->nullable();
            $table->timestampTz('destroyed_at')->nullable();

            $table->index(['document_type_id', 'status']);
        });

        DB::statement("ALTER TABLE documents ADD CONSTRAINT documents_confidentiality_valid
            CHECK (confidentiality IN ('public','internal','confidential','restricted'))");
        DB::statement("ALTER TABLE documents ADD CONSTRAINT documents_status_valid
            CHECK (status IN ('active','archived','destroyed'))");

        // Filter metadata tanpa perlu satu kolom untuk tiap field.
        DB::statement('CREATE INDEX documents_metadata_gin ON documents USING gin (metadata jsonb_path_ops)');
        // Pencarian sebagian kata pada judul, sudah bisa dipakai sebelum
        // pencarian isi berkas dibangun.
        DB::statement('CREATE INDEX documents_title_trgm ON documents USING gin (title gin_trgm_ops)');
        // Sebagian besar layar hanya menampilkan dokumen aktif, jadi indeksnya
        // dibatasi ke baris itu saja supaya tetap kecil.
        DB::statement("CREATE INDEX documents_active_only ON documents (category_id) WHERE status = 'active'");
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
