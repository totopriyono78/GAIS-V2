<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekanan: tukang servis AC, bengkel kendaraan, vendor genset, dan sejenisnya.
 *
 * Dibuat sebagai data induk, bukan sebagai kolom teks bebas di perintah kerja. Nama
 * yang diketik bebas akan berubah menjadi "PT Sejuk Abadi", "Sejuk Abadi", dan "sejuk
 * abadi" di dalam satu tabel yang sama dalam hitungan bulan, dan begitu itu terjadi
 * pertanyaan "berapa yang sudah kita bayarkan ke vendor ini tahun lalu" tidak bisa
 * dijawab lagi. Tabel ini juga yang nanti dipakai modul tagihan di Tahap 5.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 200);
            // jasa, barang, keduanya
            $table->string('type', 20)->default('jasa');
            $table->string('specialization', 200)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_number', 40)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
