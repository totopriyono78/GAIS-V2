<?php

namespace App\Services\Barcode;

use InvalidArgumentException;

/**
 * Penghasil barcode Code 128 subset B dalam bentuk SVG.
 *
 * Ditulis sendiri, tanpa paket tambahan, karena yang dibutuhkan hanya satu
 * simbologi dan hasilnya harus berupa SVG yang bisa langsung ditempel di
 * halaman cetak label. Tabel polanya dicocokkan dengan implementasi acuan
 * sehingga hasilnya identik bit per bit.
 *
 * Subset B menampung huruf besar, huruf kecil, angka, dan tanda baca ASCII
 * 32 sampai 126. Kode aset GAIS selalu berada di rentang itu.
 */
class Code128
{
    /** Pola 11 modul untuk nilai 0 sampai 105. */
    private const PATTERNS = [
        '11011001100',
        '11001101100',
        '11001100110',
        '10010011000',
        '10010001100',
        '10001001100',
        '10011001000',
        '10011000100',
        '10001100100',
        '11001001000',
        '11001000100',
        '11000100100',
        '10110011100',
        '10011011100',
        '10011001110',
        '10111001100',
        '10011101100',
        '10011100110',
        '11001110010',
        '11001011100',
        '11001001110',
        '11011100100',
        '11001110100',
        '11101101110',
        '11101001100',
        '11100101100',
        '11100100110',
        '11101100100',
        '11100110100',
        '11100110010',
        '11011011000',
        '11011000110',
        '11000110110',
        '10100011000',
        '10001011000',
        '10001000110',
        '10110001000',
        '10001101000',
        '10001100010',
        '11010001000',
        '11000101000',
        '11000100010',
        '10110111000',
        '10110001110',
        '10001101110',
        '10111011000',
        '10111000110',
        '10001110110',
        '11101110110',
        '11010001110',
        '11000101110',
        '11011101000',
        '11011100010',
        '11011101110',
        '11101011000',
        '11101000110',
        '11100010110',
        '11101101000',
        '11101100010',
        '11100011010',
        '11101111010',
        '11001000010',
        '11110001010',
        '10100110000',
        '10100001100',
        '10010110000',
        '10010000110',
        '10000101100',
        '10000100110',
        '10110010000',
        '10110000100',
        '10011010000',
        '10011000010',
        '10000110100',
        '10000110010',
        '11000010010',
        '11001010000',
        '11110111010',
        '11000010100',
        '10001111010',
        '10100111100',
        '10010111100',
        '10010011110',
        '10111100100',
        '10011110100',
        '10011110010',
        '11110100100',
        '11110010100',
        '11110010010',
        '11011011110',
        '11011110110',
        '11110110110',
        '10101111000',
        '10100011110',
        '10001011110',
        '10111101000',
        '10111100010',
        '11110101000',
        '11110100010',
        '10111011110',
        '10111101110',
        '11101011110',
        '11110101110',
        '11010000100',
        '11010010000',
        '11010011100',
    ];

    private const START_B = 104;

    private const STOP = '1100011101011';

    /**
     * Mengubah teks menjadi deretan modul, 1 berarti batang hitam.
     */
    public static function modules(string $value): string
    {
        if ($value === '') {
            throw new InvalidArgumentException('Nilai barcode tidak boleh kosong.');
        }

        $sum = self::START_B;
        $bits = self::PATTERNS[self::START_B];
        $position = 0;

        foreach (str_split($value) as $character) {
            $ascii = ord($character);

            if ($ascii < 32 || $ascii > 126) {
                throw new InvalidArgumentException(
                    "Karakter '{$character}' tidak bisa dijadikan barcode Code 128 subset B."
                );
            }

            $code = $ascii - 32;
            $position++;
            $sum += $code * $position;
            $bits .= self::PATTERNS[$code];
        }

        $bits .= self::PATTERNS[$sum % 103];
        $bits .= self::STOP;

        return $bits;
    }

    /**
     * Menghasilkan SVG yang lebarnya dipaskan ke ruang yang tersedia di label.
     * Lebar modul dibatasi supaya kode pendek tidak melar dan kode panjang
     * tidak menyusut sampai tidak terbaca pemindai.
     */
    public static function svgFitted(string $value, float $maxWidthMm, float $heightMm = 12.0): string
    {
        $moduleCount = max(strlen(self::modules($value)), 1);
        $moduleWidth = $maxWidthMm / $moduleCount;
        $moduleWidth = min($moduleWidth, 0.5);
        $moduleWidth = max($moduleWidth, 0.19);

        return self::svg($value, $moduleWidth, $heightMm);
    }

    /**
     * Menghasilkan SVG siap tempel. Lebar modul dalam milimeter supaya ukuran
     * cetaknya bisa dipastikan, bukan bergantung resolusi layar.
     */
    public static function svg(string $value, float $moduleWidthMm = 0.33, float $heightMm = 12.0): string
    {
        $bits = self::modules($value);
        $count = strlen($bits);
        $width = round($count * $moduleWidthMm, 3);

        $rects = '';
        $index = 0;

        while ($index < $count) {
            if ($bits[$index] === '0') {
                $index++;

                continue;
            }

            $run = 0;

            while ($index + $run < $count && $bits[$index + $run] === '1') {
                $run++;
            }

            $x = round($index * $moduleWidthMm, 3);
            $w = round($run * $moduleWidthMm, 3);
            $rects .= '<rect x="'.$x.'" y="0" width="'.$w.'" height="'.$heightMm.'" fill="#000000"/>';

            $index += $run;
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'mm" height="'.$heightMm.'mm" '
            .'viewBox="0 0 '.$width.' '.$heightMm.'" role="img" aria-label="Barcode '.htmlspecialchars($value, ENT_QUOTES).'">'
            .$rects
            .'</svg>';
    }
}
