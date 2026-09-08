<?php

namespace App\Filament\Resources\BusinessTrips\Pages;

use App\Filament\Resources\BusinessTrips\BusinessTripResource;
use App\Models\BusinessTrip;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessTrip extends CreateRecord
{
    protected static string $resource = BusinessTripResource::class;

    public function getSubheading(): ?string
    {
        return 'Setelah disimpan, pengajuan ini menunggu persetujuan kepala departemen yang berangkat. Kalau departemennya belum punya kepala, atau yang berangkat justru kepalanya sendiri, pengajuan langsung berstatus disetujui dan alasannya tertulis di layar.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        /** @var BusinessTrip $perjalanan */
        $perjalanan = $this->getRecord();

        return Notification::make()
            ->success()
            ->title($perjalanan->code.' diajukan')
            ->body(filled($perjalanan->approval_skipped_reason)
                ? 'Langsung berstatus disetujui. '.$perjalanan->approval_skipped_reason
                : 'Menunggu persetujuan '.($perjalanan->approver?->full_name ?? 'kepala departemen').'.');
    }
}
