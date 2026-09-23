<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

/**
 * Tipe field yang boleh muncul di skema metadata sebuah jenis dokumen.
 *
 * Daftarnya sengaja pendek. Satu definisi skema dipakai untuk tiga hal
 * sekaligus: membangun form, memvalidasi isian, dan merender filter. Format
 * yang lebih ekspresif, misalnya JSON Schema penuh, tidak bisa dipetakan
 * otomatis ke ketiganya, dan kelebihannya justru jadi beban.
 */
enum MetadataFieldType: string
{
    use PunyaLabel;

    case Teks = 'string';
    case TeksPanjang = 'text';
    case Angka = 'number';
    case Tanggal = 'date';
    case YaTidak = 'boolean';
    case Pilihan = 'select';
    case Relasi = 'reference';

    public function label(): string
    {
        return match ($this) {
            self::Teks => 'Teks singkat',
            self::TeksPanjang => 'Teks panjang',
            self::Angka => 'Angka',
            self::Tanggal => 'Tanggal',
            self::YaTidak => 'Ya atau tidak',
            self::Pilihan => 'Pilihan',
            self::Relasi => 'Relasi ke tabel lain',
        };
    }

    /** Hanya tipe ini yang perlu daftar pilihan. */
    public function perluOpsi(): bool
    {
        return $this === self::Pilihan;
    }

    /** Hanya tipe ini yang perlu menyebut tabel tujuannya. */
    public function perluRelasi(): bool
    {
        return $this === self::Relasi;
    }
}
