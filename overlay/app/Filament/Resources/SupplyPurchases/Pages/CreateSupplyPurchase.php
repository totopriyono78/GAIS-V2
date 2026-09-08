<?php

namespace App\Filament\Resources\SupplyPurchases\Pages;

use App\Filament\Resources\SupplyPurchases\SupplyPurchaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplyPurchase extends CreateRecord
{
    protected static string $resource = SupplyPurchaseResource::class;

    public function getSubheading(): ?string
    {
        return 'Yang dicatat di sini baru kepala pembeliannya. Barangnya ditambahkan satu per satu di langkah berikutnya, dan pembelian tanpa barang tidak bisa diajukan maupun dicatat masuk gudang.';
    }

    /**
     * Membersihkan kolom pemasok yang tidak dipakai jenis pembelian ini.
     *
     * Kolom yang tersembunyi tetap terkirim dengan nilai lamanya kalau orang sempat mengisi
     * satu jenis lalu berganti ke jenis yang lain sebelum menyimpan. Tanpa pembersihan ini,
     * sebuah pesanan resmi bisa tersimpan membawa nama toko yang tidak ada hubungannya, dan
     * layar lalu menampilkan dua pemasok yang berbeda untuk satu pembelian.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['kind'] ?? 'pesanan') === 'pesanan') {
            $data['supplier_name'] = null;

            return $data;
        }

        $data['vendor_id'] = null;
        $data['expected_date'] = null;

        return $data;
    }

    /**
     * Setelah disimpan, langsung ke halaman lihat, bukan kembali ke daftar. Pembelian yang
     * baru dibuat belum punya satu pun barang, dan halaman lihat adalah satu satunya tempat
     * barang itu bisa ditambahkan.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
