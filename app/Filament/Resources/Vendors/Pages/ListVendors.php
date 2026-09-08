<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    public function getSubheading(): ?string
    {
        return 'Tukang servis, bengkel, dan pemasok yang mengerjakan pemeliharaan. Dipakai juga oleh modul tagihan nanti.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Vendor'),
        ];
    }
}
