<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\MaintenanceVisit;
use App\Models\WorkOrder;
use Illuminate\Support\Carbon;

/**
 * Satu tempat untuk menjawab "berapa biaya pemeliharaan".
 *
 * Biaya pemeliharaan tersimpan di dua tabel, dan itu bukan kelalaian melainkan akibat
 * langsung dari dua jalur pencatatan yang memang berbeda:
 *
 * - Kunjungan preventif bisa ditutup langsung dari riwayat jadwal, tanpa perintah kerja.
 *   Biayanya tersimpan di kunjungan itu.
 * - Pekerjaan korektif selalu lahir sebagai perintah kerja. Biayanya tersimpan di sana.
 * - Kunjungan preventif yang dikerjakan lewat perintah kerja menyalin biayanya ke
 *   kunjungan saat perintah kerja ditutup, jadi angkanya ada di kedua tempat.
 *
 * Menjumlahkan salah satu tabel saja menghasilkan angka yang kurang; menjumlahkan
 * keduanya menghasilkan angka yang dobel. Aturan yang benar hanya satu, dan ditulis
 * sekali di sini: seluruh kunjungan yang dikerjakan, ditambah perintah kerja selesai
 * yang tidak menempel pada kunjungan mana pun.
 *
 * Kelas ini lahir dari satu cacat yang tertangkap saat pengujian: dasbor menyebut
 * Rp 1.250.000 untuk bulan yang sama ketika layar jadwal menyebut Rp 2.100.000. Dua
 * angka berbeda untuk pertanyaan yang sama adalah cara tercepat membuat orang berhenti
 * mempercayai seluruh layar.
 */
class BiayaPemeliharaan
{
    /** Biaya yang benar benar keluar dalam satu rentang tanggal. */
    public static function antara(Carbon|string $dari, Carbon|string $sampai): float
    {
        $dari = $dari instanceof Carbon ? $dari->toDateString() : $dari;
        $sampai = $sampai instanceof Carbon ? $sampai->toDateString() : $sampai;

        $kunjungan = (float) MaintenanceVisit::query()
            ->where('status', 'dikerjakan')
            ->whereBetween('completed_date', [$dari, $sampai])
            ->sum('cost');

        $korektif = (float) WorkOrder::query()
            ->selesai()
            ->whereNull('maintenance_visit_id')
            ->whereBetween('completed_date', [$dari, $sampai])
            ->sum('cost');

        return round($kunjungan + $korektif, 2);
    }

    /**
     * Biaya per bulan, dikelompokkan menurut bulan pengerjaannya.
     *
     * @return array<string, float> berkunci YYYY-MM
     */
    public static function perBulan(Carbon $mulai, int $jumlahBulan): array
    {
        $hasil = [];

        for ($i = 0; $i < $jumlahBulan; $i++) {
            $bulan = $mulai->copy()->addMonths($i);

            $hasil[$bulan->format('Y-m')] = self::antara(
                $bulan->copy()->startOfMonth(),
                $bulan->copy()->endOfMonth(),
            );
        }

        return $hasil;
    }

    /** Seluruh biaya pemeliharaan yang pernah keluar untuk satu aset. */
    public static function untukAset(Asset $aset): float
    {
        $kunjungan = (float) MaintenanceVisit::query()
            ->where('asset_id', $aset->getKey())
            ->where('status', 'dikerjakan')
            ->sum('cost');

        $korektif = (float) WorkOrder::query()
            ->where('asset_id', $aset->getKey())
            ->selesai()
            ->whereNull('maintenance_visit_id')
            ->sum('cost');

        return round($kunjungan + $korektif, 2);
    }

    /** Jumlah pekerjaan yang selesai dalam satu rentang, preventif maupun korektif. */
    public static function jumlahSelesaiAntara(Carbon|string $dari, Carbon|string $sampai): int
    {
        $dari = $dari instanceof Carbon ? $dari->toDateString() : $dari;
        $sampai = $sampai instanceof Carbon ? $sampai->toDateString() : $sampai;

        return MaintenanceVisit::query()
            ->where('status', 'dikerjakan')
            ->whereBetween('completed_date', [$dari, $sampai])
            ->count()
            + WorkOrder::query()
                ->selesai()
                ->whereNull('maintenance_visit_id')
                ->whereBetween('completed_date', [$dari, $sampai])
                ->count();
    }
}
