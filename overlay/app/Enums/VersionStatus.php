<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

enum VersionStatus: string
{
    use PunyaLabel;

    case Draf = 'draft';
    case Ditinjau = 'in_review';
    case Disahkan = 'approved';
    case Ditolak = 'rejected';
    case Ditarik = 'superseded';

    public function label(): string
    {
        return match ($this) {
            self::Draf => 'Draf',
            self::Ditinjau => 'Menunggu pengesahan',
            self::Disahkan => 'Disahkan',
            self::Ditolak => 'Ditolak',
            self::Ditarik => 'Ditarik tanpa pengganti',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Draf => 'gray',
            self::Ditinjau => 'warning',
            self::Disahkan => 'success',
            self::Ditolak => 'danger',
            self::Ditarik => 'gray',
        };
    }

    /**
     * Hanya status ini yang terkena constraint satu_versi_berlaku, sehingga draf
     * bebas disimpan tanpa tanggal berlaku.
     */
    public function berlaku(): bool
    {
        return $this === self::Disahkan;
    }
}
