<?php

namespace App\Filament\Resources\ServiceRequests\Pages;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceRequests extends ListRecords
{
    protected static string $resource = ServiceRequestResource::class;

    /**
     * Ringkasannya dihitung dari daftar yang memang boleh dilihat orang ini, bukan dari
     * seluruh tabel. Karyawan yang membaca "12 menunggu tim GA" padahal ia hanya boleh
     * melihat dua tiketnya sendiri akan mengira layarnya rusak.
     */
    public function getSubheading(): ?string
    {
        $dasar = ServiceRequestResource::getEloquentQuery();

        $menungguPersetujuan = (clone $dasar)->menungguPersetujuan()->count();
        $menungguGa = (clone $dasar)->menungguGa()->count();
        $lewatSla = (clone $dasar)->lewatSla()->count();

        $bagian = [];

        if ($menungguPersetujuan > 0) {
            $bagian[] = $menungguPersetujuan.' menunggu persetujuan atasan';
        }

        if ($menungguGa > 0) {
            $bagian[] = $menungguGa.' menunggu diterima tim GA';
        }

        if ($lewatSla > 0) {
            $bagian[] = $lewatSla.' sudah lewat batas waktu';
        }

        if ($bagian === []) {
            return 'Tidak ada permintaan yang menunggu tindakan.';
        }

        return ucfirst(implode(', ', $bagian)).'. Yang belum selesai selalu ada di urutan teratas.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ajukan permintaan'),
        ];
    }
}
