<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Vehicle;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Halaman satu kendaraan.
 *
 * Ini layar yang dibuka saat seseorang perlu mengurus perpanjangan: nomor rangka dan
 * mesin untuk dibawa ke Samsat, tanggal berakhir tiap dokumen, dan riwayat perpanjangan
 * sebelumnya lengkap dengan biayanya.
 */
class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->plate_number;
    }

    public function getHeading(): string
    {
        /** @var Vehicle $kendaraan */
        $kendaraan = $this->getRecord();

        return $kendaraan->plate_number.' '.$kendaraan->namaLengkap();
    }

    public function getSubheading(): ?string
    {
        /** @var Vehicle $kendaraan */
        $kendaraan = $this->getRecord();

        if (! $kendaraan->is_active) {
            return 'Sudah tidak dipakai, jadi jatuh tempo dokumennya tidak lagi diingatkan.';
        }

        $dokumen = $kendaraan->jatuhTempoTerdekat();

        if ($dokumen === null) {
            return $kendaraan->usageLabel().'. Belum ada dokumen yang dicatat.';
        }

        return $kendaraan->usageLabel().'. Yang paling dekat: '.$dokumen->jenisLabel()
            .', berakhir '.$dokumen->expires_at->translatedFormat('d F Y')
            .', '.strtolower($dokumen->keteranganWaktu()).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Edit Vehicle'),
        ];
    }
}
