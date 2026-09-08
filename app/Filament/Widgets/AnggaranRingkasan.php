<?php

namespace App\Filament\Widgets;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Services\RealisasiBiaya;
use App\Support\Rupiah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Angka pembuka tab Anggaran, seluruhnya untuk tahun berjalan.
 *
 * Kartu keempat adalah yang paling mudah dilupakan dan paling mahal kalau tidak ada:
 * nilai faktur dan struk yang sudah masuk tetapi belum disetujui. Uang itu memang belum
 * jadi realisasi, tetapi sudah terlanjur keluar dari kas seseorang, dan tanpa disebut di
 * sini seluruh pagu di layar ini akan terlihat sehat tepat pada hari ia sedang tidak.
 */
class AnggaranRingkasan extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('budgets.read') ?? false;
    }

    protected function getStats(): array
    {
        $tahun = (int) now()->format('Y');

        $anggaran = Budget::query()->tahun($tahun)->with(['category', 'department'])->get();

        if ($anggaran->isEmpty()) {
            return [
                Stat::make('Pagu tahun '.$tahun, 'Belum ditetapkan')
                    ->description('Realisasi tetap dijumlahkan dari catatan yang ada, tetapi belum punya pembanding')
                    ->color('gray'),
            ];
        }

        $pagu = (float) $anggaran->sum(fn (Budget $b): float => (float) $b->amount);
        $realisasi = (float) $anggaran->sum(fn (Budget $b): float => $b->realisasi());
        $lewat = $anggaran->filter(fn (Budget $b): bool => $b->keadaan() === 'lewat')->count();
        $mendekati = $anggaran->filter(fn (Budget $b): bool => $b->keadaan() === 'mendekati')->count();

        $persen = $pagu > 0 ? round($realisasi / $pagu * 100, 1) : null;

        $layanan = app(RealisasiBiaya::class);

        $tertunda = (float) ExpenseCategory::query()
            ->where('is_active', true)
            ->get()
            ->sum(fn (ExpenseCategory $k): float => $layanan->tertundaSeluruhnya($k, $tahun));

        return [
            Stat::make('Pagu tahun '.$tahun, Rupiah::ringkas($pagu))
                ->description(Rupiah::penuh($pagu).' dari '.$anggaran->count().' baris pagu')
                ->color('gray'),

            Stat::make('Realisasi', Rupiah::ringkas($realisasi))
                ->description($persen === null
                    ? 'Pagunya nol, jadi persentasenya tidak bisa dihitung'
                    : number_format($persen, 1, ',', '.').' persen dari pagu, dijumlahkan sendiri dari catatan yang ada')
                ->color($persen !== null && $persen > 100 ? 'danger' : 'primary'),

            Stat::make('Pagu terlewati', number_format($lewat, 0, ',', '.'))
                ->description($lewat > 0
                    ? ($mendekati > 0 ? $mendekati.' lagi mendekati batas' : 'Perlu ditinjau sekarang')
                    : ($mendekati > 0 ? $mendekati.' mendekati batas' : 'Seluruh pagu masih aman'))
                ->color($lewat > 0 ? 'danger' : ($mendekati > 0 ? 'warning' : 'success')),

            Stat::make('Belum disetujui', $tertunda > 0 ? Rupiah::ringkas($tertunda) : 'Tidak ada')
                ->description($tertunda > 0
                    ? Rupiah::penuh($tertunda).' dari tagihan rekanan dan struk karyawan, belum masuk realisasi'
                    : 'Tidak ada faktur atau struk yang menunggu persetujuan')
                ->color($tertunda > 0 ? 'warning' : 'success'),
        ];
    }
}
