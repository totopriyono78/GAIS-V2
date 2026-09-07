<?php

namespace App\Filament\Resources\AssetDisposals\Pages;

use App\Filament\Resources\AssetDisposals\AssetDisposalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetDisposal extends EditRecord
{
    protected static string $resource = AssetDisposalResource::class;

    /**
     * Aset yang dilepas tidak bisa diganti setelah dokumennya dibuat, karena
     * mengganti aset berarti satu aset diam diam kembali dipakai dan aset lain
     * diam diam dilepas, tanpa jejak. Yang bisa diperbaiki adalah keterangannya.
     */
    public function getSubheading(): ?string
    {
        $asset = $this->record->asset;

        if ($asset === null) {
            return null;
        }

        return 'Aset '.$asset->code.' '.$asset->name.', status sekarang '.$asset->statusLabel()
            .'. Untuk mengembalikan aset ini, batalkan pelepasannya.';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Batalkan pelepasan')
                ->modalHeading('Batalkan pelepasan aset')
                ->modalDescription('Dokumen ini dihapus, dan status aset dikembalikan ke keadaan sebelum dilepas. Berkas pendukung yang sudah diunggah ikut hilang.')
                ->modalSubmitActionLabel('Batalkan dan kembalikan'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
