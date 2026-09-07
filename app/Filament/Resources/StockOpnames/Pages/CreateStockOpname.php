<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStockOpname extends CreateRecord
{
    protected static string $resource = StockOpnameResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Sesi opname dibuat, nomornya '.$this->record->code)
            ->body('Langkah berikutnya: tekan Susun daftar target untuk mengambil aset yang masuk cakupan.');
    }

    /**
     * Sesi baru langsung dibuka halamannya, karena pekerjaan berikutnya ada di sana:
     * menyusun daftar target lalu memeriksa satu per satu.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
