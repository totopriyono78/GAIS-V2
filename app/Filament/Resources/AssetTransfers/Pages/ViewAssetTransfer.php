<?php

namespace App\Filament\Resources\AssetTransfers\Pages;

use App\Filament\Resources\AssetTransfers\AssetTransferResource;
use App\Models\AssetTransfer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetTransfer extends ViewRecord
{
    protected static string $resource = AssetTransferResource::class;

    public function getTitle(): string
    {
        return 'Asset Transfer '.$this->record->code;
    }

    public function getSubheading(): ?string
    {
        $record = $this->record;

        return $record->transfer_date->translatedFormat('d F Y').'. '.$record->describeChanges().'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bam')
                ->label('Download BAM')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn (): string => route('gais.aset.berita-acara', [
                    'transfer' => $this->record->getKey(),
                    'jenis' => 'bam',
                ]))
                ->openUrlInNewTab(),

            Action::make('bast')
                ->label('Download BAST')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (): string => route('gais.aset.berita-acara', [
                    'transfer' => $this->record->getKey(),
                    'jenis' => 'bast',
                ]))
                ->openUrlInNewTab(),

            Action::make('kartu_riwayat')
                ->label('Asset History Card')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->visible(fn (): bool => $this->record->asset_id !== null)
                ->url(fn (): string => route('gais.aset.riwayat', ['asset' => $this->record->asset_id]))
                ->openUrlInNewTab(),

            DeleteAction::make()
                ->label('Cancel Transfer')
                ->visible(fn (): bool => AssetTransferResource::canDelete($this->record))
                ->modalHeading('Cancel Asset Transfer')
                ->modalDescription('Dokumen ini dihapus, dan aset dikembalikan ke lokasi, penanggung jawab, serta departemen sebelum serah terima ini. Hanya bisa dilakukan selama belum ada perpindahan lain sesudahnya.')
                ->modalSubmitActionLabel('Cancel and Return'),
        ];
    }
}
