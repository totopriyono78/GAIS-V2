<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Support\Rupiah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Angka pembuka tab Kendaraan. Semuanya dihitung dari basis data saat halaman dibuka.
 */
class KendaraanRingkasan extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('vehicles.read') ?? false;
    }

    protected function getStats(): array
    {
        $aktif = Vehicle::query()->aktif()->count();

        /*
         * Keduanya dihitung dari dokumen yang berlaku, bukan dari seluruh baris.
         * Tanpa itu, pajak tahun lalu yang sudah diperpanjang akan selamanya terhitung
         * sebagai keterlambatan, dan angka di dasbor tidak pernah bisa kembali ke nol.
         */
        $terlambat = VehicleDocument::query()
            ->whereHas('vehicle', fn ($query) => $query->where('is_active', true))
            ->terlambat()
            ->count();

        $segera = VehicleDocument::query()
            ->whereHas('vehicle', fn ($query) => $query->where('is_active', true))
            ->jatuhTempo(30)
            ->count() - $terlambat;

        $biaya = (float) VehicleDocument::query()
            ->where('issued_date', '>=', now()->subYear())
            ->sum('cost');

        return [
            Stat::make('Kendaraan dipakai', number_format($aktif, 0, ',', '.'))
                ->description($aktif > 0 ? 'Terhubung ke aset masing masing' : 'Belum ada kendaraan yang didata')
                ->color($aktif > 0 ? 'primary' : 'gray'),

            Stat::make('Dokumen sudah lewat', number_format($terlambat, 0, ',', '.'))
                ->description($terlambat > 0 ? 'Perlu diurus sekarang' : 'Tidak ada yang terlambat')
                ->color($terlambat > 0 ? 'danger' : 'success'),

            Stat::make('Jatuh tempo 30 hari', number_format(max($segera, 0), 0, ',', '.'))
                ->description($segera > 0 ? 'Masih sempat diurus' : 'Tidak ada yang mendekati jatuh tempo')
                ->color($segera > 0 ? 'warning' : 'success'),

            Stat::make('Biaya dokumen setahun', Rupiah::ringkas($biaya))
                ->description(Rupiah::penuh($biaya).' dari dokumen yang terbit 12 bulan terakhir')
                ->color('gray'),
        ];
    }
}
