<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kendaraan dinas.
 *
 * Kendaraan tidak dibuat sebagai daftar terpisah dari aset, melainkan sebagai keterangan
 * tambahan di atas satu baris aset yang sudah ada. Alasannya sederhana: mobil adalah aset
 * tetap. Ia disusutkan, dipindahkan antar departemen, diperbaiki lewat perintah kerja,
 * dan suatu hari dilepas. Semua itu sudah dibangun di modul aset, dan membuat daftar
 * kendaraan yang berdiri sendiri berarti membangunnya untuk kedua kalinya, lalu
 * menghabiskan sisa umur aplikasi mencocokkan dua daftar yang pelan pelan berbeda.
 *
 * Yang ada di tabel ini hanya yang benar benar khas kendaraan dan tidak masuk akal
 * dipasang di setiap aset: nomor polisi, nomor rangka dan mesin, jenis bahan bakar,
 * odometer, dan cara kendaraan itu dipakai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Satu aset hanya boleh punya satu baris kendaraan. Kunci unik ini yang
            // menjaga janji "kendaraan adalah aset", bukan sekadar kebiasaan pengisian.
            $table->foreignId('asset_id')->unique()->constrained('assets')->cascadeOnDelete();

            $table->string('plate_number', 20)->unique();
            // mobil_penumpang, mobil_operasional, pikap, truk, motor, lainnya
            $table->string('vehicle_type', 30);
            /*
             * Cara kendaraan ini dipakai, dan itu menentukan layar mana yang mengurusnya:
             * pool dipesan bergantian, pegangan melekat pada satu orang, operasional
             * dipakai untuk kirim barang dan keperluan lapangan.
             */
            $table->string('usage_mode', 20)->default('pool');

            $table->string('chassis_number', 50)->nullable();
            $table->string('engine_number', 50)->nullable();
            $table->string('color', 40)->nullable();
            $table->unsignedSmallInteger('production_year')->nullable();
            // bensin, solar, listrik, hybrid
            $table->string('fuel_type', 20)->nullable();
            // manual, matik
            $table->string('transmission', 20)->nullable();
            $table->unsignedSmallInteger('seat_capacity')->nullable();
            // Untuk pikap dan truk, dalam kilogram. Kosong untuk kendaraan penumpang.
            $table->unsignedInteger('payload_kg')->nullable();

            // Sopir tetap, kalau ada. Kendaraan pool biasanya tidak punya.
            $table->foreignId('default_driver_employee_id')->nullable()
                ->constrained('employees')->nullOnDelete();

            /*
             * Odometer terakhir yang diketahui, dalam kilometer.
             *
             * Ini satu satunya angka di aplikasi ini yang sengaja disimpan padahal bisa
             * dihitung dari catatan lain. Alasannya: saat log perjalanan dan pengisian
             * BBM dibangun, angka ini yang dipakai memvalidasi bahwa odometer tidak
             * mundur, dan menghitungnya ulang dari seluruh riwayat setiap kali orang
             * mengetik satu baris terlalu mahal. Yang mengisinya adalah catatan
             * perjalanan dan pengisian BBM, bukan tangan, kecuali saat pendataan awal.
             */
            $table->unsignedInteger('last_odometer_km')->nullable();
            $table->date('last_odometer_date')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['usage_mode', 'is_active']);
            $table->index('vehicle_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
