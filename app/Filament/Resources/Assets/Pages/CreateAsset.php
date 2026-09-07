<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    /**
     * Kode aset baru dibuat saat disimpan, jadi pengguna belum melihatnya di form.
     * Menyebutkannya di pemberitahuan membuat kode itu langsung bisa dicatat atau dicetak.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Aset tersimpan')
            ->body('Kode asetnya '.$this->record->code.'. Cetak labelnya lewat tombol Label di daftar aset.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
