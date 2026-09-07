<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceVisit;
use App\Models\WorkOrder;
use App\Services\BiayaPemeliharaan;
use App\Support\Rupiah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Angka pembuka tab Pemeliharaan. Semuanya dihitung dari basis data saat halaman dibuka.
 */
class PemeliharaanRingkasan extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('work_orders.read') ?? false;
    }

    protected function getStats(): array
    {
        $terbuka = WorkOrder::query()->terbuka()->count();
        $mendesak = WorkOrder::query()->terbuka()->where('priority', 'mendesak')->count();
        $terlewat = MaintenanceVisit::query()->terlambat()->count();

        /*
         * Keduanya lewat BiayaPemeliharaan, bukan langsung ke tabel perintah kerja.
         * Kunjungan preventif bisa ditutup tanpa perintah kerja, dan biayanya tersimpan
         * di kunjungan itu. Menjumlahkan perintah kerja saja membuat dasbor menyebut
         * angka yang lebih kecil daripada layar jadwal untuk bulan yang sama.
         */
        $biayaBulanIni = BiayaPemeliharaan::antara(now()->startOfMonth(), now()->endOfMonth());
        $selesaiBulanIni = BiayaPemeliharaan::jumlahSelesaiAntara(now()->startOfMonth(), now()->endOfMonth());

        return [
            Stat::make('Pekerjaan belum selesai', number_format($terbuka, 0, ',', '.'))
                ->description($mendesak > 0 ? $mendesak.' di antaranya mendesak' : 'Tidak ada yang mendesak')
                ->color($mendesak > 0 ? 'danger' : ($terbuka > 0 ? 'warning' : 'success')),

            Stat::make('Kunjungan lewat jatuh tempo', number_format($terlewat, 0, ',', '.'))
                ->description($terlewat > 0 ? 'Perlu dikerjakan atau ditandai dilewati' : 'Semua kunjungan masih di depan')
                ->color($terlewat > 0 ? 'danger' : 'success'),

            Stat::make('Selesai bulan ini', number_format($selesaiBulanIni, 0, ',', '.'))
                ->description('Preventif dan korektif, menurut tanggal selesainya')
                ->color('primary'),

            Stat::make('Biaya bulan ini', Rupiah::ringkas($biayaBulanIni))
                ->description(Rupiah::penuh($biayaBulanIni).' dari pekerjaan yang selesai')
                ->color('gray'),
        ];
    }
}
