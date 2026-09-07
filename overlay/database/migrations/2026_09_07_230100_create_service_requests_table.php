<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permintaan perbaikan dari karyawan.
 *
 * Tiket ini adalah pintu masuk, bukan pekerjaan itu sendiri. Begitu tim GA menerimanya,
 * lahir satu perintah kerja korektif dan pekerjaannya hidup di sana: penugasan, biaya,
 * lampiran hasil, dan riwayat aset. Tiketnya tetap ada sebagai catatan siapa melapor
 * kapan, siapa menyetujui, dan berapa lama menunggu.
 *
 * Pemisahan itu yang membuat tidak ada dua daftar pekerjaan yang harus dicocokkan tangan.
 * Tim GA punya satu antrean, yaitu perintah kerja. Karyawan punya satu layar, yaitu
 * tiketnya sendiri, yang statusnya ikut bergerak saat perintah kerjanya selesai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('requester_employee_id')->constrained('employees')->cascadeOnDelete();
            // Disalin saat tiket dibuat. Karyawan bisa pindah departemen, dan tiket lama
            // harus tetap menunjukkan departemen mana yang dulu memintanya.
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            $table->foreignId('service_request_category_id')->nullable()
                ->constrained('service_request_categories')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            // Boleh kosong. Lampu mati di koridor bukan kerusakan satu aset tertentu.
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();

            $table->string('title', 200);
            $table->text('description');
            $table->string('priority', 20)->default('normal');

            // diajukan, disetujui, ditolak, diterima, selesai, dibatalkan
            $table->string('status', 20)->default('diajukan');
            $table->timestamp('submitted_at')->nullable();

            // Persetujuan atasan, yaitu kepala departemen pemohon.
            $table->foreignId('approver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            /*
             * Kenapa tiket ini tidak lewat persetujuan. Diisi sendiri oleh sistem pada tiga
             * keadaan yang kalau dibiarkan akan membuat tiket tersangkut selamanya:
             * departemen belum punya kepala, pemohonnya kepala departemen itu sendiri, dan
             * prioritas mendesak. Ditulis di kolomnya sendiri supaya lompatan itu terbaca
             * di layar, bukan terjadi diam diam.
             */
            $table->string('approval_skipped_reason', 200)->nullable();

            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            // Batas waktu menurut target kategori, dihitung sekali saat diterima.
            $table->timestamp('sla_due_at')->nullable();

            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();

            $table->timestamp('closed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['requester_employee_id', 'status']);
            $table->index(['department_id', 'status']);
        });

        // Perintah kerja tahu tiket asalnya, supaya penyelesaiannya bisa menutup tiket itu.
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('service_request_id')->nullable()
                ->constrained('service_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_request_id');
        });

        Schema::dropIfExists('service_requests');
    }
};
