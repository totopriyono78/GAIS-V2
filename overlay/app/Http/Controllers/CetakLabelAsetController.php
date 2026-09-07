<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman cetak label aset untuk lembar stiker A4.
 *
 * Ukuran label diambil dari pengaturan sistem, bukan ditulis di kode, karena
 * merek stiker yang beredar punya ukuran berbeda beda dan tim GA harus bisa
 * menyesuaikannya sendiri tanpa menunggu pengembang.
 */
class CetakLabelAsetController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user !== null && $user->hasPermission('assets.print'),
            403,
            'Anda tidak punya izin mencetak label aset.',
        );

        $ids = collect(explode(',', (string) $request->query('ids', '')))
            ->map(fn ($value) => (int) trim((string) $value))
            ->filter()
            ->unique()
            ->take(500)
            ->values();

        abort_if($ids->isEmpty(), 404, 'Tidak ada aset yang dipilih untuk dicetak.');

        $assets = Asset::query()
            ->with(['location', 'category'])
            ->whereIn('id', $ids)
            ->orderBy('code')
            ->get();

        abort_if($assets->isEmpty(), 404, 'Aset yang dipilih tidak ditemukan.');

        return view('cetak.label-aset', [
            'assets' => $assets,
            'lebar' => $this->setting('label.lebar_mm', 70.0),
            'tinggi' => $this->setting('label.tinggi_mm', 37.0),
            'kolom' => (int) $this->setting('label.kolom', 3),
            'marginAtas' => $this->setting('label.margin_atas_mm', 8.0),
            'marginKiri' => $this->setting('label.margin_kiri_mm', 5.0),
            'merek' => Setting::get('perusahaan.nama_singkat', 'GAIS'),
        ]);
    }

    private function setting(string $key, float $default): float
    {
        $value = Setting::get($key);

        return is_numeric($value) ? (float) $value : $default;
    }
}
