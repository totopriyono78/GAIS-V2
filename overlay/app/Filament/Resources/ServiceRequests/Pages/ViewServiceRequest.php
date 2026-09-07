<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/**
 * Halaman satu permintaan perbaikan.
 *
 * Ini layar yang dibuka pemohon untuk menjawab satu pertanyaan: sekarang menunggu siapa.
 * Karena itu perjalanan tiketnya ditulis berurutan waktu, lengkap dengan nama orang di
 * tiap langkah, dan tombol yang tersedia selalu tombol milik langkah berikutnya.
 */
class ViewServiceRequest extends ViewRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->code;
    }

    public function getHeading(): string
    {
        /** @var ServiceRequest $tiket */
        $tiket = $this->getRecord();

        return $tiket->code.' '.$tiket->title;
    }

    public function getSubheading(): ?string
    {
        /** @var ServiceRequest $tiket */
        $tiket = $this->getRecord();

        return match ($tiket->status) {
            'diajukan' => 'Menunggu persetujuan '.($tiket->approver?->full_name ?? 'kepala departemen')
                .'. Sudah menunggu '.$tiket->lamaMenunggu().'.',
            'disetujui' => 'Sudah disetujui, menunggu tim GA menerimanya. Sudah menunggu '.$tiket->lamaMenunggu().'.',
            'diterima' => 'Sedang dikerjakan lewat perintah kerja '.($tiket->workOrder?->code ?? 'yang belum tercatat')
                .'. '.$tiket->slaLabel().'.',
            'selesai' => 'Selesai pada '.($tiket->closed_at?->translatedFormat('d F Y, H:i') ?? 'waktu yang tidak tercatat').'.',
            'ditolak' => 'Ditolak. Alasannya ada di bagian perjalanan tiket di bawah.',
            'dibatalkan' => 'Dibatalkan pemohon.',
            default => null,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            ServiceRequestResource::setujuiAction(iconOnly: false),
            ServiceRequestResource::terimaAction(iconOnly: false),
            ServiceRequestResource::tolakAction(iconOnly: false),
            ServiceRequestResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->label('Ubah permintaan')
                ->visible(fn (): bool => ServiceRequestResource::canEdit($this->getRecord())),
        ];
    }
}
