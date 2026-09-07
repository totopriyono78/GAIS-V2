<?php

namespace App\Filament\Resources\SupplyItems\Pages;

use App\Filament\Resources\SupplyItems\RelationManagers\TransactionsRelationManager;
use App\Filament\Resources\SupplyItems\SupplyItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupplyItem extends EditRecord
{
    protected static string $resource = SupplyItemResource::class;

    /**
     * Angka yang paling dicari orang saat membuka halaman ini adalah stoknya,
     * jadi angka itu ditaruh di subjudul, bukan disembunyikan di dalam tabel.
     */
    public function getSubheading(): ?string
    {
        $record = $this->record;
        $stok = $record->currentStock();

        return 'Stok sekarang '.$record->formatQuantity($stok)
            .', minimum '.$record->formatQuantity($record->minimum_stock)
            .', '.strtolower($record->stockStateLabel($stok)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            SupplyItemResource::catatMutasiAction(iconOnly: false)
                // Riwayat mutasi adalah komponen Livewire tersendiri di bawah formulir,
                // jadi ia tidak ikut digambar ulang saat tombol di kepala halaman ditekan.
                ->after(fn () => $this->dispatch(TransactionsRelationManager::REFRESH_EVENT)),

            // Filament menilai tombol hapus lewat Gate, dan Gate::before meloloskan super
            // admin untuk semua kemampuan, jadi aturan "barang yang sudah punya mutasi
            // tidak boleh dihapus" harus disebut sendiri di sini.
            DeleteAction::make()
                ->visible(fn (): bool => SupplyItemResource::canDelete($this->record)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
