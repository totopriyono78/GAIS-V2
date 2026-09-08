<?php

namespace App\Filament\Resources\Reimbursements\Pages;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use App\Models\Reimbursement;
use App\Models\ReimbursementLine;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReimbursements extends ListRecords
{
    protected static string $resource = ReimbursementResource::class;

    /**
     * Subjudul menyebut berapa yang menunggu di tiap meja, dan berapa uang karyawan yang
     * sudah disetujui tetapi belum kembali. Angka terakhir itu yang paling cepat
     * dikeluhkan orang, dan menyembunyikannya tidak membuatnya hilang.
     */
    public function getSubheading(): ?string
    {
        $dasar = ReimbursementResource::getEloquentQuery();

        if (! (clone $dasar)->exists()) {
            return 'Belum ada pengajuan yang tercatat. Setelah atasan menyetujui dan tim GA memeriksa, nilainya masuk sendiri ke realisasi anggaran departemen yang dibebani.';
        }

        $menungguAtasan = (clone $dasar)->menungguAtasan()->count();
        $menungguGa = (clone $dasar)->menungguGa()->count();
        $menungguTransfer = (clone $dasar)->menungguPembayaran()->count();

        $bagian = [];

        if ($menungguAtasan > 0) {
            $bagian[] = $menungguAtasan.' menunggu persetujuan atasan';
        }

        if ($menungguGa > 0) {
            $bagian[] = $menungguGa.' menunggu diperiksa tim GA';
        }

        if ($menungguTransfer > 0) {
            $nilai = ReimbursementLine::query()
                ->join('reimbursements', 'reimbursements.id', '=', 'reimbursement_lines.reimbursement_id')
                ->whereIn('reimbursements.id', (clone $dasar)->menungguPembayaran()->select('reimbursements.id'))
                ->sum('reimbursement_lines.amount');

            $bagian[] = $menungguTransfer.' sudah disetujui dan menunggu ditransfer, senilai '.Rupiah::penuh((float) $nilai);
        }

        if ($bagian === []) {
            return 'Tidak ada pengajuan yang menunggu tindakan. Yang paling lama menunggu selalu ada di urutan teratas.';
        }

        return ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Reimbursement'),
        ];
    }
}
