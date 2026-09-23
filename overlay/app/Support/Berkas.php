<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Satu pintu untuk seluruh berkas unggahan: foto aset, dokumen kendaraan, lampiran
 * tiket perbaikan, bukti penggantian biaya, pindaian surat, dan tagihan rekanan.
 *
 * ## Kenapa berkas tidak lagi di disk public
 *
 * Sampai 23 September 2026 seluruh berkas itu disimpan di disk `public` dengan
 * visibility public. Nama berkasnya memang diacak Filament, jadi alamatnya tidak
 * bisa ditebak. Tetapi alamat itu berlaku selamanya dan tidak pernah diperiksa
 * izinnya sekali pun. Siapa saja yang memegang tautannya, entah dari riwayat
 * peramban, grup pesan, atau tangkapan layar yang diteruskan, bisa mengunduh
 * kontrak sewa, bukti pembayaran, dan pindaian surat tanpa pernah masuk sistem.
 *
 * Disk `dokumen` tidak punya symlink ke folder public, jadi tidak ada berkas di
 * dalamnya yang bisa diambil peladen web secara langsung. Satu satunya jalan
 * membacanya adalah rute bawaan Laravel yang menolak permintaan tanpa tanda
 * tangan sah, dan tanda tangan itu hanya diterbitkan ke layar yang pemakainya
 * sudah lolos pemeriksaan izin modul.
 *
 * Kelas ini dibuat supaya nama disk dan cara menerbitkan alamatnya ditulis di
 * satu tempat. Sebelumnya string 'public' tersebar di dua puluh berkas, dan itu
 * sebabnya satu keputusan lama ikut terbawa ke setiap modul baru.
 */
class Berkas
{
    public const DISK = 'dokumen';

    /**
     * Umur alamat unduhan, dalam menit.
     *
     * Sengaja pendek. Alamat bertanda tangan yang bocor lewat riwayat peramban
     * atau grup pesan tetap bisa dipakai sampai masa berlakunya habis, jadi masa
     * itu perlu lebih pendek daripada umur percakapan yang biasa meneruskannya.
     */
    public const MENIT = 5;

    public static function disk(): Filesystem
    {
        return Storage::disk(self::DISK);
    }

    /**
     * Alamat berumur pendek untuk satu berkas.
     *
     * Mengembalikan null kalau jalurnya kosong atau berkasnya sudah tidak ada di
     * penyimpanan, supaya pemanggilnya menyembunyikan tombol unduh alih alih
     * menyodorkan tautan yang berujung halaman kosong.
     */
    public static function url(?string $path, int $menit = self::MENIT): ?string
    {
        if (blank($path) || ! self::disk()->exists($path)) {
            return null;
        }

        try {
            return self::disk()->temporaryUrl($path, now()->addMinutes($menit));
        } catch (Throwable) {
            // Driver yang tidak mendukung alamat sementara tidak boleh membuat
            // halaman gagal dimuat. Tombol unduhnya saja yang hilang.
            return null;
        }
    }

    public static function ada(?string $path): bool
    {
        return filled($path) && self::disk()->exists($path);
    }

    public static function ukuran(?string $path): ?int
    {
        return self::ada($path) ? self::disk()->size($path) : null;
    }

    public static function hapus(?string $path): void
    {
        if (filled($path)) {
            self::disk()->delete($path);
        }
    }
}
