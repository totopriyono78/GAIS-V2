<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

/**
 * Klasifikasi kerahasiaan dokumen.
 *
 * Ini dimensi yang berdiri sendiri dari izin. Seseorang bisa punya izin
 * documents.read dan tetap tidak boleh membuka dokumen sangat rahasia, karena
 * yang menghalanginya adalah tingkat kewenangan miliknya, bukan haknya atas
 * modul. Angkanya sejajar dengan clearance_level di tabel users.
 */
enum Confidentiality: string
{
    use PunyaLabel;

    case Publik = 'public';
    case Internal = 'internal';
    case Rahasia = 'confidential';
    case SangatRahasia = 'restricted';

    public function label(): string
    {
        return match ($this) {
            self::Publik => 'Publik',
            self::Internal => 'Internal',
            self::Rahasia => 'Rahasia',
            self::SangatRahasia => 'Sangat rahasia',
        };
    }

    public function keterangan(): string
    {
        return match ($this) {
            self::Publik => 'Boleh dibaca siapa pun yang bisa masuk sistem.',
            self::Internal => 'Untuk karyawan. Ini bawaan untuk dokumen baru.',
            self::Rahasia => 'Hanya untuk yang tingkat kewenangannya 2 ke atas.',
            self::SangatRahasia => 'Hanya untuk yang tingkat kewenangannya 3.',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Publik => 'gray',
            self::Internal => 'info',
            self::Rahasia => 'warning',
            self::SangatRahasia => 'danger',
        };
    }

    /** Makin tinggi makin sensitif. Dibandingkan dengan clearance_level pengguna. */
    public function tingkat(): int
    {
        return match ($this) {
            self::Publik => 0,
            self::Internal => 1,
            self::Rahasia => 2,
            self::SangatRahasia => 3,
        };
    }
}
