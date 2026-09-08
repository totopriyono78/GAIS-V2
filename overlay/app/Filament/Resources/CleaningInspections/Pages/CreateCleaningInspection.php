<?php

namespace App\Filament\Resources\CleaningInspections\Pages;

use App\Filament\Resources\CleaningInspections\CleaningInspectionResource;
use App\Models\CleaningInspection;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCleaningInspection extends CreateRecord
{
    protected static string $resource = CleaningInspectionResource::class;

    public function getSubheading(): ?string
    {
        return 'Yang diisi di sini hanya tanggal dan cakupannya. Daftar area yang akan diperiksa disusun sendiri begitu putaran ini disimpan.';
    }

    /**
     * Daftar areanya disusun langsung setelah putaran tersimpan, bukan lewat tombol tersendiri.
     *
     * Berbeda dari opname, yang menyusun daftarnya lewat tombol karena penyusunan itu
     * sekaligus membekukan stok dan karenanya perlu jadi keputusan yang disengaja. Pemeriksaan
     * kebersihan tidak membekukan apa pun, jadi tombol tambahan di situ hanya akan menjadi
     * satu langkah yang harus dihafal orang tanpa memberi kesempatan memutuskan apa pun.
     */
    protected function afterCreate(): void
    {
        /** @var CleaningInspection $putaran */
        $putaran = $this->getRecord();

        $jumlah = $putaran->generateLines();

        if ($jumlah === 0) {
            Notification::make()
                ->warning()
                ->title('Tidak ada area yang masuk cakupan')
                ->body('Putaran ini terbuat, tetapi daftarnya kosong karena cakupannya tidak menghasilkan satu pun area yang masih dipakai. Ubah cakupannya lalu tekan Rebuild Area List, atau batalkan putaran ini.')
                ->persistent()
                ->send();

            return;
        }

        Notification::make()
            ->success()
            ->title($putaran->code.' siap diperiksa')
            ->body($jumlah.' area masuk ke lembar pemeriksaan.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
