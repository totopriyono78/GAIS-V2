<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

/**
 * Hasil pemindaian virus satu berkas.
 *
 * Dilewati berarti pemindaian memang dimatikan di pemasangan ini, bukan berarti
 * berkasnya bersih. Dua hal itu sengaja dibedakan supaya layar tidak menyatakan
 * sesuatu yang tidak pernah diperiksa.
 */
enum ScanStatus: string
{
    use PunyaLabel;

    case Menunggu = 'pending';
    case Bersih = 'clean';
    case Terinfeksi = 'infected';
    case Dilewati = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu pemeriksaan',
            self::Bersih => 'Bersih',
            self::Terinfeksi => 'Terdeteksi virus',
            self::Dilewati => 'Tidak diperiksa',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Bersih => 'success',
            self::Terinfeksi => 'danger',
            self::Dilewati => 'gray',
        };
    }

    /**
     * Berkas yang belum diperiksa tidak boleh diunduh, berkas yang tidak
     * diperiksa boleh.
     *
     * Bedanya penting. Menunggu berarti pemeriksaan memang dijanjikan dan belum
     * selesai, jadi menahannya masuk akal. Dilewati berarti pemeriksaan tidak
     * dipasang di lingkungan ini, dan menahan berkas selamanya karena janji yang
     * tidak pernah dibuat akan membuat modulnya tidak terpakai.
     */
    public function bolehDiunduh(): bool
    {
        return $this === self::Bersih || $this === self::Dilewati;
    }
}
