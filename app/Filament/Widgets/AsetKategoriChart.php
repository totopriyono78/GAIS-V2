<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\AssetCategory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Sebaran aset per kategori. Kategori dengan jumlah nol tidak ditampilkan, supaya
 * diagramnya tidak penuh potongan kosong yang tidak membawa informasi.
 */
class AsetKategoriChart extends ChartWidget
{
    protected ?string $heading = 'Assets by Category';

    protected ?string $description = 'Kategori yang belum punya aset tidak ditampilkan.';

    protected static ?int $sort = 3;

    /*
     * Widget dasbor dimuat bersama halamannya, bukan lewat permintaan susulan.
     * Isinya hanya beberapa kueri agregat, jadi satu permintaan lebih cepat
     * daripada enam permintaan sekaligus, dan tulisan "Loading..." tidak sempat
     * berkedip. Ini juga yang membuat dasbor tetap terbuka penuh di server
     * bawaan PHP, yang hanya melayani satu permintaan pada satu waktu.
     */
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('assets.read') ?? false;
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $hitung = Asset::query()
            ->where('status', '!=', 'dilepas')
            ->selectRaw('asset_category_id, count(*) as jumlah')
            ->groupBy('asset_category_id')
            ->orderByDesc('jumlah')
            ->pluck('jumlah', 'asset_category_id');

        $kategori = AssetCategory::query()->whereIn('id', $hitung->keys())->get()->keyBy('id');

        // Satu keluarga ungu Vuexy yang memudar lalu abu keunguannya, bukan warna
        // pelangi, supaya tetap satu bahasa dengan palet aplikasi dan tidak ada kategori
        // yang tampak seperti status (hijau, merah, jingga).
        $tangga = ['#4E41CC', '#5D4FE6', '#7367F0', '#8F85F3', '#ADA5F6', '#CCC7FA', '#5E5873', '#6E6B7B', '#B9B9C3', '#D8D6DE'];

        $label = [];
        $nilai = [];
        $warna = [];
        $i = 0;

        foreach ($hitung as $id => $jumlah) {
            $label[] = $kategori[$id]?->pickerLabel() ?? 'Tanpa kategori';
            $nilai[] = (int) $jumlah;
            $warna[] = $tangga[$i % count($tangga)];
            $i++;
        }

        return [
            'datasets' => [[
                'label' => 'Jumlah aset',
                'data' => $nilai,
                'backgroundColor' => $warna,
                // Tanpa garis tepi, potongan dipisah jarak. Garis tepi berwarna tetap
                // akan tampak sebagai cincin putih di tema gelap.
                'borderWidth' => 0,
                'spacing' => 2,
            ]],
            'labels' => $label,
        ];
    }
}
