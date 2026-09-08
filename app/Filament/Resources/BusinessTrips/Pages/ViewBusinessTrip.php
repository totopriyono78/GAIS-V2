<?php

namespace App\Filament\Resources\BusinessTrips\Pages;

use App\Filament\Resources\BusinessTrips\BusinessTripResource;
use App\Models\BusinessTrip;
use App\Support\Rupiah;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewBusinessTrip extends ViewRecord
{
    protected static string $resource = BusinessTripResource::class;

    /**
     * Menggambar ulang halaman saat satu baris pengeluaran berubah.
     *
     * Metodenya sengaja kosong. Biaya sebenarnya dan kalimat kurang bayar ada di infolist
     * halaman ini, sedangkan yang mengubahnya adalah tombol di relation manager, yaitu
     * komponen Livewire yang berbeda. Tanpa ini, kalimat penyelesaiannya tetap menunjukkan
     * keadaan saat halaman pertama dibuka, cacat yang sama seperti D-25 pada kiriman N.
     */
    #[On('rincian-berubah')]
    public function rincianBerubah(): void {}

    public function getSubheading(): ?string
    {
        /** @var BusinessTrip $perjalanan */
        $perjalanan = $this->getRecord();

        return match (true) {
            $perjalanan->isDiajukan() => 'Menunggu persetujuan '.($perjalanan->approver?->full_name ?? 'kepala departemen').'. Belum ada uang yang keluar.',
            $perjalanan->isDisetujui() && ! $perjalanan->uangMukaSudahDibayar() => 'Sudah disetujui. Uang mukanya belum dibayarkan tim GA.',
            $perjalanan->isDisetujui() => 'Uang muka '.$perjalanan->uangMukaLabel().' sudah dibayarkan. Catat rincian biayanya di bawah, lalu ajukan pertanggungjawabannya.',
            $perjalanan->isDipertanggungjawabkan() => 'Menunggu diperiksa tim GA. '.$perjalanan->selisihKalimat(),
            $perjalanan->isSelesai() => 'Selesai. '.$perjalanan->selisihKalimat().' Biayanya sebesar '.Rupiah::penuh($perjalanan->totalRealisasi()).' sudah masuk realisasi anggaran.',
            $perjalanan->status === 'ditolak' => 'Pengajuan ini ditolak. '.($perjalanan->rejection_reason ?? ''),
            default => 'Pengajuan ini dibatalkan sebelum uang muka dibayarkan.',
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            BusinessTripResource::setujuiAction(iconOnly: false),
            BusinessTripResource::tolakAction(iconOnly: false),
            BusinessTripResource::bayarUangMukaAction(iconOnly: false),
            BusinessTripResource::pertanggungjawabkanAction(iconOnly: false),
            BusinessTripResource::tutupAction(iconOnly: false),
            BusinessTripResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => $this->getRecord()->masihBerjalan()
                    && BusinessTripResource::canEdit($this->getRecord())),
        ];
    }
}
