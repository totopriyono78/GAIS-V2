<?php

namespace App\Support\Concerns;

/**
 * Dipasang di enum yang punya method label().
 *
 * GAIS sebelumnya menyimpan daftar pilihan sebagai konstanta larik di model,
 * misalnya AssetDocument::TYPES, lalu memberikannya langsung ke ->options().
 * Enum modul dokumen memakai enum PHP sungguhan karena nilainya membawa
 * perilaku, bukan sekadar label: Lifecycle tahu jenisnya perlu nomor dokumen
 * atau tidak, Confidentiality tahu tingkat sensitifnya. Trait ini menjaga cara
 * pemakaiannya di layar tetap sama seperti modul lain.
 */
trait PunyaLabel
{
    /**
     * Daftar pilihan siap pakai untuk ->options() di Filament.
     *
     * @return array<string, string>
     */
    public static function opsi(): array
    {
        $hasil = [];

        foreach (self::cases() as $case) {
            $hasil[$case->value] = $case->label();
        }

        return $hasil;
    }

    public static function label_dari(?string $nilai): ?string
    {
        return $nilai === null ? null : (self::tryFrom($nilai)?->label() ?? $nilai);
    }
}
