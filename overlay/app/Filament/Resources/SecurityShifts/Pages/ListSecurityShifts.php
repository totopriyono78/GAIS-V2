<?php

namespace App\Filament\Resources\SecurityShifts\Pages;

use App\Filament\Resources\SecurityShifts\SecurityShiftResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSecurityShifts extends ListRecords
{
    protected static string $resource = SecurityShiftResource::class;

    public function getSubheading(): ?string
    {
        return 'Rencana jaga dan kehadiran yang sebenarnya, disimpan berdampingan supaya keduanya bisa dibandingkan akhir bulan.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Shift'),
        ];
    }
}
