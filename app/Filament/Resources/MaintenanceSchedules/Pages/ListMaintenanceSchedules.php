<?php

namespace App\Filament\Resources\MaintenanceSchedules\Pages;

use App\Filament\Resources\MaintenanceSchedules\MaintenanceScheduleResource;
use App\Models\MaintenanceVisit;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceSchedules extends ListRecords
{
    protected static string $resource = MaintenanceScheduleResource::class;

    /**
     * Subjudul menyebut berapa yang sudah lewat. Angka itu yang menentukan apakah orang
     * perlu bertindak hari ini, dan menaruhnya di atas berarti tidak ada yang perlu
     * menyaring dulu untuk mengetahuinya.
     */
    public function getSubheading(): ?string
    {
        $lewat = MaintenanceVisit::query()->terlambat()->count();
        $segera = MaintenanceVisit::query()->jatuhTempo(30)->count() - $lewat;

        if ($lewat === 0 && $segera <= 0) {
            return 'Tidak ada pekerjaan yang jatuh tempo dalam 30 hari ke depan.';
        }

        $bagian = [];

        if ($lewat > 0) {
            $bagian[] = $lewat.' sudah lewat jatuh tempo';
        }

        if ($segera > 0) {
            $bagian[] = $segera.' jatuh tempo dalam 30 hari';
        }

        return ucfirst(implode(', ', $bagian)).'. Baris paling mendesak ada di urutan teratas.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah jadwal'),
        ];
    }
}
