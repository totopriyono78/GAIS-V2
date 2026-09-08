<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Sebaran status aset. Diagram lingkaran dipilih karena pertanyaannya memang
 * tentang proporsi, yaitu berapa bagian dari seluruh aset yang sedang dipinjam
 * atau sedang diperbaiki, bukan tentang perubahan dari waktu ke waktu.
 */
class AsetStatusChart extends ChartWidget
{
    protected ?string $heading = 'Assets by Status';

    protected ?string $description = 'Aset yang sudah dilepas tidak ikut dihitung.';

    protected static ?int $sort = 2;

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
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $label = [];
        $nilai = [];
        $warna = [];

        // Warna mengikuti arti statusnya, bukan urutan acak, supaya sama dengan
        // warna badge status di daftar aset.
        $petaWarna = [
            'aktif' => '#17505E',
            'dipinjam' => '#588D9C',
            'perbaikan' => '#A2542F',
            'tidak_dipakai' => '#76878E',
        ];

        foreach (Asset::STATUSES as $kunci => $teks) {
            if ($kunci === 'dilepas' || ! $hitung->has($kunci)) {
                continue;
            }

            $label[] = $teks;
            $nilai[] = (int) $hitung[$kunci];
            $warna[] = $petaWarna[$kunci] ?? '#B6C2C7';
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
