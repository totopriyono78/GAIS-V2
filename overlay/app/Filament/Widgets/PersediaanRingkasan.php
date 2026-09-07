<?php

namespace App\Filament\Widgets;

use App\Models\SupplyItem;
use App\Support\Rupiah;
use App\Models\SupplyTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Angka pembuka tab Persediaan.
 *
 * Nilai persediaan dihitung dari stok dikali harga satuan terakhir. Itu perkiraan,
 * bukan nilai akuntansi, karena harga tiap pembelian berbeda dan metode penilaian
 * persediaan belum ditetapkan. Keterangannya ditulis di layar supaya angkanya tidak
 * terbaca sebagai angka yang siap masuk laporan keuangan.
 */
class PersediaanRingkasan extends StatsOverviewWidget
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
        return Auth::user()?->hasPermission('supply_items.read') ?? false;
    }

    protected function getStats(): array
    {
        $aktif = SupplyItem::query()->where('is_active', true);

        $jenis = (clone $aktif)->count();
        $perluDipesan = (clone $aktif)->needsRestock()->count();
        $habis = (clone $aktif)->outOfStock()->count();

        $nilai = 0.0;

        SupplyItem::query()
            ->where('is_active', true)
            ->whereNotNull('last_price')
            ->withSum('transactions', 'quantity')
            ->chunkById(200, function ($barang) use (&$nilai) {
                foreach ($barang as $item) {
                    $stok = max((int) $item->transactions_sum_quantity, 0);
                    $nilai += $stok * (float) $item->last_price;
                }
            });

        $keluarBulanIni = (int) abs(SupplyTransaction::query()
            ->where('type', 'keluar')
            ->whereBetween('transaction_date', [now()->startOfMonth()->toDateString(), now()->toDateString()])
            ->sum('quantity'));

        return [
            Stat::make('Jenis barang', number_format($jenis, 0, ',', '.'))
                ->description('Masih dipakai')
                ->color('primary'),

            Stat::make('Perlu dipesan', number_format($perluDipesan, 0, ',', '.'))
                ->description($habis > 0 ? "Termasuk {$habis} yang stoknya sudah habis" : 'Belum ada yang habis')
                ->color($perluDipesan > 0 ? 'warning' : 'success'),

            Stat::make('Perkiraan nilai persediaan', Rupiah::ringkas($nilai))
                ->description(Rupiah::penuh($nilai).' dari harga terakhir, bukan nilai akuntansi')
                ->color('gray'),

            Stat::make('Keluar bulan ini', number_format($keluarBulanIni, 0, ',', '.'))
                ->description('Jumlah satuan dari seluruh barang')
                ->color('gray'),
        ];
    }
}
