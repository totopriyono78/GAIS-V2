<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\AksesDokumen;
use App\Support\Berkas;
use App\Support\Concerns\MemeriksaAksesDokumen;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Satu satunya pintu untuk mengambil berkas dokumen.
 *
 * Berkasnya dialirkan lewat controller ini, bukan lewat alamat bertanda tangan
 * seperti lampiran modul lain. Bedanya disengaja, dan ada dua alasannya.
 *
 * Pertama, izinnya diperiksa pada saat berkasnya diambil, bukan pada saat
 * tautannya dibuat. Alamat bertanda tangan tetap bisa dipakai selama masa
 * berlakunya meski izin orangnya sudah dicabut semenit setelah tautan terbit,
 * dan untuk dokumen berklasifikasi rahasia jeda itu terlalu longgar.
 *
 * Kedua, di sinilah pengunduhannya tercatat. Untuk keperluan audit, yang
 * ditanyakan bukan siapa yang mengubah dokumen, melainkan siapa saja yang
 * pernah membawa salinannya keluar.
 *
 * Tiga lapis pemeriksaan di bawah berdiri sendiri dan urutannya penting:
 * izin modul, lalu klasifikasi kerahasiaan, lalu cakupan data.
 */
class UnduhDokumenController extends Controller
{
    use MemeriksaAksesDokumen;

    public function __invoke(
        Request $request,
        Document $document,
        DocumentVersion $version,
        AksesDokumen $akses,
    ): StreamedResponse {
        $user = $request->user();

        $this->pastikanBoleh($user, $document, $version, 'documents.download');

        $akses->catat($document, $user, 'download', $version);

        // Nama asli dipakai di sini, dan hanya di sini. Di penyimpanan, berkas
        // bernama acak supaya judulnya tidak membocorkan isi dokumennya lewat
        // daftar berkas.
        return Berkas::disk()->download($version->storage_path, $version->original_name);
    }
}
