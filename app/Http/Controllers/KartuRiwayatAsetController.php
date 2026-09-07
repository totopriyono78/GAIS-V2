<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Setting;
use App\Services\RiwayatAset;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Kartu riwayat satu aset.
 *
 * Halaman ini berada di luar panel, sama seperti halaman cetak label, karena
 * kartu riwayat sering harus ikut dilampirkan ke berita acara serah terima atau
 * berita acara pelepasan. Tampilan polos tanpa menu membuatnya langsung bisa
 * dicetak, dan aturan cetak di bawah membuang tombolnya dari kertas.
 */
class KartuRiwayatAsetController extends Controller
{
    public function __invoke(Request $request, Asset $asset): View
    {
        $user = $request->user();

        abort_unless(
            $user !== null && $user->hasPermission('assets.read'),
            403,
            'Anda tidak punya izin melihat data aset.',
        );

        $asset->load(['location', 'custodian', 'department', 'category']);

        return view('cetak.kartu-riwayat-aset', [
            'asset' => $asset,
            'peristiwa' => RiwayatAset::untuk($asset),
            'merek' => Setting::get('perusahaan.nama_singkat', 'GAIS'),
            'perusahaan' => Setting::get('perusahaan.nama', ''),
        ]);
    }
}
