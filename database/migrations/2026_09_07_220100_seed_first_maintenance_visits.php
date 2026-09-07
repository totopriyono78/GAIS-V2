<?php

use App\Models\MaintenanceSchedule;
use Illuminate\Database\Migrations\Migration;

/**
 * Kunjungan pertama untuk jadwal yang sudah dibuat sebelum tabel kunjungan ada.
 *
 * Tanpa ini, jadwal yang dibuat kemarin akan punya riwayat kosong selamanya dan tidak
 * akan pernah muncul di daftar jatuh tempo, karena daftar itu sekarang membaca kunjungan.
 * Migrasi ini memakai jalur yang sama dengan pembuatan jadwal biasa, bukan menulis baris
 * mentah, supaya aturan penomoran dan tanggalnya persis sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        MaintenanceSchedule::query()
            ->where('is_active', true)
            ->whereDoesntHave('visits')
            ->chunkById(100, function ($jadwal) {
                foreach ($jadwal as $satu) {
                    $satu->pastikanAdaKunjunganTerbuka();
                }
            });
    }

    public function down(): void
    {
        // Kunjungan yang lahir dari sini tidak bisa dibedakan dari kunjungan yang dibuat
        // orang, dan membuang keduanya berarti membuang riwayat. Dibiarkan apa adanya.
    }
};
