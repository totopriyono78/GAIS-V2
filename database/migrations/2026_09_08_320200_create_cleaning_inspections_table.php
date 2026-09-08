<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu putaran pemeriksaan kebersihan oleh pengawas GA.
 *
 * Yang tercatat di sini adalah hasil pemeriksaan, bukan pengakuan petugas. Keputusan pemilik
 * proyek pada 8 September 2026: pengawas GA yang berkeliling dan mencentang, karena sebagian
 * petugasnya tenaga rekanan yang tidak punya akun dan tidak membawa aplikasi ini ke mana mana.
 *
 * Bedanya dengan opname, dan ini disengaja: di sini tidak ada langkah menyusun daftar yang
 * terpisah. Daftar areanya lahir sendiri begitu putaran dibuat, karena tidak ada angka yang
 * perlu dibekukan lebih dulu. Opname membekukan stok supaya pergerakan di sela penghitungan
 * ketahuan; kebersihan tidak punya angka yang bisa bergerak seperti itu.
 *
 * Hanya ada tiga keadaan: sedang diperiksa, selesai, dan dibatalkan. Tidak ada langkah
 * penerapan seperti pada opname, karena pemeriksaan kebersihan tidak mengubah angka apa pun
 * di tempat lain. Ia adalah catatannya sendiri.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->date('inspection_date');

            // Cakupan. Ketiganya boleh kosong, dan kosong berarti tidak menyaring apa pun.
            $table->string('scope_category', 30)->nullable();
            $table->string('scope_frequency', 20)->nullable();
            $table->foreignId('scope_location_id')->nullable()->constrained('locations')->nullOnDelete();

            // berjalan, selesai, dibatalkan
            $table->string('status', 20)->default('berjalan');

            $table->timestamp('finished_at')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'inspection_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_inspections');
    }
};
