<?php

use App\Support\Berkas;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Riwayat versi satu dokumen, beserta berkas tiap versinya.
 *
 * Tiga hal di sini yang tidak bisa dijaga dari sisi PHP:
 *
 * 1. satu_versi_berlaku. Dua versi tidak boleh berlaku pada periode yang
 *    bertindih. Pemeriksaan di PHP tetap bocor kalau dua pengesahan diproses
 *    bersamaan, karena keduanya bisa lolos sebelum salah satunya tersimpan.
 *    Constraint ini menolak yang kedua di tingkat basis data, dengan SQLSTATE
 *    23P01. effective_until yang kosong berarti berlaku tanpa batas akhir, dan
 *    daterange sudah menangani itu sendiri.
 *
 * 2. approved_needs_date. Versi yang disahkan wajib punya tanggal mulai
 *    berlaku, sebab tanpa tanggal itu baris tersebut lolos dari constraint di
 *    atas dan pengamanannya jadi sia sia untuk dokumen itu.
 *
 * 3. restrictOnDelete pada document_id. Riwayat versi tidak boleh ikut terhapus
 *    saat seseorang menghapus dokumennya, justru karena riwayat itu yang
 *    diperiksa saat audit.
 *
 * Status versi lama sengaja TIDAK diubah menjadi superseded saat versi baru
 * disahkan. Constraint memakai rentang tanggal, jadi beberapa baris berstatus
 * approved boleh hidup bersama selama periodenya tidak bertindih. Kalau
 * statusnya diubah, pertanyaan "versi mana yang berlaku 15 Maret lalu" tidak
 * bisa dijawab lagi, padahal justru itu yang ditanyakan auditor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->restrictOnDelete();
            $table->integer('version_number');
            $table->string('storage_disk', 20)->default(Berkas::DISK);
            $table->text('storage_path');
            // sha256, dipakai dua hal: mendeteksi unggahan kembar, dan menjadi
            // bukti bahwa berkas tidak berubah setelah disahkan.
            $table->char('file_hash', 64);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 150);
            $table->string('original_name', 255);
            $table->string('status', 20)->default('draft');
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->text('change_note')->nullable();
            $table->string('scan_status', 20)->default('pending');
            $table->foreignId('uploaded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'version_number']);
            $table->index('file_hash');
            $table->index(['document_id', 'status']);
        });

        DB::statement("ALTER TABLE document_versions ADD CONSTRAINT document_versions_status_valid
            CHECK (status IN ('draft','in_review','approved','rejected','superseded'))");
        DB::statement("ALTER TABLE document_versions ADD CONSTRAINT document_versions_scan_valid
            CHECK (scan_status IN ('pending','clean','infected','skipped'))");
        DB::statement("ALTER TABLE document_versions ADD CONSTRAINT document_versions_approved_needs_date
            CHECK (status <> 'approved' OR effective_from IS NOT NULL)");
        DB::statement('ALTER TABLE document_versions ADD CONSTRAINT document_versions_valid_period
            CHECK (effective_until IS NULL OR effective_from IS NULL OR effective_until > effective_from)');

        DB::statement("
            ALTER TABLE document_versions
            ADD CONSTRAINT satu_versi_berlaku
            EXCLUDE USING gist (
                document_id WITH =,
                daterange(effective_from, effective_until, '[)') WITH &&
            ) WHERE (status = 'approved')
        ");

        DB::statement('ALTER TABLE documents
            ADD CONSTRAINT documents_current_version_fk
            FOREIGN KEY (current_version_id) REFERENCES document_versions(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE documents DROP CONSTRAINT IF EXISTS documents_current_version_fk');
        Schema::dropIfExists('document_versions');
    }
};
