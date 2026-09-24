<?php

namespace App\Exceptions;

use Illuminate\Support\Carbon;

/**
 * Tanggal berlaku yang dipilih lebih awal daripada tanggal berlaku versi yang
 * sekarang masih berjalan.
 *
 * Kalau diteruskan, versi lama akan ditutup pada tanggal sebelum ia sendiri
 * mulai berlaku, dan riwayatnya jadi tidak masuk akal: ada versi yang berakhir
 * sebelum ia dimulai. Basis data memang menolaknya lewat
 * document_versions_valid_period, tetapi penolakan di sana keluar sebagai
 * SQLSTATE 23514 yang tidak bisa dibaca siapa pun. Pemeriksaan ini menolak
 * lebih dulu, dengan menyebutkan tanggal yang menjadi batasnya.
 *
 * Catatan: menyahkan pada tanggal yang SAMA dengan versi lama diperbolehkan.
 * Itu penggantian di hari yang sama, dan rentang tanggalnya menjadi kosong,
 * bukan terbalik.
 */
class TanggalBerlakuMundur extends MasalahVersiDokumen
{
    /** check_violation pada PostgreSQL. */
    public const SQLSTATE = '23514';

    public static function untukPengesahan(int $nomorVersiLama, Carbon|string $mulaiVersiLama, Carbon|string $tanggalBaru): self
    {
        return new self(
            'Versi '.$nomorVersiLama.' yang sekarang berlaku mulai '.self::terbaca($mulaiVersiLama).', '
            .'jadi penggantinya tidak bisa disahkan mulai '.self::terbaca($tanggalBaru).'. '
            .'Pilih tanggal yang tidak lebih awal dari '.self::terbaca($mulaiVersiLama).', '
            .'atau perbaiki dulu tanggal berlaku versi '.$nomorVersiLama.'.',
        );
    }

    public static function untukPenarikan(int $nomorVersi, Carbon|string $mulai, Carbon|string $sampai): self
    {
        return new self(
            'Versi '.$nomorVersi.' baru berlaku mulai '.self::terbaca($mulai).', '
            .'jadi tidak bisa ditutup pada '.self::terbaca($sampai).'. '
            .'Pilih tanggal yang tidak lebih awal dari '.self::terbaca($mulai).'.',
        );
    }

    /**
     * Basis data menolak dengan 23514 tanpa memberi tahu baris mana yang
     * bermasalah. Daripada memunculkan layar 500, keadaan itu diterjemahkan
     * menjadi pesan yang menyebutkan apa yang perlu diperiksa pemakainya.
     */
    public static function tidakTerduga(int $dokumenId): self
    {
        return new self(
            'Pengesahan tidak jadi disimpan karena periode berlakunya bertabrakan dengan riwayat versi dokumen ini. '
            .'Periksa kolom Masa berlaku pada setiap versi di daftar ini, lalu coba lagi dengan tanggal yang sesuai. '
            .'(Dokumen #'.$dokumenId.')',
        );
    }

    private static function terbaca(Carbon|string $tanggal): string
    {
        return ($tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal))->translatedFormat('d F Y');
    }
}
