<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Perintah kerja pemeliharaan, preventif maupun korektif.
 *
 * Satu tabel untuk keduanya, bukan dua tabel yang mirip. Yang membedakan hanya asal
 * usulnya: preventif lahir dari jadwal, korektif lahir dari kerusakan yang dilaporkan.
 * Selebihnya sama persis, dan orang yang bertanya "apa saja yang pernah dikerjakan pada
 * aset ini" ingin melihat keduanya dalam satu daftar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();

            // preventif, korektif
            $table->string('type', 20)->default('korektif');
            // Jadwal asalnya, kalau perintah kerja ini lahir dari jadwal preventif.
            // Jadwalnya boleh dihapus tanpa ikut menghapus riwayat pekerjaannya.
            $table->foreignId('maintenance_schedule_id')->nullable()
                ->constrained('maintenance_schedules')->nullOnDelete();

            // rendah, normal, tinggi, mendesak
            $table->string('priority', 20)->default('normal');
            // dibuka, dikerjakan, selesai, dibatalkan
            $table->string('status', 20)->default('dibuka');

            $table->date('reported_date');
            $table->foreignId('reported_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('problem');

            $table->date('scheduled_date')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('technician_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->date('completed_date')->nullable();
            $table->text('work_done')->nullable();
            $table->decimal('cost', 18, 2)->nullable();
            // Kondisi aset setelah dikerjakan, disalin ke asetnya saat perintah kerja
            // diselesaikan. Boleh kosong, artinya kondisinya tidak berubah.
            $table->string('condition_after', 30)->nullable();

            $table->text('cancel_reason')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['asset_id', 'reported_date']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
