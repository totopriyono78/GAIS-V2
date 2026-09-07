<?php

namespace App\Services;

use App\Support\Periode;

/**
 * Mesin hitung penyusutan.
 *
 * Kelas ini sengaja tidak menyentuh basis data dan tidak tahu apa apa tentang model
 * Aset. Isinya hanya aritmetika: diberi angka perolehan, nilai sisa, umur, metode,
 * dan periode mulai, ia menghasilkan jadwal bulan per bulan. Dipisah begitu supaya
 * angkanya bisa diuji sendiri terhadap hitungan tangan tim finance, tanpa perlu basis
 * data, dan supaya satu satunya tempat rumus penyusutan ditulis adalah di sini.
 *
 * Dua hal yang paling mudah salah di penyusutan, dan bagaimana keduanya ditangani:
 *
 * 1. Recehan pembulatan. Beban dibulatkan dua angka di belakang koma tiap bulan, dan
 *    sisa pembagian yang tidak habis akan menumpuk. Bangunan 24,5 miliar dengan umur
 *    240 bulan menyisakan 80 sen yang tidak pernah tersusutkan. Bulan terakhir menyerap
 *    seluruh sisanya, jadi nilai buku di akhir umur tepat sama dengan nilai sisa, bukan
 *    mendekati.
 *
 * 2. Penyerapan itu tidak boleh dipakai di semua metode. Saldo menurun murni memang
 *    dirancang menyisakan nilai buku di akhir umur; menghabiskannya di bulan terakhir
 *    justru mengingkari metodenya. Karena itu penyerapan hanya berlaku kalau jadwalnya
 *    memang menargetkan nilai sisa.
 */
class JadwalPenyusutan
{
    public const GARIS_LURUS = 'garis_lurus';

    public const SALDO_MENURUN = 'saldo_menurun';

    public const TIDAK_DISUSUTKAN = 'tidak_disusutkan';

    /**
     * Jadwal penyusutan bulan per bulan, dari periode mulai sampai periode akhir umur
     * ekonomisnya. Yang dikembalikan adalah seluruh umur aset, bukan hanya sampai hari
     * ini, supaya pemanggilnya bisa mengambil bagian mana pun yang dibutuhkan.
     *
     * @param  float  $perolehan  Nilai perolehan aset
     * @param  float  $nilaiSisa  Perkiraan nilai di akhir umur ekonomis
     * @param  int  $umurBulan  Umur ekonomis dalam bulan
     * @param  string  $metode  garis_lurus, saldo_menurun, atau tidak_disusutkan
     * @param  string  $mulai  Periode pertama yang disusutkan, YYYY-MM
     * @param  bool  $habisAkhir  Saldo menurun: sisanya dihabiskan di tahun terakhir
     * @return array<int, array{periode: string, urutan: int, beban: float, akumulasi: float, nilai_buku: float}>
     */
    public static function susun(
        float $perolehan,
        float $nilaiSisa,
        int $umurBulan,
        string $metode,
        string $mulai,
        bool $habisAkhir = true,
    ): array {
        if ($umurBulan <= 0 || $metode === self::TIDAK_DISUSUTKAN || ! Periode::sah($mulai)) {
            return [];
        }

        $dasar = max($perolehan - $nilaiSisa, 0.0);

        if ($dasar <= 0) {
            return [];
        }

        $baris = [];
        $akumulasi = 0.0;

        // Saldo menurun ganda: tarif setahun dua kali tarif garis lurus.
        $umurTahun = $umurBulan / 12;
        $tarifTahunan = $umurTahun > 0 ? (2 / $umurTahun) : 0.0;

        $bebanTahunIni = null;

        // Penyerapan sisa hanya untuk jadwal yang memang menargetkan nilai sisa.
        $menargetkanNilaiSisa = $metode === self::GARIS_LURUS || $habisAkhir;

        for ($i = 0; $i < $umurBulan; $i++) {
            $bulanKeDalamTahun = $i % 12;

            if ($metode === self::GARIS_LURUS) {
                $beban = $dasar / $umurBulan;
            } else {
                if ($bulanKeDalamTahun === 0) {
                    $nilaiBukuAwalTahun = $perolehan - $akumulasi;
                    $sisaTahun = ($umurBulan - $i) / 12;

                    // Tahun terakhir: sisanya dihabiskan, mengikuti kebiasaan pajak.
                    $bebanTahunIni = ($habisAkhir && $sisaTahun <= 1.0001)
                        ? max($nilaiBukuAwalTahun - $nilaiSisa, 0.0)
                        : $nilaiBukuAwalTahun * $tarifTahunan;
                }

                $bulanTersisaTahunIni = min(12 - $bulanKeDalamTahun, $umurBulan - $i);
                $beban = $bebanTahunIni / min(12, max($bulanTersisaTahunIni + $bulanKeDalamTahun, 1));
            }

            $sisaDasar = max($dasar - $akumulasi, 0.0);

            $beban = ($menargetkanNilaiSisa && $i === $umurBulan - 1)
                ? $sisaDasar
                : min($beban, $sisaDasar);
            $beban = round($beban, 2);

            if ($beban <= 0 && $akumulasi >= $dasar - 0.005) {
                break;
            }

            $akumulasi = round($akumulasi + $beban, 2);

            $baris[] = [
                'periode' => Periode::tambah($mulai, $i),
                'urutan' => $i,
                'beban' => $beban,
                'akumulasi' => $akumulasi,
                'nilai_buku' => round($perolehan - $akumulasi, 2),
            ];
        }

        return $baris;
    }

    /**
     * Satu baris jadwal untuk periode tertentu, atau null kalau periode itu di luar
     * umur ekonomisnya.
     *
     * @param  array<int, array{periode: string, urutan: int, beban: float, akumulasi: float, nilai_buku: float}>  $jadwal
     * @return array{periode: string, urutan: int, beban: float, akumulasi: float, nilai_buku: float}|null
     */
    public static function pada(array $jadwal, string $periode): ?array
    {
        foreach ($jadwal as $baris) {
            if ($baris['periode'] === $periode) {
                return $baris;
            }
        }

        return null;
    }

    /**
     * Akumulasi menurut jadwal sampai dengan periode tertentu. Dipakai untuk mengisi
     * akumulasi awal aset lama yang penyusutannya sudah berjalan sebelum aplikasi ini
     * dipakai, supaya angkanya tidak perlu diketik satu per satu.
     *
     * @param  array<int, array{periode: string, urutan: int, beban: float, akumulasi: float, nilai_buku: float}>  $jadwal
     */
    public static function akumulasiSampai(array $jadwal, string $periode): float
    {
        $akumulasi = 0.0;

        foreach ($jadwal as $baris) {
            if ($baris['periode'] > $periode) {
                break;
            }

            $akumulasi = $baris['akumulasi'];
        }

        return $akumulasi;
    }
}
