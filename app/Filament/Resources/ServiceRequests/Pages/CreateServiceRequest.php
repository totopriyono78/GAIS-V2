<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function getTitle(): string
    {
        return 'New Corrective Maintenance';
    }

    public function getHeading(): string
    {
        return 'New Corrective Maintenance';
    }

    public function getSubheading(): ?string
    {
        return 'Laporkan apa yang rusak. Setelah disetujui atasan dan diterima tim GA, permintaan ini berubah menjadi perintah kerja dan Anda bisa mengikuti perkembangannya dari sini.';
    }

    /**
     * Setelah disimpan, orang dibawa ke halaman tiketnya sendiri, bukan kembali ke daftar.
     * Yang baru melapor ingin tahu satu hal: sekarang menunggu siapa. Jawabannya ada di
     * halaman itu, bukan di baris kesekian sebuah tabel.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        /** @var ServiceRequest $tiket */
        $tiket = $this->getRecord();

        return Notification::make()
            ->success()
            ->title('Permintaan '.$tiket->code.' diajukan')
            ->body(filled($tiket->approval_skipped_reason)
                ? 'Langsung masuk antrean tim GA. '.$tiket->approval_skipped_reason.'.'
                : 'Menunggu persetujuan '.($tiket->approver?->full_name ?? 'kepala departemen').'.');
    }
}
