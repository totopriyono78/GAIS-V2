<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu sesi penghitungan fisik barang habis pakai.
 *
 * Bentuknya sesi bebas, bukan periode bulanan, sesuai keputusan pemilik proyek pada 8
 * September 2026. Alasannya konsistensi: opname aset yang sudah dipakai tim sejak kiriman B
 * berbentuk sesi bebas juga, dan memberi dua bentuk berbeda untuk pekerjaan yang sama sama
 * "hitung fisik lalu cocokkan" hanya menambah hal yang perlu dihafal orang.
 *
 * Alurnya menyalin opname aset persis: susun daftar target, mulai penghitungan, catat temuan,
 * selesaikan, lalu terapkan penyesuaian. Penyesuaian dipisah dari penyelesaian karena
 * mengubah stok harus jadi tindakan yang disengaja, bukan efek samping menutup sesi.
 *
 * Perbedaannya dengan opname aset ada di apa yang berubah saat penyesuaian diterapkan. Pada
 * aset yang berubah adalah kolom lokasi dan kondisi. Di sini yang lahir adalah mutasi koreksi,
 * karena stok tidak pernah disimpan sebagai kolom sejak kiriman C dan satu satunya cara
 * mengubahnya adalah menambah baris ke buku stok.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name', 150);

            // Cakupan. Keduanya boleh kosong, dan kosong berarti seluruh barang.
            $table->string('scope_category', 30)->nullable();
            $table->foreignId('scope_location_id')->nullable()->constrained('locations')->nullOnDelete();

            // draft, berjalan, selesai, dibatalkan
            $table->string('status', 20)->default('draft');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamp('adjusted_at')->nullable();
            $table->foreignId('adjusted_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_opnames');
    }
};
