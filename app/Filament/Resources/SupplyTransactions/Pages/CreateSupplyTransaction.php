<?php

namespace App\Filament\Resources\SupplyTransactions\Pages;

use App\Filament\Resources\SupplyTransactions\Pages\Concerns\GuardsSupplyStock;
use App\Filament\Resources\SupplyTransactions\SupplyTransactionResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplyTransaction extends CreateRecord
{
    use GuardsSupplyStock;

    protected static string $resource = SupplyTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->guardStock($data);

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        $item = $this->record->item;

        return Notification::make()
            ->success()
            ->title('Mutasi '.$this->record->code.' tersimpan')
            ->body($item === null
                ? null
                : 'Stok '.$item->name.' sekarang '.$item->formatQuantity($item->currentStock()).'.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
