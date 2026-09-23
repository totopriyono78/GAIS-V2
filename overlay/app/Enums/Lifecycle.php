<?php

namespace App\Enums;

use App\Support\Concerns\PunyaLabel;

/**
 * Tiga pola siklus hidup dokumen.
 *
 * Pembedaan ini yang mencegah satu tabel berisi puluhan kolom kosong dan form
 * unggah berita acara yang menanyakan tanggal mulai berlaku.
 */
enum Lifecycle: string
{
    use PunyaLabel;

    /** SOP, kebijakan, sertifikat. Punya versi berlaku, masa berlaku, pengesahan. */
    case Controlled = 'controlled';

    /** Kontrak, sewa, polis. Yang menonjol para pihak dan tanggal berakhir. */
    case TermBased = 'term_based';

    /** Berita acara, faktur, foto aset. Hidupnya mengikuti catatan induk. */
    case Attachment = 'attachment';

    public function label(): string
    {
        return match ($this) {
            self::Controlled => 'Dokumen terkendali',
            self::TermBased => 'Dokumen berjangka waktu',
            self::Attachment => 'Lampiran transaksi',
        };
    }

    public function keterangan(): string
    {
        return match ($this) {
            self::Controlled => 'Punya versi yang berlaku, masa berlaku, dan pengesahan. Contohnya SOP dan kebijakan.',
            self::TermBased => 'Yang penting para pihak dan tanggal berakhirnya. Revisinya berupa adendum. Contohnya kontrak dan polis.',
            self::Attachment => 'Tidak punya siklus hidup sendiri, ikut catatan induknya, dan tidak diberi nomor. Contohnya berita acara dan faktur.',
        };
    }

    /** Lampiran tidak diberi nomor sendiri karena identitasnya ikut induk. */
    public function perluNomor(): bool
    {
        return $this !== self::Attachment;
    }
}
