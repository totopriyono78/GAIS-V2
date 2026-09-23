<?php

namespace App\Http\Controllers;

use App\Enums\Confidentiality;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\AksesDokumen;
use App\Support\Berkas;
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
    public function __invoke(
        Request $request,
        Document $document,
        DocumentVersion $version,
        AksesDokumen $akses,
    ): StreamedResponse {
        $user = $request->user();

        abort_if($user === null, 403);

        // Lapis satu: izin modul.
        abort_unless(
            $user->hasPermission('documents.download'),
            403,
            'Anda tidak punya izin mengunduh berkas dokumen.',
        );

        // Versi harus benar benar milik dokumen yang disebut di alamatnya.
        // Tanpa pemeriksaan ini, nomor versi milik dokumen lain bisa dipasang
        // pada dokumen yang boleh dibuka, dan berkas yang seharusnya tertutup
        // ikut terambil.
        abort_unless($version->document_id === $document->id, 404);

        // Lapis dua: klasifikasi kerahasiaan dibanding tingkat kewenangan.
        abort_unless(
            $document->confidentiality->tingkat() <= $user->clearanceLevel(),
            403,
            'Dokumen ini berklasifikasi '.$document->confidentiality->label()
            .', dan tingkat kewenangan Anda belum mencukupi untuk membukanya.',
        );

        // Lapis tiga: cakupan data. Dipakai ulang dari scope yang sama dengan
        // yang menyaring daftarnya, supaya layar dan pintu unduhan tidak pernah
        // berbeda pendapat tentang dokumen mana yang boleh dilihat.
        abort_unless(
            Document::query()->whereKey($document->id)->terlihatOleh($user)->exists(),
            403,
            'Dokumen ini berada di luar cakupan data yang boleh Anda lihat.',
        );

        abort_unless(
            $version->bolehDiunduh(),
            403,
            'Berkas versi ini belum lolos pemeriksaan, jadi belum bisa diunduh.',
        );

        abort_unless(
            Berkas::ada($version->storage_path),
            404,
            'Berkasnya tidak ditemukan di penyimpanan. Kemungkinan besar ia hilang saat pemindahan penyimpanan.',
        );

        $akses->catat($document, $user, 'download', $version);

        // Nama asli dipakai di sini, dan hanya di sini. Di penyimpanan, berkas
        // bernama acak supaya judulnya tidak membocorkan isi dokumennya lewat
        // daftar berkas.
        return Berkas::disk()->download($version->storage_path, $version->original_name);
    }

    /**
     * Daftar klasifikasi yang boleh dibuka seorang pengguna, untuk ditampilkan
     * di layar saat ia bertanya kenapa sebuah dokumen tidak muncul.
     *
     * @return list<string>
     */
    public static function klasifikasiTerbuka(int $tingkat): array
    {
        return array_values(array_map(
            static fn (Confidentiality $k): string => $k->label(),
            array_filter(
                Confidentiality::cases(),
                static fn (Confidentiality $k): bool => $k->tingkat() <= $tingkat,
            ),
        ));
    }
}
