<?php

namespace App\Filament\Resources\CleaningInspections\Pages;

use App\Filament\Resources\CleaningInspections\CleaningInspectionResource;
use App\Models\CleaningInspection;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewCleaningInspection extends ViewRecord
{
    protected static string $resource = CleaningInspectionResource::class;

    /**
     * Menggambar ulang halaman saat satu baris pemeriksaan berubah.
     *
     * Metodenya sengaja kosong, sama seperti pada opname barang habis pakai. Livewire
     * menggambar ulang komponen setiap kali ia menangani sebuah peristiwa, dan itulah yang
     * membuat angka kemajuan serta jumlah temuan di infolist ikut bergerak saat pengawas
     * mencatat hasil di lembar di bawahnya. Tanpa ini, kedua angka itu tetap memperlihatkan
     * keadaan saat halaman pertama dibuka, cacat yang sama seperti D-25 pada kiriman N.
     */
    #[On('rincian-berubah')]
    public function rincianBerubah(): void {}

    public function getSubheading(): ?string
    {
        /** @var CleaningInspection $putaran */
        $putaran = $this->getRecord();

        if ($putaran->isCancelled()) {
            return 'Putaran ini dibatalkan. Hasil yang sempat dicatat tetap tersimpan.';
        }

        if ($putaran->isRunning()) {
            if ($putaran->jumlahBaris() === 0) {
                return 'Daftar areanya kosong, karena cakupan putaran ini tidak menghasilkan satu pun area yang masih dipakai. Ubah cakupannya lewat Ubah, lalu tekan Rebuild Area List.';
            }

            return 'Sedang diperiksa. '.$putaran->sudahDiperiksa().' dari '.$putaran->jumlahBaris()
                .' area sudah dicatat, dan '.$putaran->jumlahTemuan().' di antaranya bermasalah.';
        }

        if ($putaran->isFinished()) {
            return $putaran->jumlahTemuan() === 0
                ? 'Selesai. Seluruh area yang diperiksa dalam keadaan bersih.'
                // Foto tidak disebut sebagai janji, karena tidak setiap temuan berfoto.
                : 'Selesai dengan '.$putaran->jumlahTemuan().' area yang bermasalah. Temuannya tetap terbaca di lembar di bawah, lengkap dengan catatan pengawasnya.';
        }

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            CleaningInspectionResource::selesaikanAction(iconOnly: false),
            CleaningInspectionResource::susunUlangAction(iconOnly: false),
            CleaningInspectionResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => $this->getRecord()->isRunning()
                    && CleaningInspectionResource::canEdit($this->getRecord())),
        ];
    }
}
