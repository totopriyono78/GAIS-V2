<?php

namespace App\Filament\Resources\SupplyOpnames\Pages;

use App\Filament\Resources\SupplyOpnames\SupplyOpnameResource;
use App\Models\SupplyOpname;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewSupplyOpname extends ViewRecord
{
    protected static string $resource = SupplyOpnameResource::class;

    /**
     * Menggambar ulang halaman saat satu baris hitungan berubah.
     *
     * Metodenya sengaja kosong. Livewire menggambar ulang komponen setiap kali ia menangani
     * sebuah peristiwa, dan itulah yang membuat angka kemajuan serta jumlah selisih di
     * infolist ikut bergerak saat orang mencatat hitungan di lembar di bawahnya. Tanpa ini,
     * kedua angka itu tetap memperlihatkan keadaan saat halaman pertama dibuka, cacat yang
     * sama seperti D-25 pada kiriman N.
     */
    #[On('rincian-berubah')]
    public function rincianBerubah(): void {}

    public function getSubheading(): ?string
    {
        /** @var SupplyOpname $opname */
        $opname = $this->getRecord();

        if ($opname->status === 'dibatalkan') {
            return 'Sesi ini dibatalkan. Stok tidak tersentuh sama sekali.';
        }

        if ($opname->isDraft()) {
            return $opname->jumlahBaris() === 0
                ? 'Daftar barang yang akan dihitung belum disusun. Tekan Build Target List untuk menyusunnya dari cakupan sesi ini.'
                : 'Daftar berisi '.$opname->jumlahBaris().' barang dan siap dihitung. Menyusun ulang daftar akan mengganti seluruh isinya.';
        }

        if ($opname->isRunning()) {
            return 'Sedang dihitung. '.$opname->sudahDihitung().' dari '.$opname->jumlahBaris()
                .' barang sudah dicatat, dan '.$opname->jumlahSelisih()
                .' di antaranya selisih. Stok belum berubah sama sekali.';
        }

        if ($opname->isAdjusted()) {
            return 'Selesai. Stok sudah disesuaikan mengikuti hitungan fisik, dan tiap selisihnya tercatat sebagai mutasi koreksi di buku stok.';
        }

        return 'Selesai dihitung, dengan '.$opname->jumlahSelisih().' barang yang selisih. '
            .'Stok masih memakai angka lama sampai penyesuaian diterapkan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            SupplyOpnameResource::susunAction(iconOnly: false),
            SupplyOpnameResource::mulaiAction(iconOnly: false),
            SupplyOpnameResource::selesaikanAction(iconOnly: false),
            SupplyOpnameResource::terapkanAction(iconOnly: false),
            SupplyOpnameResource::bukaLagiAction(iconOnly: false),
            SupplyOpnameResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => SupplyOpnameResource::canEdit($this->getRecord())),
        ];
    }
}
