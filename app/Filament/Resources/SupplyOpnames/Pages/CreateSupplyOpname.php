<?php

namespace App\Filament\Resources\SupplyOpnames\Pages;

use App\Filament\Resources\SupplyOpnames\SupplyOpnameResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplyOpname extends CreateRecord
{
    protected static string $resource = SupplyOpnameResource::class;

    public function getSubheading(): ?string
    {
        return 'Yang dicatat di sini baru kepala sesinya, yaitu nama dan cakupannya. Daftar barang yang akan dihitung disusun sendiri oleh aplikasi dari cakupan itu di langkah berikutnya, bukan diketik satu per satu.';
    }

    /**
     * Setelah disimpan, langsung ke halaman lihat, bukan kembali ke daftar. Sesi yang baru
     * dibuat belum punya daftar target, dan halaman lihat adalah satu satunya tempat daftar
     * itu bisa disusun.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
