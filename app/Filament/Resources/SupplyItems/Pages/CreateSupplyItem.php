<?php

namespace App\Filament\Resources\SupplyItems\Pages;

use App\Filament\Resources\SupplyItems\SupplyItemResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplyItem extends CreateRecord
{
    protected static string $resource = SupplyItemResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Barang dibuat, kodenya '.$this->record->code)
            ->body('Stoknya masih nol. Langkah berikutnya: catat mutasi barang masuk supaya stoknya terisi.');
    }

    /**
     * Barang baru langsung dibuka halamannya, karena pekerjaan berikutnya ada di sana:
     * mencatat barang masuk yang pertama.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
