<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

enum LinkRelation: string
{
    use PunyaLabel;

    case Lampiran = 'attachment';
    case Rujukan = 'reference';
    case Pengganti = 'supersedes';
    case Adendum = 'amendment';

    public function label(): string
    {
        return match ($this) {
            self::Lampiran => 'Lampiran',
            self::Rujukan => 'Dirujuk',
            self::Pengganti => 'Menggantikan',
            self::Adendum => 'Adendum',
        };
    }
}
