<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\SupplyTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Pemakaian barang habis pakai per departemen selama enam bulan terakhir.
 *
 * Batang, bukan lingkaran, karena pertanyaannya membandingkan besaran antar
 * departemen, dan batang jauh lebih mudah dibandingkan daripada potongan lingkaran.
 * Angka ini yang nanti menjadi dasar membandingkan anggaran ATK dengan pemakaian
 * sebenarnya, dan itulah alasan departemen diwajibkan saat mencatat barang keluar.
 */
class PersediaanPemakaianChart extends ChartWidget
{
    protected ?string $heading = 'Usage by Department, Last Six Months';

    protected ?string $description = 'Dihitung dari jumlah satuan barang keluar, belum dikali harga.';

    protected static ?int $sort = 2;

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
        return Auth::user()?->hasPermission('supply_transactions.read') ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $sejak = now()->subMonths(6)->startOfMonth()->toDateString();

        $hitung = SupplyTransaction::query()
            ->where('type', 'keluar')
            ->whereDate('transaction_date', '>=', $sejak)
            ->whereNotNull('department_id')
            ->selectRaw('department_id, sum(abs(quantity)) as jumlah')
            ->groupBy('department_id')
            ->orderByDesc('jumlah')
            ->limit(12)
            ->pluck('jumlah', 'department_id');

        $departemen = Department::query()->whereIn('id', $hitung->keys())->pluck('name', 'id');

        return [
            'datasets' => [[
                'label' => 'Satuan barang keluar',
                'data' => $hitung->values()->map(fn ($n) => (int) $n)->all(),
                'backgroundColor' => '#38707F',
                'borderColor' => '#17505E',
                'borderWidth' => 1,
            ]],
            'labels' => $hitung->keys()->map(fn ($id) => $departemen[$id] ?? 'Tidak dicatat')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }
}
