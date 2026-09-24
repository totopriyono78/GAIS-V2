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
 * Menampilkan berkas di dalam halaman, bukan mengunduhnya.
 *
 * Bedanya dari pintu unduhan hanya dua, dan keduanya disengaja.
 *
 * Pertama, izinnya documents.read, bukan documents.download. Melihat dokumen di
 * layar dan membawa salinannya keluar adalah dua hal yang berbeda beratnya, dan
 * itulah alasan kedua izin itu dipisah sejak awal.
 *
 * Kedua, tercatat sebagai pratinjau, bukan unduhan. Saat audit menanyakan siapa
 * yang pernah membawa salinan dokumen ini keluar, membuka pratinjau tidak
 * pantas ikut terhitung.
 *
 * Yang tidak berbeda: berkasnya tetap dialirkan lewat controller, tetap
 * diperiksa tiga lapis, dan tetap tidak pernah punya alamat yang bisa dibuka
 * tanpa masuk sistem.
 */
class PratinjauDokumenController extends Controller
{
    use MemeriksaAksesDokumen;

    public function __invoke(
        Request $request,
        Document $document,
        DocumentVersion $version,
        AksesDokumen $akses,
    ): StreamedResponse {
        $user = $request->user();

        $this->pastikanBoleh($user, $document, $version, 'documents.read');

        $akses->catat($document, $user, 'preview', $version);

        return Berkas::disk()->response(
            $version->storage_path,
            $version->original_name,
            [
                // Ditampilkan di tempat, tidak diunduh.
                'Content-Disposition' => 'inline; filename="'.addslashes($version->original_name).'"',
                // Berkas dari pemakai tidak boleh menjalankan apa pun terhadap
                // sesi orang yang membukanya. PDF bisa memuat JavaScript, dan
                // gambar SVG lebih parah lagi.
                'Content-Security-Policy' => "default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; object-src 'self'; sandbox",
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ],
        );
    }
}
