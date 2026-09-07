<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengisian BBM.
 *
 * Dicatat per pengisian beserta odometernya, bukan sebagai total per bulan, karena
 * hanya dengan begitu konsumsi kilometer per liter bisa dihitung, dan hanya dengan
 * angka itu kendaraan yang tiba tiba jadi boros bisa ketahuan sebelum servis besar.
 *
 * Kolom is_full_tank yang membuat perhitungan itu jujur. Konsumsi hanya bisa dihitung
 * antara dua pengisian penuh, karena hanya di dua titik itulah isi tangkinya diketahui
 * sama. Pengisian setengah tangki tetap dicatat dan tetap masuk hitungan liter, tetapi
 * tidak pernah menjadi titik ukur sendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_refuelings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            $table->date('filled_at');
            $table->unsignedInteger('odometer_km');
            $table->decimal('liters', 8, 2);
            $table->decimal('cost', 15, 2)->nullable();

            $table->string('station', 120)->nullable();
            // bensin, solar, listrik. Boleh berbeda dari bahan bakar kendaraan untuk
            // kendaraan yang memang bisa diisi lebih dari satu jenis.
            $table->string('fuel_type', 20)->nullable();
            $table->boolean('is_full_tank')->default(true);

            $table->foreignId('driver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_id', 'odometer_km']);
            $table->index(['vehicle_id', 'filled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_refuelings');
    }
};
