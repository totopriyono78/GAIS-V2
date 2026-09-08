<?php

namespace App\Filament\Resources\SupplyRequests\Pages;

use App\Filament\Resources\SupplyRequests\SupplyRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplyRequests extends ListRecords
{
    protected static string $resource = SupplyRequestResource::class;

    /**
     * Subjudul menyebut berapa yang menunggu di tiap meja, ditambah berapa yang sudah
     * disetujui tetapi stoknya belum cukup.
     *
     * Angka terakhir itu yang paling sering menjadi keluhan di gudang ATK: permintaan yang
     * disetujui bulan lalu dan tidak pernah bisa diserahkan karena barangnya memang tidak
     * pernah dipesan. Menyebutkannya di sini membuatnya terlihat sebelum ditanyakan orang.
     */
    public function getSubheading(): ?string
    {
        $dasar = SupplyRequestResource::getEloquentQuery();

        if (! (clone $dasar)->exists()) {
            return 'Belum ada permintaan yang tercatat. Setelah atasan menyetujui dan tim GA menyerahkan barangnya, stok berkurang sendiri dan pemakaiannya tercatat atas nama departemen pemohon.';
        }

        $menungguAtasan = (clone $dasar)->menungguAtasan()->count();
        $menungguSerah = (clone $dasar)->menungguPenyerahan()->count();

        $bagian = [];

        if ($menungguAtasan > 0) {
            $bagian[] = $menungguAtasan.' menunggu persetujuan atasan';
        }

        if ($menungguSerah > 0) {
            $bagian[] = $menungguSerah.' sudah disetujui dan menunggu diserahkan tim GA';

            // Dihitung dari barisnya, bukan dari kolom apa pun. Stok memang tidak pernah
            // disimpan sebagai kolom sejak kiriman C, jadi kekurangannya juga tidak bisa
            // dibaca tanpa menjumlahkan mutasinya lebih dulu.
            $stokKurang = (clone $dasar)->menungguPenyerahan()->get()
                ->filter(fn ($permintaan): bool => $permintaan->kekuranganStok() !== [])
                ->count();

            if ($stokKurang > 0) {
                $bagian[] = $stokKurang.' di antaranya belum bisa diserahkan penuh karena stoknya kurang';
            }
        }

        if ($bagian === []) {
            return 'Tidak ada permintaan yang menunggu tindakan. Yang paling lama menunggu selalu ada di urutan teratas.';
        }

        return ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New Supply Request'),
        ];
    }
}
