<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log perjalanan kendaraan.
 *
 * Satu baris adalah satu kali kendaraan keluar dan kembali. Jaraknya tidak disimpan,
 * melainkan selisih dua angka odometer, supaya tidak pernah ada baris yang jaraknya
 * tidak cocok dengan odometernya sendiri.
 *
 * Perjalanan boleh tidak punya pemesanan. Kendaraan operasional yang keluar mengantar
 * barang tiap hari tidak masuk akal dipesan lebih dulu, dan memaksanya lewat pemesanan
 * hanya akan membuat orang berhenti mencatat perjalanannya sama sekali.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('vehicle_booking_id')->nullable()->constrained('vehicle_bookings')->nullOnDelete();
            $table->foreignId('driver_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->timestamp('departed_at');
            $table->timestamp('returned_at')->nullable();

            $table->unsignedInteger('start_odometer_km');
            $table->unsignedInteger('end_odometer_km')->nullable();

            $table->string('destination', 200);
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_id', 'departed_at']);
            $table->index('vehicle_booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_trips');
    }
};
