<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Surat perjalanan dinas beserta uang mukanya.
 *
 * Alurnya lima langkah, dan tiap langkah dikerjakan orang yang berbeda:
 *
 *   Karyawan mengajukan -> Atasan menyetujui -> GA membayarkan uang muka
 *   -> Karyawan mempertanggungjawabkan -> GA menutup dan menghitung selisihnya
 *
 * Keputusan pemilik proyek pada 8 September 2026: ada uang muka, dan sepulangnya
 * dipertanggungjawabkan. Karena itu ada dua angka uang di sini yang tidak boleh tertukar:
 *
 * - `advance_amount`: uang muka yang benar benar dibayarkan sebelum berangkat.
 * - Jumlah baris pertanggungjawaban: biaya yang benar benar terjadi.
 *
 * Selisih keduanya tidak pernah disimpan sebagai kolom. Ia dihitung saat dibaca, dengan
 * alasan yang sama seperti nilai tagihan sejak kiriman K: angka selisih yang disimpan akan
 * basi begitu satu baris pertanggungjawaban diubah, dan selisih yang basi pada urusan uang
 * adalah selisih yang akan dipersoalkan orang.
 *
 * Persetujuan bisa dilewati, mengikuti aturan yang sudah berlaku pada permintaan perbaikan
 * sejak kiriman G dan penggantian biaya sejak kiriman L: departemen yang belum punya kepala,
 * atau pemohon yang justru kepala departemen itu sendiri. Alasan lompatannya ditulis ke
 * kolomnya sendiri supaya terbaca di layar, bukan disimpulkan orang dari kolom kosong.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_trips', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();

            // Departemen yang dibebani. Dibekukan saat pengajuan dibuat, bukan dibaca ulang
            // dari kartu karyawannya nanti, karena karyawan bisa pindah departemen dan
            // perjalanan lama harus tetap menunjukkan siapa yang dulu membiayainya.
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();

            $table->string('destination', 200);
            $table->text('purpose');

            $table->date('start_date');
            $table->date('end_date');

            // darat_dinas, darat_umum, kereta, pesawat, laut, lainnya
            $table->string('transport_mode', 20)->default('darat_umum');

            $table->decimal('estimated_cost', 15, 2)->nullable();

            // diajukan, disetujui, dipertanggungjawabkan, selesai, ditolak, dibatalkan
            $table->string('status', 30)->default('diajukan');

            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('approver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->text('approval_skipped_reason')->nullable();
            $table->text('rejection_reason')->nullable();

            // Uang muka.
            $table->decimal('advance_amount', 15, 2)->nullable();
            $table->timestamp('advance_paid_at')->nullable();
            $table->foreignId('advance_paid_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('advance_note', 255)->nullable();

            // Pertanggungjawaban.
            $table->timestamp('reported_at')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->foreignId('settled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('settlement_note')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'start_date']);
            $table->index(['employee_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_trips');
    }
};
