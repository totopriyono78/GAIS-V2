<?php

namespace App\Filament\Resources\ServiceAreas\Pages;

use App\Filament\Resources\ServiceAreas\ServiceAreaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceAreas extends ListRecords
{
    protected static string $resource = ServiceAreaResource::class;

    public function getSubheading(): ?string
    {
        return 'Area yang dibersihkan dan diperiksa. Daftar inilah yang menjadi lembar pemeriksaan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Area'),
        ];
    }
}
