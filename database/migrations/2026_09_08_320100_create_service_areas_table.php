<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Area layanan kebersihan.
 *
 * Bukan pengganti tabel lokasi, dan sengaja tidak digabung dengannya. Lokasi menjawab
 * "aset ini ada di mana", jadi kedalamannya gedung, lantai, ruangan. Area layanan menjawab
 * "yang dibersihkan setiap hari apa saja", dan satuannya berbeda: toilet pria lantai 2 adalah
 * satu area meskipun bukan satu ruangan tersendiri di daftar lokasi, sementara satu ruangan
 * besar bisa terpecah jadi dua area kalau petugasnya memang dua orang.
 *
 * Sambungannya ke lokasi tetap ada lewat `location_id`, opsional, supaya orang yang mencari
 * berdasarkan lantai tetap ketemu.
 *
 * Jadwal tidak dibuat sebagai tabel tersendiri. Yang membedakan satu area dari area lain
 * hanya dua hal, yaitu seberapa sering ia dibersihkan dan siapa penanggung jawabnya, dan
 * keduanya cukup jadi kolom di sini. Tabel jadwal baru berguna kalau satu area punya petugas
 * berbeda per hari atau per shift, dan itu belum jadi kebutuhan yang disebutkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_areas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);

            // toilet, ruang_kerja, ruang_rapat, lobi, pantry, koridor, halaman, gudang, lainnya
            $table->string('category', 30)->default('lainnya');

            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();

            // harian, mingguan, bulanan. Dipakai sebagai penyaring saat menyusun lembar
            // pemeriksaan, supaya pemeriksaan harian tidak ikut menyeret area yang memang
            // hanya dijadwalkan sebulan sekali.
            $table->string('frequency', 20)->default('harian');

            // Penanggung jawab. Boleh kosong, karena area baru sering didaftarkan lebih dulu
            // sebelum petugasnya ditentukan.
            $table->foreignId('service_staff_id')->nullable()->constrained('service_staff')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['frequency', 'is_active']);
            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_areas');
    }
};
