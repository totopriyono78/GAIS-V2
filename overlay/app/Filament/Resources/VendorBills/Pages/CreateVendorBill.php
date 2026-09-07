<?php

namespace App\Filament\Resources\VendorBills\Pages;

use App\Filament\Resources\VendorBills\VendorBillResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVendorBill extends CreateRecord
{
    protected static string $resource = VendorBillResource::class;

    public function getSubheading(): ?string
    {
        return 'Yang dicatat di sini baru kepala fakturnya. Rincian pembebanan ke departemen dan kategori biaya diisi di langkah berikutnya, dan jumlah rincian itulah yang menjadi nilai tagihannya.';
    }

    /**
     * Setelah disimpan, langsung ke halaman lihat, bukan kembali ke daftar. Tagihan yang
     * baru dibuat belum punya satu pun baris pembebanan, dan halaman lihat adalah satu
     * satunya tempat baris itu bisa ditambahkan.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
