<?php

namespace App\Support\Concerns;

/**
 * Membedakan tabel yang memang kosong dari tabel yang sedang dipersempit.
 *
 * Keduanya sama sama menampilkan kotak kosong, tetapi kalimat yang benar untuk keduanya
 * berbeda jauh. "Belum ada apa apa, mulai tambahkan" pada tabel berisi seratus baris yang
 * sedang tersaring adalah kalimat yang membingungkan, dan orang yang membacanya akan
 * menambah baris kedua ratus satu yang juga tidak akan terlihat.
 *
 * Asalnya dari AssetResource pada kiriman B, dipindahkan ke sini pada kiriman P supaya
 * tabel lain tidak menyalinnya lagi satu per satu.
 */
trait DetectsTableFilters
{
    /**
     * Apakah tabel sedang dipersempit oleh pencarian atau penyaring.
     *
     * Nilai penyaring yang tidak aktif bisa berupa null, teks kosong, larik kosong, atau
     * false untuk penyaring bertanda centang. Keempatnya diperlakukan sebagai tidak aktif.
     * Angka nol sengaja dianggap aktif, karena nol adalah pilihan yang sah.
     */
    protected static function adaPenyaringAktif(mixed $livewire): bool
    {
        if (filled($livewire->tableSearch ?? null)) {
            return true;
        }

        foreach ((array) ($livewire->tableFilters ?? []) as $penyaring) {
            foreach ((array) $penyaring as $nilai) {
                if ($nilai === null || $nilai === '' || $nilai === false || $nilai === []) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }
}
