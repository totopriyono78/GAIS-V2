<?php

namespace App\Filament\Resources\CleaningInspections\Pages;

use App\Filament\Resources\CleaningInspections\CleaningInspectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCleaningInspections extends ListRecords
{
    protected static string $resource = CleaningInspectionResource::class;

    public function getSubheading(): ?string
    {
        return 'Putaran pemeriksaan kebersihan oleh pengawas GA. Yang tercatat adalah hasil pemeriksaan, bukan laporan petugas.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Inspection'),
        ];
    }
}
