<?php

namespace App\Filament\Resources\AssetDisposals\Pages;

use App\Filament\Resources\AssetDisposals\AssetDisposalResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetDisposal extends CreateRecord
{
    protected static string $resource = AssetDisposalResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        $record = $this->record;

        return Notification::make()
            ->success()
            ->title('Pelepasan '.$record->code.' tersimpan')
            ->body('Status aset '.($record->asset?->code ?? '').' sekarang Sudah dilepas, dan aset itu tidak ikut lagi dalam stock opname.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
