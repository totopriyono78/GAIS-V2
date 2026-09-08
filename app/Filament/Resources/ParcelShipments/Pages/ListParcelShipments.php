<?php

namespace App\Filament\Resources\ParcelShipments\Pages;

use App\Filament\Resources\ParcelShipments\ParcelShipmentResource;
use App\Models\ParcelShipment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParcelShipments extends ListRecords
{
    protected static string $resource = ParcelShipmentResource::class;

    public function getSubheading(): ?string
    {
        $menunggu = ParcelShipment::query()->menunggu()->count();

        if ($menunggu === 0) {
            return 'Permintaan kirim paket dan biaya sebenarnya. Tidak ada paket yang menunggu diantar.';
        }

        return 'Permintaan kirim paket dan biaya sebenarnya. '.$menunggu.' paket menunggu diantar ke ekspedisi.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Request Shipment'),
        ];
    }
}
