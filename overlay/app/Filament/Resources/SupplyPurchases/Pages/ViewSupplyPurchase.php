<?php

namespace App\Filament\Resources\SupplyPurchases\Pages;

use App\Filament\Resources\SupplyPurchases\SupplyPurchaseResource;
use App\Models\SupplyPurchase;
use App\Support\Rupiah;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewSupplyPurchase extends ViewRecord
{
    protected static string $resource = SupplyPurchaseResource::class;

    /**
     * Menggambar ulang halaman saat baris rincian di relation manager berubah.
     *
     * Metodenya sengaja kosong. Livewire menggambar ulang komponen setiap kali ia menangani
     * sebuah peristiwa, jadi keberadaan pendengar inilah yang menyegarkan angka turunan di
     * infolist, bukan isi metodenya.
     */
    #[On('rincian-berubah')]
    public function rincianBerubah(): void {}

    /**
     * Subjudul menjawab pertanyaan yang berbeda pada tiap tahap, dan pada tahap menunggu
     * barang ia menyebut angka yang paling sering ditanyakan: berapa yang sudah datang dan
     * berapa jenis yang belum.
     */
    public function getSubheading(): ?string
    {
        /** @var SupplyPurchase $pembelian */
        $pembelian = $this->getRecord();

        if ($pembelian->status === 'draft') {
            $alasan = $pembelian->alasanBelumBisaDiajukan();

            if ($alasan !== null) {
                return $alasan;
            }

            return $pembelian->kind === 'langsung'
                ? 'Draf senilai '.$pembelian->totalLabel().'. Stok belum bertambah sampai tombol Record and Receive ditekan.'
                : 'Draf senilai '.$pembelian->totalLabel().'. Belum dikirim ke rekanan, dan stok belum tersentuh.';
        }

        return match ($pembelian->status) {
            'diajukan' => 'Menunggu manajer GA menyetujui sebelum pesanan dikirim ke '
                .$pembelian->pemasokLabel().'. Stok belum tersentuh.',
            'disetujui' => 'Sudah disetujui dan barangnya ditunggu. '
                .$pembelian->janjiLabel().'. Stok bertambah saat kirimannya dicatat lewat tombol Receive Goods.',
            'diterima_sebagian' => 'Sudah datang senilai '.Rupiah::penuh($pembelian->totalDiterima())
                .' dari '.$pembelian->totalLabel().'. '.$pembelian->barisBelumLengkap()
                .' jenis barang masih ditunggu, dan sisanya terbaca di daftar di bawah.',
            'selesai' => filled($pembelian->closing_reason)
                ? 'Ditutup sebelum lengkap. Yang sempat datang senilai '.Rupiah::penuh($pembelian->totalDiterima())
                    .', dan sisanya tercatat tidak jadi dikirim.'
                : 'Selesai. Seluruh barangnya sudah datang, senilai '.Rupiah::penuh($pembelian->totalDiterima()).'.',
            'ditolak' => 'Ditolak manajer GA dan tidak dikirim ke rekanan. Kembalikan ke draf untuk memperbaiki daftarnya.',
            default => 'Dibatalkan. Stok tidak tersentuh.',
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            SupplyPurchaseResource::ajukanAction(iconOnly: false),
            SupplyPurchaseResource::catatLangsungAction(iconOnly: false),
            SupplyPurchaseResource::setujuiAction(iconOnly: false),
            SupplyPurchaseResource::terimaAction(iconOnly: false),
            SupplyPurchaseResource::tutupAction(iconOnly: false),
            SupplyPurchaseResource::tolakAction(iconOnly: false),
            SupplyPurchaseResource::perbaikiAction(iconOnly: false),
            SupplyPurchaseResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => SupplyPurchaseResource::canEdit($this->getRecord())),
        ];
    }
}
