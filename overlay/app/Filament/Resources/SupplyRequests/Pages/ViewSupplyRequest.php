<?php

namespace App\Filament\Resources\SupplyRequests\Pages;

use App\Filament\Resources\SupplyRequests\SupplyRequestResource;
use App\Models\SupplyRequest;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplyRequest extends ViewRecord
{
    protected static string $resource = SupplyRequestResource::class;

    /**
     * Subjudul menjawab satu pertanyaan yang dibawa ketiga pembaca layar ini: sedang ada di
     * meja siapa, dan apa yang perlu terjadi berikutnya. Untuk permintaan yang menunggu
     * diserahkan, ia juga menyebut barang mana yang stoknya kurang, karena itulah satu
     * satunya hal yang menghalangi tombol Issue Supplies bekerja.
     */
    public function getSubheading(): ?string
    {
        /** @var SupplyRequest $permintaan */
        $permintaan = $this->getRecord();

        if ($permintaan->status === 'draft') {
            $alasan = $permintaan->alasanBelumBisaDiajukan();

            if ($alasan !== null) {
                return $alasan;
            }

            $kurang = $permintaan->kekuranganStok();

            return 'Draf berisi '.$permintaan->jenisLabel().'. Stok belum tersentuh, dan baru berkurang saat barangnya diserahkan.'
                .($kurang !== [] ? ' Hari ini '.count($kurang).' barang stoknya belum cukup, dan itu tidak menghalangi pengajuan.' : '');
        }

        if ($permintaan->status === 'disetujui') {
            $kurang = $permintaan->kekuranganStok();

            return $kurang === []
                ? 'Menunggu tim GA menyerahkan barangnya. Stok seluruh barangnya cukup, jadi penyerahan bisa dilakukan sekarang.'
                : 'Menunggu tim GA. Belum bisa diserahkan penuh: '.implode('. ', $kurang).'.';
        }

        return match ($permintaan->status) {
            'diajukan' => 'Menunggu persetujuan '.($permintaan->approver?->full_name ?? 'kepala departemen').'. Stok belum tersentuh.',
            'diserahkan' => 'Selesai. Barangnya sudah diserahkan dan stoknya sudah berkurang.'
                .($permintaan->barisKurang() > 0
                    ? ' '.$permintaan->barisKurang().' barang diserahkan kurang dari yang diminta, dan selisihnya terbaca di daftar di bawah.'
                    : ''),
            'ditolak' => 'Ditolak '.($permintaan->rejected_stage === 'ga' ? 'tim GA' : 'atasan').'. Stok tidak tersentuh. Kembalikan ke draf untuk memperbaiki daftarnya.',
            default => 'Dibatalkan. Stok tidak tersentuh.',
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            SupplyRequestResource::ajukanAction(iconOnly: false),
            SupplyRequestResource::setujuiAction(iconOnly: false),
            SupplyRequestResource::serahkanAction(iconOnly: false),
            SupplyRequestResource::tolakAction(iconOnly: false),
            SupplyRequestResource::perbaikiAction(iconOnly: false),
            SupplyRequestResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => SupplyRequestResource::canEdit($this->getRecord())),
        ];
    }
}
