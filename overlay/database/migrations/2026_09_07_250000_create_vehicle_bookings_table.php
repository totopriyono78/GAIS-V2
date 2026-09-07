<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pemesanan kendaraan.
 *
 * Alurnya sengaja dibuat sama persis dengan permintaan perbaikan, karena orang yang
 * memakainya sama dan mempelajari dua alur berbeda untuk dua hal yang sama sama
 * "minta lalu disetujui" hanya membuang waktu mereka: karyawan mengajukan, kepala
 * departemennya menyetujui, lalu tim GA menugaskan kendaraan dan sopirnya.
 *
 * Kendaraan sengaja belum ditentukan saat diajukan. Pemohon tahu ia perlu mobil untuk
 * berempat ke Bekasi, bukan mobil yang mana, dan membiarkan pemohon memilih sendiri
 * adalah cara tercepat membuat dua orang memesan kendaraan yang sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('requester_employee_id')->constrained('employees')->cascadeOnDelete();
            // Dibekukan saat dibuat, sama seperti pada permintaan perbaikan.
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            $table->string('destination', 200);
            $table->text('purpose');
            $table->unsignedSmallInteger('passenger_count')->nullable();
            $table->boolean('needs_driver')->default(false);

            $table->timestamp('start_at');
            $table->timestamp('end_at');

            // diajukan, disetujui, ditugaskan, berjalan, selesai, ditolak, dibatalkan
            $table->string('status', 20)->default('diajukan');

            // Diisi tim GA saat menugaskan, bukan oleh pemohon.
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('driver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();

            $table->foreignId('approver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->string('approval_skipped_reason', 200)->nullable();

            $table->text('rejection_reason')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Penyaring yang paling sering dipakai: jadwal satu kendaraan pada rentang
            // waktu tertentu, dan itu yang juga dipakai memeriksa bentrok.
            $table->index(['vehicle_id', 'start_at', 'end_at']);
            $table->index(['status', 'start_at']);
            $table->index(['requester_employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_bookings');
    }
};
