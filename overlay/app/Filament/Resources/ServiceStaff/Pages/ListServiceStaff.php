<?php

namespace App\Filament\Resources\ServiceStaff\Pages;

use App\Filament\Resources\ServiceStaff\ServiceStaffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceStaff extends ListRecords
{
    protected static string $resource = ServiceStaffResource::class;

    public function getSubheading(): ?string
    {
        return 'Petugas kebersihan dan keamanan, karyawan perusahaan maupun tenaga dari rekanan penyedia.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Staff'),
        ];
    }
}
