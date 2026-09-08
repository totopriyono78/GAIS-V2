<?php

namespace App\Filament\Resources\SupplyOpnames\Pages;

use App\Filament\Resources\SupplyOpnames\SupplyOpnameResource;
use App\Models\SupplyOpname;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplyOpnames extends ListRecords
{
    protected static string $resource = SupplyOpnameResource::class;

    /**
     * Subjudul menyebut sesi yang masih dihitung dan sesi yang sudah selesai tetapi stoknya
     * belum disesuaikan.
     *
     * Angka kedua yang paling perlu terlihat. Sesi yang dihitung dengan susah payah lalu tidak
     * pernah diterapkan adalah cara paling umum opname menjadi pekerjaan sia sia, dan
     * menyebutkannya di sini membuatnya terlihat sebelum lembar hitungannya basi.
     */
    public function getSubheading(): ?string
    {
        $dasar = SupplyOpnameResource::getEloquentQuery();

        if (! (clone $dasar)->exists()) {
            return 'Belum ada sesi opname. Opname mencocokkan buku stok dengan isi rak yang sebenarnya, dan tiap selisihnya menjadi mutasi koreksi yang bisa ditelusuri.';
        }

        $berjalan = (clone $dasar)->where('status', 'berjalan')->count();
        $menunggu = (clone $dasar)->menungguPenyesuaian()->count();

        $bagian = [];

        if ($berjalan > 0) {
            $bagian[] = $berjalan.' sesi sedang dihitung';
        }

        if ($menunggu > 0) {
            $selisih = (clone $dasar)->menungguPenyesuaian()->get()
                ->sum(fn (SupplyOpname $o): int => $o->jumlahSelisih());

            $bagian[] = $menunggu.' sesi sudah selesai dihitung tetapi stoknya belum disesuaikan'
                .($selisih > 0 ? ', dengan '.$selisih.' barang yang selisih' : '');
        }

        if ($bagian === []) {
            return 'Tidak ada sesi yang menunggu tindakan. Seluruh opname yang pernah dibuat sudah selesai atau dibatalkan.';
        }

        return ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Opname Session'),
        ];
    }
}
