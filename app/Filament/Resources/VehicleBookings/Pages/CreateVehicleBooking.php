<?php

namespace App\Filament\Resources\VehicleBookings\Pages;

use App\Filament\Resources\VehicleBookings\VehicleBookingResource;
use App\Models\VehicleBooking;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleBooking extends CreateRecord
{
    protected static string $resource = VehicleBookingResource::class;

    public function getTitle(): string
    {
        return 'Book Vehicle';
    }

    public function getHeading(): string
    {
        return 'Book Vehicle';
    }

    public function getSubheading(): ?string
    {
        return 'Sebutkan kapan dan ke mana. Kendaraannya dipilih tim GA setelah pemesanan disetujui, supaya tidak ada dua orang yang memesan mobil yang sama pada jam yang sama.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        /** @var VehicleBooking $pemesanan */
        $pemesanan = $this->getRecord();

        return Notification::make()
            ->success()
            ->title('Pemesanan '.$pemesanan->code.' diajukan')
            ->body(filled($pemesanan->approval_skipped_reason)
                ? 'Langsung menunggu tim GA menugaskan kendaraan. '.$pemesanan->approval_skipped_reason.'.'
                : 'Menunggu persetujuan '.($pemesanan->approver?->full_name ?? 'kepala departemen').'.');
    }
}
