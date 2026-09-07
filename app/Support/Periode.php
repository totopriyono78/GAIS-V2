<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Periode bulanan dalam bentuk teks YYYY-MM.
 *
 * Penyusutan dihitung per bulan, dan bulan bukan tanggal. Memakai tanggal untuk
 * mewakili bulan selalu berakhir dengan pertanyaan tanggal berapa yang dipakai,
 * dan jawaban yang berbeda beda di tiap tempat. Teks YYYY-MM tidak punya pertanyaan
 * itu, urutannya benar kalau diurutkan sebagai teks, dan terbaca apa adanya saat
 * seseorang membuka basis data.
 */
class Periode
{
    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public static function sah(?string $periode): bool
    {
        if (! is_string($periode) || ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $periode)) {
            return false;
        }

        return true;
    }

    /** Periode dari sebuah tanggal. */
    public static function dariTanggal(Carbon|string|null $tanggal): ?string
    {
        if (blank($tanggal)) {
            return null;
        }

        $carbon = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);

        return $carbon->format('Y-m');
    }

    public static function sekarang(): string
    {
        return Carbon::now()->format('Y-m');
    }

    /** Berapa bulan dari $dari sampai $ke. Negatif kalau $ke lebih awal. */
    public static function selisih(string $dari, string $ke): int
    {
        [$t1, $b1] = array_map('intval', explode('-', $dari));
        [$t2, $b2] = array_map('intval', explode('-', $ke));

        return ($t2 - $t1) * 12 + ($b2 - $b1);
    }

    public static function tambah(string $periode, int $bulan): string
    {
        [$t, $b] = array_map('intval', explode('-', $periode));
        $total = ($t * 12 + ($b - 1)) + $bulan;

        return sprintf('%04d-%02d', intdiv($total, 12), $total % 12 + 1);
    }

    public static function berikutnya(string $periode): string
    {
        return self::tambah($periode, 1);
    }

    public static function sebelumnya(string $periode): string
    {
        return self::tambah($periode, -1);
    }

    /** 2026-09 menjadi September 2026. */
    public static function label(?string $periode): string
    {
        if (! self::sah($periode)) {
            return 'Belum ditentukan';
        }

        [$tahun, $bulan] = array_map('intval', explode('-', $periode));

        return self::BULAN[$bulan].' '.$tahun;
    }

    /** Hari terakhir bulan itu, dipakai untuk tanggal dokumen dan jurnal. */
    public static function akhirBulan(string $periode): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', $periode.'-01')->endOfMonth();
    }
}
