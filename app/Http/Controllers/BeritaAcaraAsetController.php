<?php

namespace App\Http\Controllers;

use App\Models\AssetTransfer;
use App\Models\Setting;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Berita acara mutasi aset (BAM) dan berita acara serah terima (BAST).
 *
 * Keduanya lahir dari satu dokumen serah terima yang sama, tetapi menjawab hal
 * berbeda. BAM menjelaskan perpindahan barangnya, dan yang menandatangani adalah
 * pihak yang bertanggung jawab atas pencatatan. BAST menjelaskan penyerahan dari
 * orang ke orang, dan yang menandatangani adalah kedua orang itu. Karena itu
 * keduanya dibuat sebagai dua berkas, bukan satu berkas dengan dua judul.
 *
 * Berkasnya dibuat dengan Dompdf, bukan dengan dialog cetak peramban, supaya yang
 * turun benar benar berkas PDF yang bisa langsung dilampirkan ke surat.
 */
class BeritaAcaraAsetController extends Controller
{
    public const JENIS = [
        'bam' => 'Berita Acara Mutasi Aset',
        'bast' => 'Berita Acara Serah Terima Aset',
    ];

    public function __invoke(Request $request, AssetTransfer $transfer, string $jenis): Response
    {
        $user = $request->user();

        abort_unless(
            $user !== null && $user->hasPermission('asset_transfers.read'),
            403,
            'Anda tidak punya izin melihat dokumen serah terima aset.',
        );

        abort_unless(array_key_exists($jenis, self::JENIS), 404, 'Jenis berita acara tidak dikenal.');

        /*
         * Kalau pustaka PDF belum dipasang, yang keluar penjelasan singkat beserta
         * perintahnya, bukan halaman galat. Tanpa ini, tombol unduh akan terasa rusak
         * padahal yang kurang cuma satu perintah composer.
         */
        if (! class_exists(Dompdf::class)) {
            return response(
                "Pustaka pembuat PDF belum terpasang.\n\n"
                ."Jalankan perintah ini sekali di folder proyek, lalu coba lagi:\n"
                ."  composer require dompdf/dompdf\n",
                503,
                ['Content-Type' => 'text/plain; charset=UTF-8'],
            );
        }

        $transfer->load([
            'asset.category', 'asset.location',
            'fromLocation', 'toLocation',
            'fromCustodian', 'toCustodian',
            'fromDepartment', 'toDepartment',
            'handedOverBy.department', 'receivedBy.department',
        ]);

        $html = view('cetak.berita-acara-aset', [
            'transfer' => $transfer,
            'jenis' => $jenis,
            'judul' => self::JENIS[$jenis],
            'perusahaan' => Setting::get('perusahaan.nama', ''),
            'merek' => Setting::get('perusahaan.nama_singkat', 'GAIS'),
            'alamat' => Setting::get('perusahaan.alamat', ''),
            'telepon' => Setting::get('perusahaan.telepon', ''),
        ])->render();

        $options = new Options;
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $namaBerkas = strtoupper($jenis).'-'
            .str_replace(['/', '\\'], '-', $transfer->code).'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$namaBerkas.'"',
        ]);
    }
}
