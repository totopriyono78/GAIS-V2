<?php

namespace App\Filament\Resources\VehicleBookings\Pages;

use App\Filament\Resources\VehicleBookings\VehicleBookingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleBookings extends ListRecords
{
    protected static string $resource = VehicleBookingResource::class;

    public function getSubheading(): ?string
    {
        $dasar = VehicleBookingResource::getEloquentQuery();

        $menungguPersetujuan = (clone $dasar)->menungguPersetujuan()->count();
        $menungguKendaraan = (clone $dasar)->menungguKendaraan()->count();
        $terlambat = (clone $dasar)->terlambatDitutup()->count();

        $bagian = [];

        if ($menungguPersetujuan > 0) {
            $bagian[] = $menungguPersetujuan.' menunggu persetujuan atasan';
        }

        if ($menungguKendaraan > 0) {
            $bagian[] = $menungguKendaraan.' menunggu kendaraan ditugaskan';
        }

        if ($terlambat > 0) {
            $bagian[] = $terlambat.' jadwalnya sudah lewat tetapi perjalanannya belum ditutup';
        }

        if ($bagian === []) {
            return 'Tidak ada pemesanan yang menunggu tindakan. Yang paling dekat berangkat selalu ada di urutan teratas.';
        }

        return ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Pesan kendaraan'),
        ];
    }
}
