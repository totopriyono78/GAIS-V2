<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Laporan kejadian keamanan.
 *
 * Tanpa kolom status sendiri, sesuai keputusan pemilik proyek pada 8 September 2026: insiden
 * yang butuh perbaikan fisik diteruskan menjadi tiket perbaikan yang alurnya sudah berjalan
 * sejak kiriman G, dan yang tidak butuh perbaikan berhenti sebagai catatan.
 *
 * Membuat mesin status kedua di sini berarti satu insiden punya dua status yang bisa berbeda
 * pendapat: "selesai" di sini sementara tiketnya masih terbuka di sana. Yang dipakai sebagai
 * gantinya hanya dua kolom penutup, dan keadaan insiden dibaca dari ketiadaan atau keadaan
 * tiketnya. Uraiannya ada di IncidentReport::tindakLanjutLabel().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->timestamp('occurred_at');

            // kehilangan, kerusakan, orang_asing, kecelakaan, kebakaran, lainnya
            $table->string('category', 30);

            // rendah, sedang, tinggi
            $table->string('severity', 20)->default('sedang');

            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();

            // Shift yang sedang berjaga saat kejadian. Boleh kosong, karena kejadian bisa
            // dilaporkan orang lain di luar jam jaga mana pun.
            $table->foreignId('security_shift_id')->nullable()->constrained('security_shifts')->nullOnDelete();

            // Petugas yang melaporkan. Boleh kosong kalau yang melapor bukan petugas keamanan.
            $table->foreignId('reported_by_staff_id')->nullable()->constrained('service_staff')->nullOnDelete();

            $table->text('description');

            // Tiket perbaikan yang lahir dari insiden ini.
            $table->foreignId('service_request_id')->nullable()->constrained('service_requests')->nullOnDelete();

            $table->timestamp('closed_at')->nullable();
            $table->text('closing_note')->nullable();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['occurred_at']);
            $table->index(['category', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
