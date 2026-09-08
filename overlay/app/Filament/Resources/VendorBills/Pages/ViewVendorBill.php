<?php

namespace App\Filament\Resources\VendorBills\Pages;

use App\Filament\Resources\VendorBills\VendorBillResource;
use App\Models\VendorBill;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewVendorBill extends ViewRecord
{
    protected static string $resource = VendorBillResource::class;

    /**
     * Menggambar ulang halaman saat baris rincian di relation manager berubah.
     *
     * Metodenya sengaja kosong. Livewire menggambar ulang komponen setiap kali ia menangani
     * sebuah peristiwa, jadi keberadaan pendengar inilah yang menyegarkan angka turunan di
     * infolist, bukan isi metodenya.
     */
    #[On('rincian-berubah')]
    public function rincianBerubah(): void {}

    public function getSubheading(): ?string
    {
        /** @var VendorBill $tagihan */
        $tagihan = $this->getRecord();

        $alasan = $tagihan->status === 'draft' ? $tagihan->alasanBelumBisaDiajukan() : null;

        if ($alasan !== null) {
            return $alasan;
        }

        return match ($tagihan->status) {
            'draft' => 'Draf senilai '.$tagihan->totalLabel().'. Belum masuk hitungan anggaran mana pun sampai diajukan dan disetujui.',
            'diajukan' => 'Menunggu persetujuan. Nilainya tampil di layar anggaran sebagai angka yang menunggu, belum sebagai realisasi.',
            'disetujui' => 'Sudah terhitung sebagai realisasi anggaran tahun '.$tagihan->tahunAnggaran().'. '.$tagihan->jatuhTempoLabel().' sampai jatuh tempo pembayarannya.',
            'dibayar' => 'Selesai. Terhitung sebagai realisasi anggaran tahun '.$tagihan->tahunAnggaran().'.',
            'ditolak' => 'Ditolak dan tidak terhitung di anggaran. Kembalikan ke draf untuk memperbaiki rinciannya.',
            default => 'Dibatalkan dan tidak terhitung di anggaran mana pun.',
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            VendorBillResource::ajukanAction(iconOnly: false),
            VendorBillResource::setujuiAction(iconOnly: false),
            VendorBillResource::tolakAction(iconOnly: false),
            VendorBillResource::bayarAction(iconOnly: false),
            VendorBillResource::perbaikiAction(iconOnly: false),
            VendorBillResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => VendorBillResource::canEdit($this->getRecord())),
        ];
    }
}
