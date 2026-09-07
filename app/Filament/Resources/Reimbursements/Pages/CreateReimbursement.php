<?php

namespace App\Filament\Resources\Reimbursements\Pages;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateReimbursement extends CreateRecord
{
    protected static string $resource = ReimbursementResource::class;

    public function getSubheading(): ?string
    {
        return 'Yang dicatat di sini baru kepala pengajuannya. Struknya ditambahkan satu per satu di langkah berikutnya, dan jumlah struk itulah yang menjadi nilai pengajuannya.';
    }

    /**
     * Setelah disimpan, langsung ke halaman lihat, bukan kembali ke daftar. Pengajuan yang
     * baru dibuat belum punya satu pun struk, dan halaman lihat adalah satu satunya tempat
     * struk itu bisa ditambahkan.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
