<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use App\Models\ReimbursementLine;
use App\Support\Rupiah;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Angka penggantian biaya karyawan di tab Anggaran.
 *
 * Ketiga kartu pertama mengikuti tiga meja yang dilewati satu pengajuan, dan urutannya
 * sengaja sama dengan urutan alurnya, supaya orang membaca kartu ini seperti membaca
 * antrean: menunggu atasan, menunggu tim GA, menunggu ditransfer.
 *
 * Kartu ketiga membawa nilainya, bukan hanya jumlah pengajuannya, karena itulah uang
 * karyawan yang sudah dikeluarkan dan belum kembali. Angka itu yang paling cepat
 * dikeluhkan orang, dan menyembunyikannya tidak membuatnya lebih kecil.
 *
 * Seluruh angka memakai penyempitan daftar yang sama dengan menunya. Karyawan biasa
 * melihat pengajuannya sendiri, kepala departemen melihat departemennya. Dasbor yang
 * menyebut angka yang tidak boleh dibuka orangnya hanya menimbulkan pertanyaan yang tidak
 * bisa dijawab layar mana pun.
 */
class PenggantianBiayaRingkasan extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    /*
     * Bukan static. StatsOverviewWidget mendeklarasikan $heading sebagai properti biasa,
     * berbeda dari TableWidget yang memakai properti static, dan menimpanya dengan static
     * membuat seluruh aplikasi berhenti dengan galat fatal saat kelas ini dimuat.
     */
    protected ?string $heading = 'Penggantian biaya karyawan';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('reimbursements.read') ?? false;
    }

    protected function getStats(): array
    {
        $dasar = fn (): Builder => ReimbursementResource::getEloquentQuery();

        $menungguAtasan = $dasar()->menungguAtasan()->count();
        $menungguGa = $dasar()->menungguGa()->count();
        $menungguTransfer = $dasar()->menungguPembayaran()->count();

        $nilaiTransfer = $this->nilai($dasar()->menungguPembayaran());

        $tahun = (int) now()->format('Y');
        $sudahDiganti = $this->nilai(
            $dasar()->where('reimbursements.status', 'dibayar'),
            $tahun,
        );

        return [
            Stat::make('Menunggu atasan', number_format($menungguAtasan, 0, ',', '.'))
                ->description($menungguAtasan > 0
                    ? 'Menunggu tanda tangan kepala departemen pemohon'
                    : 'Tidak ada yang menunggu tanda tangan atasan')
                ->color($menungguAtasan > 0 ? 'warning' : 'success'),

            Stat::make('Menunggu tim GA', number_format($menungguGa, 0, ',', '.'))
                ->description($menungguGa > 0
                    ? 'Struknya belum diperiksa'
                    : 'Tidak ada yang menunggu diperiksa')
                ->color($menungguGa > 0 ? 'warning' : 'success'),

            Stat::make('Menunggu ditransfer', $menungguTransfer > 0 ? Rupiah::ringkas($nilaiTransfer) : 'Tidak ada')
                ->description($menungguTransfer > 0
                    ? $menungguTransfer.' pengajuan, '.Rupiah::penuh($nilaiTransfer).' uang karyawan yang belum kembali'
                    : 'Tidak ada uang karyawan yang belum kembali')
                ->color($menungguTransfer > 0 ? 'danger' : 'success'),

            Stat::make('Sudah diganti tahun '.$tahun, $sudahDiganti > 0 ? Rupiah::ringkas($sudahDiganti) : 'Belum ada')
                ->description($sudahDiganti > 0
                    ? Rupiah::penuh($sudahDiganti).' menurut tanggal struknya'
                    : 'Belum ada pengajuan yang selesai diganti tahun ini')
                ->color('gray'),
        ];
    }

    /**
     * Menjumlahkan struk dari sekumpulan pengajuan.
     *
     * Nilai pengajuan tidak pernah disimpan sebagai kolom, jadi penjumlahannya selalu
     * lewat barisnya. Penyaring tahun memakai tanggal struk, bukan tanggal transfer,
     * supaya angkanya sama persis dengan yang dipakai layar anggaran.
     */
    protected function nilai(Builder $pengajuan, ?int $tahun = null): float
    {
        $query = ReimbursementLine::query()
            ->whereIn('reimbursement_lines.reimbursement_id', $pengajuan->select('reimbursements.id'));

        if ($tahun !== null) {
            $query->whereYear('reimbursement_lines.expense_date', $tahun);
        }

        return round((float) $query->sum('reimbursement_lines.amount'), 2);
    }
}
