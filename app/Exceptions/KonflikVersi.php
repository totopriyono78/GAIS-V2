<?php

namespace App\Exceptions;

/**
 * Dua pengesahan versi diproses bersamaan dan memperebutkan periode yang sama.
 *
 * Ini bukan kerusakan sistem. Constraint satu_versi_berlaku memang bertugas
 * menolak yang kedua, dan penolakan itu tanda pengamanannya bekerja. Yang
 * perlu dilakukan pemakainya cuma memuat ulang halamannya dan mencoba lagi,
 * karena kemungkinan besar versi yang ia sahkan sudah disahkan orang lain.
 */
class KonflikVersi extends MasalahVersiDokumen
{
    /** exclusion_violation pada PostgreSQL. */
    public const SQLSTATE = '23P01';

    public static function untukDokumen(int $dokumenId): self
    {
        return new self(
            'Versi lain pada dokumen ini sedang disahkan bersamaan, jadi pengesahan Anda tidak jadi disimpan. '
            .'Muat ulang halamannya dan periksa versi mana yang sekarang berlaku sebelum mencoba lagi. '
            .'(Dokumen #'.$dokumenId.')',
        );
    }
}
