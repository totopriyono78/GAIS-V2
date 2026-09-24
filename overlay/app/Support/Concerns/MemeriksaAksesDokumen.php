<?php

namespace App\Support\Concerns;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use App\Support\Berkas;

/**
 * Tiga lapis pemeriksaan sebelum berkas dokumen boleh dibaca.
 *
 * Dipakai bersama oleh pintu unduhan dan pintu pratinjau. Ditaruh di satu
 * tempat bukan demi menghemat baris, melainkan karena dua pintu yang memeriksa
 * hal yang sama dengan kode yang terpisah akan menyimpang: satu diperbaiki,
 * satunya terlupa, dan yang terlupa itu yang nanti jadi lubangnya.
 *
 * Urutannya sengaja begini:
 *
 * 1. Izin modul menjawab boleh melakukan apa.
 * 2. Klasifikasi menjawab boleh melihat yang mana, dengan membandingkan
 *    tingkat kewenangan orangnya terhadap klasifikasi dokumennya.
 * 3. Cakupan data menjawab baris mana saja, dan memakai scope yang sama persis
 *    dengan yang menyaring daftarnya, supaya layar dan pintu berkas tidak
 *    pernah berbeda pendapat.
 */
trait MemeriksaAksesDokumen
{
    protected function pastikanBoleh(
        ?User $user,
        Document $document,
        DocumentVersion $version,
        string $izin,
    ): void {
        abort_if($user === null, 403);

        abort_unless(
            $user->hasPermission($izin),
            403,
            'Anda tidak punya izin membuka berkas dokumen.',
        );

        /*
         * Versi harus benar benar milik dokumen yang disebut di alamatnya.
         *
         * Tanpa pemeriksaan ini, nomor versi milik dokumen lain bisa dipasang
         * pada dokumen yang boleh dibuka, dan berkas yang seharusnya tertutup
         * ikut terambil. Ini jenis lubang yang tidak pernah terlihat dari layar,
         * karena layar tidak pernah menghasilkan alamat semacam itu.
         */
        abort_unless($version->document_id === $document->id, 404);

        abort_unless(
            $document->confidentiality->tingkat() <= $user->clearanceLevel(),
            403,
            'Dokumen ini berklasifikasi '.$document->confidentiality->label()
            .', dan tingkat kewenangan Anda belum mencukupi untuk membukanya.',
        );

        abort_unless(
            Document::query()->whereKey($document->id)->terlihatOleh($user)->exists(),
            403,
            'Dokumen ini berada di luar cakupan data yang boleh Anda lihat.',
        );

        abort_unless(
            $version->bolehDiunduh(),
            403,
            'Berkas versi ini belum lolos pemeriksaan, jadi belum bisa dibuka.',
        );

        abort_unless(
            Berkas::ada($version->storage_path),
            404,
            'Berkasnya tidak ditemukan di penyimpanan.',
        );
    }
}
