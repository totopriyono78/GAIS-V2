<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penggantian biaya karyawan.
 *
 * Satu pengajuan adalah satu amplop berisi beberapa struk milik satu orang. Nilainya tidak
 * disimpan di sini melainkan dijumlahkan dari barisnya, dengan alasan yang sama seperti
 * tagihan rekanan: pengajuan yang totalnya tidak sama dengan jumlah struknya adalah cacat
 * yang paling mahal ditemukan belakangan, saat uangnya sudah terlanjur ditransfer.
 *
 * Departemen yang dibebani disimpan di sini, bukan di tiap baris. Struk milik satu orang
 * hampir selalu jatuh ke satu departemen, dan meminta orang memilih departemen di tiap
 * struk berarti membebani seratus pengajuan demi satu perkecualian. Kolomnya boleh kosong
 * untuk belanja kantor bersama, dan angkanya lalu muncul sebagai biaya yang belum
 * terbebankan, bukan hilang.
 *
 * Departemen dibekukan saat pengajuan dibuat, tidak dibaca ulang dari karyawannya nanti.
 * Karyawan bisa pindah departemen, dan pengajuan tahun lalu harus tetap menunjukkan
 * departemen mana yang dulu menanggung biayanya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            $table->string('title', 200);
            $table->text('notes')->nullable();

            // draft, diajukan, diperiksa, disetujui, dibayar, ditolak, dibatalkan
            $table->string('status', 20)->default('draft');
            $table->timestamp('submitted_at')->nullable();

            // Langkah pertama: atasan, yaitu kepala departemen pemohon.
            $table->foreignId('approver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->text('approval_skipped_reason')->nullable();

            // Langkah kedua: tim GA memeriksa struknya.
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->text('rejection_reason')->nullable();
            // Ditolak pada langkah yang mana. Yang memperbaiki perlu tahu apakah atasannya
            // atau tim GA yang mengembalikan, karena keduanya menolak karena hal berbeda.
            $table->string('rejected_stage', 20)->nullable();

            $table->date('paid_date')->nullable();
            $table->string('payment_reference', 80)->nullable();
            $table->foreignId('paid_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'submitted_at']);
            $table->index(['employee_id', 'status']);
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};
