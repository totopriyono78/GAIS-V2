<?php

namespace App\Filament\Resources\MaintenanceSchedules\Pages;

use App\Filament\Resources\MaintenanceSchedules\MaintenanceScheduleResource;
use App\Models\MaintenanceSchedule;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

/**
 * Halaman riwayat satu jadwal pemeliharaan.
 *
 * Ini layar yang dibuka saat seseorang bertanya apakah servis kuartal lalu benar benar
 * dikerjakan vendor, dan saat vendor menagih pekerjaan yang tanggalnya perlu dicocokkan.
 */
class ViewMaintenanceSchedule extends ViewRecord
{
    protected static string $resource = MaintenanceScheduleResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }

    public function getHeading(): string
    {
        return $this->getRecord()->name;
    }

    public function getSubheading(): ?string
    {
        /** @var MaintenanceSchedule $jadwal */
        $jadwal = $this->getRecord();

        $aset = trim(($jadwal->asset?->code ?? '').' '.($jadwal->asset?->name ?? '')) ?: 'aset yang tidak tercatat';

        if (! $jadwal->is_active) {
            return 'Jadwal untuk '.$aset.'. Sedang dihentikan, jadi tidak ada kunjungan baru yang dibuat.';
        }

        return 'Jadwal untuk '.$aset.'. '.$jadwal->keadaanLabel().'.';
    }

    /**
     * Menutup kunjungan terjadi di komponen daftar di bawah, bukan di halaman ini.
     * Metode kosong ini cukup untuk memaksa halaman menggambar ulang, sehingga rekap
     * dan jatuh tempo di atas ikut menyusul tanpa perlu disegarkan tangan.
     */
    #[On(MaintenanceScheduleResource::REFRESH_EVENT)]
    public function segarkanRekap(): void
    {
        //
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Edit Schedule'),
        ];
    }
}
