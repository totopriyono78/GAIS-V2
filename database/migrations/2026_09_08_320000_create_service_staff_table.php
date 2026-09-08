<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Petugas kebersihan dan keamanan.
 *
 * Tabelnya sendiri, bukan menumpang di karyawan, sesuai keputusan pemilik proyek pada
 * 8 September 2026: petugasnya campuran, sebagian karyawan perusahaan dan sebagian tenaga
 * dari rekanan penyedia.
 *
 * Tenaga dari rekanan tidak boleh dipaksa masuk ke daftar karyawan. Daftar karyawan dipakai
 * untuk hal hal yang tidak berlaku bagi mereka: departemen yang dibebani biaya, atasan yang
 * menyetujui pengajuan, akun yang bisa masuk aplikasi, dan penomoran NIP. Memasukkan tenaga
 * rekanan ke sana berarti setiap layar lain harus belajar mengabaikan mereka satu per satu.
 *
 * Karena itu ada dua cara mengisi baris ini, dan tepat satu yang harus dipakai per baris:
 *
 * - `employee_id` diisi: orangnya karyawan perusahaan. Namanya diambil dari kartu karyawan,
 *   jadi ganti nama di sana ikut terbaca di sini dan tidak ada dua sumber nama.
 * - `name` diisi: orangnya tenaga rekanan. Namanya diketik, dan `vendor_id` menunjuk rekanan
 *   yang memasoknya supaya keluhan tentang orangnya tahu harus disampaikan ke siapa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_staff', function (Blueprint $table) {
            $table->id();

            // Karyawan perusahaan. Kosong berarti orangnya tenaga rekanan.
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();

            // Nama yang diketik, hanya dipakai kalau employee_id kosong.
            $table->string('name', 150)->nullable();

            // Rekanan penyedia tenaga. Boleh kosong, karena tidak semua tenaga luar datang
            // lewat perusahaan penyalur.
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();

            // kebersihan, keamanan, keduanya
            $table->string('kind', 20)->default('kebersihan');

            $table->string('phone', 30)->nullable();
            $table->date('start_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kind', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_staff');
    }
};
