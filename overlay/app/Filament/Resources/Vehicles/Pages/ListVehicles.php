<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Vehicle;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicles extends ListRecords
{
    protected static string $resource = VehicleResource::class;

    public function getSubheading(): ?string
    {
        $aktif = Vehicle::query()->aktif()->count();

        if ($aktif === 0) {
            return 'Kendaraan didaftarkan dulu sebagai aset, lalu diberi nomor polisi dan dokumen di sini.';
        }

        $terlambat = Vehicle::query()->aktif()->dokumenJatuhTempo(-1)->count();
        $segera = Vehicle::query()->aktif()->dokumenJatuhTempo(30)->count() - $terlambat;

        if ($terlambat === 0 && $segera === 0) {
            return $aktif.' kendaraan dipakai, dan tidak ada dokumen yang jatuh tempo dalam 30 hari ke depan.';
        }

        $bagian = [];

        if ($terlambat > 0) {
            $bagian[] = $terlambat.' kendaraan dokumennya sudah lewat';
        }

        if ($segera > 0) {
            $bagian[] = $segera.' jatuh tempo dalam 30 hari';
        }

        return ucfirst(implode(', ', $bagian)).'. Yang paling dekat jatuh tempo ada di urutan teratas.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Daftarkan kendaraan')
                ->modalHeading('Daftarkan kendaraan')
                ->modalSubmitActionLabel('Simpan kendaraan'),
        ];
    }
}
