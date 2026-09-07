<?php

namespace App\Filament\Resources\SupplyTransactions\Pages;

use App\Filament\Resources\SupplyTransactions\Pages\Concerns\GuardsSupplyStock;
use App\Filament\Resources\SupplyTransactions\SupplyTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupplyTransaction extends EditRecord
{
    use GuardsSupplyStock;

    protected static string $resource = SupplyTransactionResource::class;

    public function getSubheading(): ?string
    {
        $item = $this->record->item;

        if ($item === null) {
            return null;
        }

        return 'Stok '.$item->name.' sekarang '.$item->formatQuantity($item->currentStock())
            .'. Mengubah mutasi ini ikut mengubah angka itu.';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mutasi yang sedang diubah dikeluarkan dari perhitungan, kalau tidak
        // angka lamanya ikut dihitung dan pemeriksaannya jadi salah.
        $this->guardStock($data, $this->record->getKey());

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
