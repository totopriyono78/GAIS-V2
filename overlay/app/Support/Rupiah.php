<?php

namespace App\Support;

/**
 * Penulisan rupiah untuk layar.
 *
 * Angka rupiah perusahaan cepat menjadi panjang. Nilai perolehan seluruh aset sudah
 * masuk puluhan miliar, dan angka penuhnya lebih lebar daripada kartu ringkasan di
 * layar 1366 piksel sehingga ujungnya terpotong. Kelas ini menyediakan dua bentuk:
 * bentuk pendek untuk angka besar di kartu, dan bentuk penuh untuk baris keterangan
 * di bawahnya. Yang dipendekkan hanya tampilannya, bukan datanya, dan angka penuhnya
 * selalu tetap ada di layar yang sama supaya tidak ada ketelitian yang hilang.
 */
class Rupiah
{
    /** Batas satuan, dari yang terbesar. */
    private const SATUAN = [
        1_000_000_000_000 => 'triliun',
        1_000_000_000 => 'miliar',
        1_000_000 => 'juta',
    ];

    /**
     * Bentuk penuh: Rp 46.929.166.000
     */
    public static function penuh(float|int $nilai): string
    {
        return 'Rp '.number_format((float) $nilai, 0, ',', '.');
    }

    /**
     * Bentuk pendek: Rp 46,93 miliar. Angka di bawah satu juta ditulis penuh, karena
     * "Rp 0,85 juta" lebih sulit dibaca daripada "Rp 850.000".
     */
    public static function ringkas(float|int $nilai): string
    {
        $nilai = (float) $nilai;

        foreach (self::SATUAN as $batas => $nama) {
            if (abs($nilai) >= $batas) {
                return 'Rp '.number_format($nilai / $batas, 2, ',', '.').' '.$nama;
            }
        }

        return self::penuh($nilai);
    }
}
