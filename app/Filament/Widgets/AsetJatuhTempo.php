<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Garansi dan masa sewa yang akan habis.
 *
 * Dibuat berjenjang satu, dua, dan tiga bulan karena tindakannya berbeda. Yang
 * habis bulan ini sudah harus diklaim atau diperpanjang sekarang, sedangkan yang
 * tiga bulan lagi baru perlu masuk rencana. Tiap angka menjadi tautan ke daftar
 * aset yang sudah tersaring, supaya orang tidak perlu menyaring ulang sendiri.
 */
class AsetJatuhTempo extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    /*
     * Widget dasbor dimuat bersama halamannya, bukan lewat permintaan susulan.
     * Isinya hanya beberapa kueri agregat, jadi satu permintaan lebih cepat
     * daripada enam permintaan sekaligus, dan tulisan "Loading..." tidak sempat
     * berkedip. Ini juga yang membuat dasbor tetap terbuka penuh di server
     * bawaan PHP, yang hanya melayani satu permintaan pada satu waktu.
     */
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Garansi dan sewa yang akan habis';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('assets.read') ?? false;
    }

    protected function getStats(): array
    {
        $stats = [];

        foreach ([1, 2, 3] as $bulan) {
            $jumlah = $this->hitungGaransi($bulan);

            $stats[] = Stat::make("Garansi habis dalam {$bulan} bulan", number_format($jumlah, 0, ',', '.'))
                ->description($jumlah > 0 ? 'Klaim atau perpanjang sebelum lewat' : 'Tidak ada yang jatuh tempo')
                ->color(match (true) {
                    $jumlah === 0 => 'gray',
                    $bulan === 1 => 'danger',
                    $bulan === 2 => 'warning',
                    default => 'primary',
                });
        }

        $sudahLewat = Asset::query()
            ->where('status', '!=', 'dilepas')
            ->where('has_warranty', true)
            ->whereNotNull('warranty_until')
            ->whereDate('warranty_until', '<', now()->toDateString())
            ->count();

        $stats[] = Stat::make('Garansi sudah lewat', number_format($sudahLewat, 0, ',', '.'))
            ->description('Perbaikan sudah menjadi biaya sendiri')
            ->color($sudahLewat > 0 ? 'gray' : 'success');

        $sewaHabis = Asset::query()
            ->where('status', '!=', 'dilepas')
            ->where('ownership_type', 'sewa')
            ->whereNotNull('lease_end_date')
            ->whereBetween('lease_end_date', [now()->toDateString(), now()->addMonths(3)->toDateString()])
            ->count();

        $stats[] = Stat::make('Sewa habis dalam 3 bulan', number_format($sewaHabis, 0, ',', '.'))
            ->description($sewaHabis > 0 ? 'Perlu diperpanjang atau dikembalikan' : 'Tidak ada sewa yang jatuh tempo')
            ->color($sewaHabis > 0 ? 'warning' : 'gray');

        return $stats;
    }

    /**
     * Dihitung sebagai rentang, bukan tumpukan. Aset yang habis bulan depan tidak
     * ikut dihitung lagi pada angka dua bulan, supaya ketiga angka bisa dijumlahkan
     * tanpa menghitung barang yang sama dua kali.
     */
    protected function hitungGaransi(int $bulan): int
    {
        $mulai = $bulan === 1
            ? now()->toDateString()
            : now()->addMonths($bulan - 1)->addDay()->toDateString();

        return Asset::query()
            ->where('status', '!=', 'dilepas')
            ->where('has_warranty', true)
            ->whereNotNull('warranty_until')
            ->whereBetween('warranty_until', [$mulai, now()->addMonths($bulan)->toDateString()])
            ->count();
    }
}
