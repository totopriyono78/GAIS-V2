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

        // Satu keluarga warna petrol yang menua, bukan warna pelangi, supaya tetap
        // satu bahasa dengan palet aplikasi.
        $tangga = ['#17505E', '#38707F', '#588D9C', '#7DABB8', '#A2C7D1', '#C5DEE5', '#3A4A50', '#596A70', '#76878E', '#96A5AB'];

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
                'borderColor' => '#FAF7F2',
                'borderWidth' => 2,
            ]],
            'labels' => $label,
        ];
    }
}
