<?php

namespace App\Filament\Resources\SupplyPurchases\Pages;

use App\Filament\Resources\SupplyPurchases\SupplyPurchaseResource;
use App\Models\SupplyPurchase;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplyPurchases extends ListRecords
{
    protected static string $resource = SupplyPurchaseResource::class;

    /**
     * Subjudul menyebut berapa yang menunggu di tiap meja, ditambah berapa pesanan yang sudah
     * lewat tanggal janji.
     *
     * Angka terakhir itu yang paling berguna di layar ini. Pesanan yang disetujui lalu
     * dilupakan karena rekanannya tidak pernah mengirim adalah cara paling umum kehabisan ATK
     * tanpa ada yang merasa bersalah, dan menyebutkannya di sini membuatnya terlihat sebelum
     * gudangnya kosong.
     */
    public function getSubheading(): ?string
    {
        $dasar = SupplyPurchaseResource::getEloquentQuery();

        if (! (clone $dasar)->exists()) {
            return 'Belum ada pembelian yang tercatat. Sejak kiriman ini, stok ATK hanya bertambah lewat penerimaan barang, jadi tiap tambahan stok selalu bisa ditelusuri sampai ke pesanan atau notanya.';
        }

        $menungguSetuju = (clone $dasar)->menungguPersetujuan()->count();
        $menungguBarang = (clone $dasar)->menungguBarang()->count();

        $bagian = [];

        if ($menungguSetuju > 0) {
            $nilai = (clone $dasar)->menungguPersetujuan()->with('lines')->get()
                ->sum(fn (SupplyPurchase $p): float => $p->total());

            $bagian[] = $menungguSetuju.' menunggu persetujuan manajer, senilai '.Rupiah::penuh($nilai);
        }

        if ($menungguBarang > 0) {
            $bagian[] = $menungguBarang.' sudah disetujui dan barangnya masih ditunggu';

            $lewatJanji = (clone $dasar)->menungguBarang()
                ->whereNotNull('expected_date')
                ->whereDate('expected_date', '<', now()->toDateString())
                ->count();

            if ($lewatJanji > 0) {
                $bagian[] = $lewatJanji.' di antaranya sudah lewat tanggal yang dijanjikan';
            }
        }

        if ($bagian === []) {
            return 'Tidak ada pembelian yang menunggu tindakan. Yang belum selesai selalu ada di urutan teratas.';
        }

        return ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Purchase'),
        ];
    }
}
