<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Peserta perjalanan dinas.
 *
 * Satu surat perjalanan bisa memberangkatkan lebih dari satu orang, tetapi uangnya tetap
 * dipegang satu orang. Karena itu penanggung jawabnya tidak pindah ke tabel ini: ia tetap
 * `business_trips.employee_id`, yang membekukan departemen yang dibebani, menentukan siapa
 * atasan yang menyetujui, dan menjadi nama yang disebut saat sisa uang muka dikembalikan.
 * Tabel ini hanya mencatat siapa saja yang ikut berangkat bersamanya.
 *
 * Tidak ada kolom biaya di sini, dan itu disengaja. Biaya perjalanan dicatat per pengeluaran
 * di `business_trip_expenses`, bukan per orang. Membagi biaya per kepala berarti mengarang
 * pembagian yang tidak pernah ditulis di struk mana pun.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_trip_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_trip_id')->constrained()->cascadeOnDelete();
            // Dibatasi hapusnya. Kartu karyawan yang pernah berangkat dinas tidak boleh
            // lenyap begitu saja dari surat perjalanan yang sudah ditutup.
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->timestamps();

            // Satu orang hanya sekali dalam satu perjalanan. Tanpa ini, daftar peserta bisa
            // menyebut nama yang sama dua kali dan jumlah rombongannya ikut salah.
            $table->unique(['business_trip_id', 'employee_id'], 'business_trip_participants_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_trip_participants');
    }
};
