<?php

namespace App\Filament\Resources\BusinessTrips\Pages;

use App\Filament\Resources\BusinessTrips\BusinessTripResource;
use App\Models\BusinessTrip;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessTrips extends ListRecords
{
    protected static string $resource = BusinessTripResource::class;

    public function getSubheading(): ?string
    {
        $menungguSetuju = BusinessTrip::query()->menungguPersetujuan()->count();
        $menungguBayar = BusinessTrip::query()->menungguUangMuka()->count();

        $bagian = [];

        if ($menungguSetuju > 0) {
            $bagian[] = $menungguSetuju.' menunggu persetujuan';
        }

        if ($menungguBayar > 0) {
            $bagian[] = $menungguBayar.' menunggu uang mukanya dibayarkan';
        }

        return $bagian === []
            ? 'Perjalanan dinas beserta uang muka dan pertanggungjawabannya. Tidak ada yang sedang menunggu tindakan.'
            : 'Perjalanan dinas beserta uang muka dan pertanggungjawabannya. '.implode(', ', $bagian).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Trip'),
        ];
    }
}
