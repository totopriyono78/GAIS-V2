<?php

namespace App\Filament\Resources\IncidentReports\Pages;

use App\Filament\Resources\IncidentReports\IncidentReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIncidentReports extends ListRecords
{
    protected static string $resource = IncidentReportResource::class;

    public function getSubheading(): ?string
    {
        return 'Buku kejadian keamanan. Yang butuh perbaikan fisik diteruskan menjadi tiket perbaikan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Report Incident'),
        ];
    }
}
