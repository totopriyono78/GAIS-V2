<?php

namespace App\Filament\Resources\VehicleBookings\Pages;

use App\Filament\Resources\VehicleBookings\VehicleBookingResource;
use App\Models\VehicleBooking;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Halaman satu pemesanan.
 *
 * Dibuka pemohon untuk satu pertanyaan: mobil saya jadi atau tidak, dan yang mana.
 * Karena itu subjudulnya selalu menjawab pertanyaan itu lebih dulu, sebelum rincian.
 */
class ViewVehicleBooking extends ViewRecord
{
    protected static string $resource = VehicleBookingResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->code;
    }

    public function getHeading(): string
    {
        /** @var VehicleBooking $pemesanan */
        $pemesanan = $this->getRecord();

        return $pemesanan->code.' ke '.$pemesanan->destination;
    }

    public function getSubheading(): ?string
    {
        /** @var VehicleBooking $pemesanan */
        $pemesanan = $this->getRecord();

        return match ($pemesanan->status) {
            'diajukan' => 'Menunggu persetujuan '.($pemesanan->approver?->full_name ?? 'kepala departemen')
                .'. Dijadwalkan '.$pemesanan->jadwalLabel().'.',
            'disetujui' => 'Sudah disetujui, menunggu tim GA menugaskan kendaraan. Dijadwalkan '.$pemesanan->jadwalLabel().'.',
            'ditugaskan' => ($pemesanan->vehicle?->plate_number ?? 'Kendaraan').' disiapkan untuk '.$pemesanan->jadwalLabel()
                .'. '.($pemesanan->driver ? 'Disopiri '.$pemesanan->driver->full_name.'.' : 'Menyetir sendiri.'),
            'berjalan' => 'Sedang berjalan dengan '.($pemesanan->vehicle?->plate_number ?? 'kendaraan yang tidak tercatat').'.',
            'selesai' => 'Selesai pada '.($pemesanan->closed_at?->translatedFormat('d F Y, H:i') ?? 'waktu yang tidak tercatat').'.',
            'ditolak' => 'Ditolak. Alasannya ada di bagian bawah.',
            'dibatalkan' => 'Dibatalkan, dan kendaraannya kembali kosong pada jam itu.',
            default => null,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            VehicleBookingResource::setujuiAction(iconOnly: false),
            VehicleBookingResource::tugaskanAction(iconOnly: false),
            VehicleBookingResource::tolakAction(iconOnly: false),
            VehicleBookingResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->label('Ubah pemesanan')
                ->visible(fn (): bool => VehicleBookingResource::canEdit($this->getRecord())),
        ];
    }
}
