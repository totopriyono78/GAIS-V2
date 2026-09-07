<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jadwal pemeliharaan preventif, satu baris per pekerjaan berulang per aset.
 *
 * Jatuh tempo berikutnya disimpan, bukan dihitung saat dibaca. Terlihat melanggar
 * kebiasaan proyek ini yang selalu menghitung dari data mentah, dan memang begitu,
 * jadi alasannya perlu ditulis: kolom ini yang dipakai menyaring dan mengurutkan
 * ribuan baris di layar dan di dasbor, dan tanggal yang dihitung di PHP tidak bisa
 * dipakai basis data untuk itu. Yang menjaganya tetap benar adalah satu tempat:
 * MaintenanceSchedule::hitungUlangJatuhTempo(), yang dipanggil setiap kali interval
 * atau tanggal terakhir berubah, termasuk saat perintah kerja diselesaikan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('name', 200);
            $table->text('tasks')->nullable();

            $table->unsignedInteger('interval_months');
            $table->date('last_done_date')->nullable();
            $table->date('next_due_date')->nullable();

            // Siapa yang biasanya mengerjakan. Boleh kosong keduanya, artinya belum
            // ditentukan dan akan diisi saat perintah kerjanya dibuat.
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('technician_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->decimal('estimated_cost', 18, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'next_due_date']);
            $table->index(['asset_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
