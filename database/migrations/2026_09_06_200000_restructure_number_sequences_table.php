<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nomor urut dipindah ke tabel periode tersendiri.
 *
 * Alasannya muncul saat impor data aset lama: aset yang diperoleh tahun 2019
 * dan aset baru tahun ini bisa dimasukkan berselang seling, sedangkan satu
 * kolom next_number hanya bisa menghitung untuk satu periode aktif. Dengan
 * satu baris penghitung per periode, urutan tahun berapa pun aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('number_sequences', function (Blueprint $table) {
            $table->string('separator', 3)->default('/')->after('prefix');
        });

        Schema::create('number_sequence_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('number_sequence_id')->constrained('number_sequences')->cascadeOnDelete();
            $table->string('period', 10)->default('');
            $table->unsignedInteger('next_number')->default(1);
            $table->timestamps();

            $table->unique(['number_sequence_id', 'period']);
        });

        Schema::table('number_sequences', function (Blueprint $table) {
            $table->dropColumn(['current_period', 'next_number']);
        });
    }

    public function down(): void
    {
        Schema::table('number_sequences', function (Blueprint $table) {
            $table->string('current_period', 10)->nullable();
            $table->unsignedInteger('next_number')->default(1);
        });

        Schema::dropIfExists('number_sequence_periods');

        Schema::table('number_sequences', function (Blueprint $table) {
            $table->dropColumn('separator');
        });
    }
};
