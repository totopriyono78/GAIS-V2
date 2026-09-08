<?php

namespace App\Filament\Resources\SupplyRequests\Pages;

use App\Filament\Resources\SupplyRequests\SupplyRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSupplyRequest extends CreateRecord
{
    protected static string $resource = SupplyRequestResource::class;

    public function getSubheading(): ?string
    {
        return 'Yang dicatat di sini baru kepala permintaannya. Barangnya ditambahkan satu per satu di langkah berikutnya, dan permintaan tanpa barang tidak bisa diajukan.';
    }

    /**
     * Pemohon dipaksa kembali ke diri sendiri kalau pengguna ini tidak memegang izin
     * Ajukan atas nama orang lain.
     *
     * Pilihan di formulir sudah dikunci, tetapi kunci di layar hanya menghalangi orang yang
     * memakai layar. Permintaan Livewire bisa dikarang, dan tanpa penjagaan di sini seseorang
     * masih bisa mengirim nomor karyawan lain lalu membebankan pemakaian ATK ke departemen
     * yang bukan departemennya.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::user()?->hasPermission('supply_requests.request_for_others')) {
            return $data;
        }

        $karyawan = Auth::user()?->employee;

        if ($karyawan !== null) {
            $data['employee_id'] = $karyawan->getKey();
        }

        return $data;
    }

    /**
     * Setelah disimpan, langsung ke halaman lihat, bukan kembali ke daftar. Permintaan yang
     * baru dibuat belum punya satu pun barang, dan halaman lihat adalah satu satunya tempat
     * barang itu bisa ditambahkan.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
