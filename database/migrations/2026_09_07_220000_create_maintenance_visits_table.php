<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris per jatuh tempo pemeliharaan preventif.
 *
 * Ini yang menjawab pertanyaan "servis kuartal lalu sudah dikerjakan vendor atau belum".
 * Tanpa tabel ini, jawabannya hanya bisa disimpulkan dari ada tidaknya perintah kerja,
 * dan jatuh tempo yang memang tidak pernah dibuatkan perintah kerja tidak meninggalkan
 * jejak apa pun: ia hanya lenyap, dan setahun kemudian tidak ada yang bisa membedakan
 * antara "tidak perlu dikerjakan" dan "terlupakan".
 *
 * Barisnya lahir sendiri: satu kunjungan dibuat begitu jadwalnya dibuat, dan kunjungan
 * berikutnya lahir setiap kali kunjungan sebelumnya ditutup, entah karena dikerjakan
 * atau karena sengaja dilewati. Jadi selalu tepat ada satu kunjungan terbuka per jadwal,
 * tidak pernah nol dan tidak pernah dua.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->constrained('maintenance_schedules')->cascadeOnDelete();
            // Disalin dari jadwalnya supaya riwayat satu aset bisa dibaca tanpa join berlapis.
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();

            // Kunjungan ke berapa sejak jadwal ini dibuat. Dipakai di layar, bukan di kueri.
            $table->unsignedInteger('sequence')->default(1);
            $table->date('due_date');

            // dijadwalkan, dikerjakan, dilewati
            $table->string('status', 20)->default('dijadwalkan');

            $table->date('completed_date')->nullable();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->foreignId('technician_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('result')->nullable();
            $table->decimal('cost', 18, 2)->nullable();

            // Alasan dilewati. Wajib diisi saat status dilewati, dijaga di layar dan model.
            $table->text('skip_reason')->nullable();

            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['maintenance_schedule_id', 'due_date']);
            $table->index(['status', 'due_date']);
            $table->index(['asset_id', 'due_date']);
        });

        // Perintah kerja boleh menempel pada satu kunjungan. Menyelesaikan perintah kerja
        // itu ikut menutup kunjungannya, jadi petugas cukup mengisi satu layar.
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('maintenance_visit_id')->nullable()
                ->constrained('maintenance_visits')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('maintenance_visit_id');
        });

        Schema::dropIfExists('maintenance_visits');
    }
};
