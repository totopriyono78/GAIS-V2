<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

/**
 * Arsip bukan hapus, dan musnah bukan hilang.
 *
 * Dokumen yang dimusnahkan berkasnya memang dihapus, tetapi barisnya tetap ada
 * beserta berita acara pemusnahannya. Jejak bahwa dokumen itu pernah ada justru
 * yang dicari saat audit.
 */
enum DocumentStatus: string
{
    use PunyaLabel;

    case Aktif = 'active';
    case Diarsipkan = 'archived';
    case Dimusnahkan = 'destroyed';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Diarsipkan => 'Diarsipkan',
            self::Dimusnahkan => 'Dimusnahkan',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Aktif => 'success',
            self::Diarsipkan => 'gray',
            self::Dimusnahkan => 'danger',
        };
    }
}
