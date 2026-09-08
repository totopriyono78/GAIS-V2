<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permintaan pemakaian ATK.
 *
 * Satu permintaan adalah satu daftar belanja milik satu orang, dan alurnya tiga langkah:
 *
 *   Karyawan meminta  ->  Atasan menyetujui  ->  Tim GA menyerahkan barangnya
 *
 * Langkah ketiga bukan sekadar perubahan status. Saat barang diserahkan, satu mutasi
 * "barang keluar" lahir untuk tiap baris, lengkap dengan departemen pemohon. Itulah yang
 * membuat pemakaian ATK per departemen berhenti diketik tangan, dan sejak kiriman J angka
 * itu langsung menjadi realisasi anggaran kategori ATK.
 *
 * Departemen wajib diisi, berbeda dari penggantian biaya yang boleh dikosongkan. Alasannya
 * sudah ditetapkan sejak kiriman C: barang keluar wajib menyebut departemen, karena angka
 * itu yang dipakai membandingkan pagu ATK tiap departemen dengan pemakaian sebenarnya.
 * Karena departemennya selalu ada, hanya tersisa dua keadaan yang melewati persetujuan
 * atasan, bukan tiga seperti pada penggantian biaya.
 *
 * Departemen dibekukan saat permintaan dibuat, tidak dibaca ulang dari karyawannya nanti.
 * Karyawan bisa pindah departemen, dan permintaan bulan lalu harus tetap menunjukkan
 * departemen mana yang dulu memakai barangnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();

            $table->string('purpose', 200);
            $table->date('needed_date')->nullable();
            $table->text('notes')->nullable();

            // draft, diajukan, disetujui, diserahkan, ditolak, dibatalkan
            $table->string('status', 20)->default('draft');
            $table->timestamp('submitted_at')->nullable();

            // Langkah kedua: atasan, yaitu kepala departemen pemohon.
            $table->foreignId('approver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->text('approval_skipped_reason')->nullable();

            // Langkah ketiga: tim GA menyerahkan barangnya dan stok berkurang.
            $table->foreignId('issued_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('issued_at')->nullable();
            $table->text('issue_note')->nullable();

            $table->text('rejection_reason')->nullable();
            // Ditolak pada langkah yang mana. Yang memperbaiki perlu tahu apakah atasannya
            // atau tim GA yang mengembalikan, karena keduanya menolak karena hal berbeda.
            $table->string('rejected_stage', 20)->nullable();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
            $table->index(['employee_id', 'status']);
            $table->index(['department_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_requests');
    }
};
