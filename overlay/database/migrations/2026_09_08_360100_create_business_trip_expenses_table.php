<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rincian pertanggungjawaban satu perjalanan dinas.
 *
 * Satu baris per pengeluaran, bukan satu angka total yang diketik. Bedanya bukan kerapian:
 * total yang diketik tidak bisa diperiksa, sedangkan baris yang berisi tanggal, jenis, dan
 * bukti bisa ditanyakan satu per satu saat angkanya dipersoalkan.
 *
 * Tanggalnya dipakai menentukan biaya ini masuk tahun anggaran yang mana, bukan tanggal
 * perjalanannya. Perjalanan yang berangkat akhir Desember dan pulang awal Januari karenanya
 * terbelah ke dua tahun anggaran sesuai kapan uangnya benar benar keluar, dan itu memang yang
 * benar meskipun terasa merepotkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_trip_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_trip_id')->constrained()->cascadeOnDelete();

            $table->date('expense_date');

            // transport, penginapan, uang_harian, konsumsi, lainnya
            $table->string('category', 20)->default('lainnya');

            $table->string('description', 255);
            $table->decimal('amount', 15, 2);

            // Bukti pengeluaran.
            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();

            $table->timestamps();

            $table->index(['business_trip_id', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_trip_expenses');
    }
};
