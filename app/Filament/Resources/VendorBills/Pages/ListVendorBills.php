<?php

namespace App\Filament\Resources\VendorBills\Pages;

use App\Filament\Resources\VendorBills\VendorBillResource;
use App\Models\VendorBill;
use App\Models\VendorBillLine;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorBills extends ListRecords
{
    protected static string $resource = VendorBillResource::class;

    /**
     * Subjudul menjawab pertanyaan yang dibawa orang ke layar ini sebelum ia sempat
     * membaca satu baris pun: ada berapa yang menunggu tanda tangan saya, dan ada yang
     * sudah telat atau tidak. Nilai yang sudah disetujui tetapi belum dibayar ikut
     * disebut, karena itulah uang yang sudah pasti keluar dari kas bulan ini.
     */
    public function getSubheading(): ?string
    {
        $menungguPersetujuan = VendorBill::query()->menungguPersetujuan()->count();
        $menungguPembayaran = VendorBill::query()->menungguPembayaran()->count();
        $terlambat = VendorBill::query()->terlambat()->count();

        if (! VendorBill::query()->exists()) {
            return 'Belum ada tagihan yang dicatat. Setelah tagihan disetujui, nilainya masuk sendiri ke realisasi anggaran departemen yang dibebani.';
        }

        $bagian = [];

        if ($menungguPersetujuan > 0) {
            $bagian[] = $menungguPersetujuan.' menunggu persetujuan';
        }

        if ($menungguPembayaran > 0) {
            $nilai = VendorBillLine::query()
                ->join('vendor_bills', 'vendor_bills.id', '=', 'vendor_bill_lines.vendor_bill_id')
                ->where('vendor_bills.status', 'disetujui')
                ->sum('vendor_bill_lines.amount');

            $bagian[] = $menungguPembayaran.' sudah disetujui dan menunggu dibayar, senilai '.Rupiah::penuh((float) $nilai);
        }

        if ($terlambat > 0) {
            $bagian[] = $terlambat.' sudah lewat jatuh tempo';
        }

        if ($bagian === []) {
            return 'Tidak ada tagihan yang menunggu tindakan. Yang paling dekat jatuh tempo selalu ada di urutan teratas.';
        }

        return ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Record Bill'),
        ];
    }
}
