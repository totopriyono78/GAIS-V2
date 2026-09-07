<?php

namespace App\Filament\Resources\Reimbursements\Pages;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use App\Models\Reimbursement;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReimbursement extends ViewRecord
{
    protected static string $resource = ReimbursementResource::class;

    /**
     * Subjudul menjawab satu pertanyaan yang dibawa ketiga pembaca layar ini: sedang ada di
     * meja siapa, dan apa yang perlu terjadi berikutnya.
     */
    public function getSubheading(): ?string
    {
        /** @var Reimbursement $pengajuan */
        $pengajuan = $this->getRecord();

        if ($pengajuan->status === 'draft') {
            $alasan = $pengajuan->alasanBelumBisaDiajukan();

            if ($alasan !== null) {
                return $alasan;
            }

            $tanpaBukti = $pengajuan->strukTanpaBukti();

            return 'Draf senilai '.$pengajuan->totalLabel().'. Belum masuk hitungan anggaran mana pun sampai diajukan dan diperiksa.'
                .($tanpaBukti > 0 ? ' '.$tanpaBukti.' struk belum ada fotonya.' : '');
        }

        return match ($pengajuan->status) {
            'diajukan' => 'Menunggu persetujuan '.($pengajuan->approver?->full_name ?? 'kepala departemen').'. Nilainya belum terhitung sebagai realisasi anggaran.',
            'diperiksa' => 'Menunggu tim GA memeriksa struknya. Nilainya belum terhitung sebagai realisasi anggaran.',
            'disetujui' => 'Sudah terhitung sebagai realisasi anggaran, dan menunggu ditransfer ke pemohon.',
            'dibayar' => 'Selesai. Sudah diganti dan terhitung sebagai realisasi anggaran.',
            'ditolak' => 'Ditolak '.($pengajuan->rejected_stage === 'ga' ? 'tim GA' : 'atasan').' dan tidak terhitung di anggaran. Kembalikan ke draf untuk memperbaiki struknya.',
            default => 'Dibatalkan dan tidak terhitung di anggaran mana pun.',
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            ReimbursementResource::ajukanAction(iconOnly: false),
            ReimbursementResource::setujuiAction(iconOnly: false),
            ReimbursementResource::periksaAction(iconOnly: false),
            ReimbursementResource::bayarAction(iconOnly: false),
            ReimbursementResource::tolakAction(iconOnly: false),
            ReimbursementResource::perbaikiAction(iconOnly: false),
            ReimbursementResource::batalkanAction(iconOnly: false),
            EditAction::make()
                ->visible(fn (): bool => ReimbursementResource::canEdit($this->getRecord())),
        ];
    }
}
