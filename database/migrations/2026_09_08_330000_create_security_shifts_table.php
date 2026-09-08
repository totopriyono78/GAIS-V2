<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jadwal jaga keamanan, satu baris per petugas per shift per tanggal.
 *
 * Rencana dan kenyataan disimpan berdampingan, tidak saling menimpa, sesuai keputusan
 * pemilik proyek pada 8 September 2026. `service_staff_id` adalah siapa yang dijadwalkan,
 * dan itu tidak pernah berubah setelah jadwalnya terbit. Kolom kehadiran di bawahnya adalah
 * apa yang benar benar terjadi.
 *
 * Kalau keduanya digabung menjadi satu kolom "siapa yang jaga", rekap akhir bulan kehilangan
 * pertanyaan yang justru paling sering ditanyakan: berapa kali seseorang tidak masuk, dan
 * berapa kali ia menggantikan orang lain. Menimpa nama yang dijadwalkan dengan nama
 * penggantinya menghapus keduanya sekaligus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_shifts', function (Blueprint $table) {
            $table->id();

            $table->date('shift_date');

            // pagi, siang, malam
            $table->string('shift', 20);

            // Siapa yang dijadwalkan. Tidak berubah setelah jadwalnya terbit.
            $table->foreignId('service_staff_id')->constrained('service_staff')->cascadeOnDelete();

            // Pos jaga. Boleh kosong untuk kantor yang hanya punya satu pintu.
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();

            // belum, hadir, terlambat, tidak_hadir, digantikan
            $table->string('attendance', 20)->default('belum');

            // Hanya terisi kalau kehadirannya digantikan.
            $table->foreignId('replacement_staff_id')->nullable()->constrained('service_staff')->nullOnDelete();

            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            /*
             * Satu petugas tidak bisa dijadwalkan dua kali pada shift dan tanggal yang sama.
             * Penjaga ini ada di basis data, bukan hanya di formulir, karena jadwal jaga
             * sering disusun dua orang sekaligus menjelang pergantian bulan.
             */
            $table->unique(['shift_date', 'shift', 'service_staff_id'], 'security_shifts_unik');

            $table->index(['shift_date', 'shift']);
            $table->index(['attendance', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_shifts');
    }
};
