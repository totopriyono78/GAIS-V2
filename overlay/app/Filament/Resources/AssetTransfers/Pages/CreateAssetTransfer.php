<?php

namespace App\Filament\Resources\AssetTransfers\Pages;

use App\Filament\Resources\AssetTransfers\AssetTransferResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAssetTransfer extends CreateRecord
{
    protected static string $resource = AssetTransferResource::class;

    /**
     * Dokumen yang tidak memindahkan apa apa tidak layak disimpan. Kalau ketiga
     * kolom tujuan kosong, yang terjadi hanya menambah satu baris riwayat yang
     * isinya sama dengan keadaan sebelumnya.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $adaTujuan = filled($data['to_location_id'] ?? null)
            || filled($data['to_custodian_employee_id'] ?? null)
            || filled($data['to_department_id'] ?? null);

        if (! $adaTujuan) {
            $pesan = 'Isi paling tidak satu tujuan: lokasi, penanggung jawab, atau departemen.';

            Notification::make()
                ->title('Serah terima tidak disimpan')
                ->body($pesan)
                ->danger()
                ->persistent()
                ->send();

            throw ValidationException::withMessages([
                'data.to_location_id' => $pesan,
            ]);
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        $record = $this->record;

        return Notification::make()
            ->success()
            ->title('Serah terima '.$record->code.' tersimpan')
            ->body('Data aset '.($record->asset?->code ?? '').' sudah ikut diperbarui. '.$record->describeChanges().'.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
