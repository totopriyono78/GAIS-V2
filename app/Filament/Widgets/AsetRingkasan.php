<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Support\Rupiah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Angka pembuka tab Aset. Semuanya dihitung dari basis data saat halaman dibuka,
 * tidak ada satu pun angka yang ditulis di kode.
 */
class AsetRingkasan extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    /*
     * Widget dasbor dimuat bersama halamannya, bukan lewat permintaan susulan.
     * Isinya hanya beberapa kueri agregat, jadi satu permintaan lebih cepat
     * daripada enam permintaan sekaligus, dan tulisan "Loading..." tidak sempat
     * berkedip. Ini juga yang membuat dasbor tetap terbuka penuh di server
     * bawaan PHP, yang hanya melayani satu permintaan pada satu waktu.
     */
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('assets.read') ?? false;
    }

    protected function getStats(): array
    {
        $aktif = Asset::query()->where('status', '!=', 'dilepas');

        $jumlah = (clone $aktif)->count();
        $nilai = (float) (clone $aktif)->sum('acquisition_cost');
        $sewaan = (clone $aktif)->where('ownership_type', 'sewa')->count();
        $tanpaPenanggungJawab = (clone $aktif)->whereNull('custodian_employee_id')->count();

        return [
            Stat::make('Aset aktif', number_format($jumlah, 0, ',', '.'))
                ->description('Belum dilepas')
                ->color('primary'),

            Stat::make('Nilai perolehan', Rupiah::ringkas($nilai))
                ->description(Rupiah::penuh($nilai).' sebelum penyusutan')
                ->color('gray'),

            Stat::make('Aset sewaan', number_format($sewaan, 0, ',', '.'))
                ->description($sewaan > 0 ? 'Bukan milik perusahaan, tidak disusutkan' : 'Belum ada yang ditandai sewa')
                ->color($sewaan > 0 ? 'warning' : 'gray'),

            Stat::make('Tanpa penanggung jawab', number_format($tanpaPenanggungJawab, 0, ',', '.'))
                ->description($tanpaPenanggungJawab > 0 ? 'Perlu ditunjuk pemegangnya' : 'Semua sudah ada pemegangnya')
                ->color($tanpaPenanggungJawab > 0 ? 'danger' : 'success'),
        ];
    }
}
