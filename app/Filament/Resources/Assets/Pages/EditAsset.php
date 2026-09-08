<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('riwayat')
                ->label('History Card')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->url(fn (): string => route('gais.aset.riwayat', ['asset' => $this->record->getKey()]))
                ->openUrlInNewTab(),
            Action::make('label')
                ->label('Print Labels')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('gais.cetak.label-aset', ['ids' => $this->record->getKey()]))
                ->openUrlInNewTab()
                ->visible(fn (): bool => AssetResource::canPrint()),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
