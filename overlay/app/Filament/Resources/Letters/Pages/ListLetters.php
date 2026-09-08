<?php

namespace App\Filament\Resources\Letters\Pages;

use App\Filament\Resources\Letters\LetterResource;
use App\Models\Letter;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLetters extends ListRecords
{
    protected static string $resource = LetterResource::class;

    public function getSubheading(): ?string
    {
        $menunggu = Letter::query()->menungguDiserahkan()->count();

        if ($menunggu === 0) {
            return 'Buku agenda surat masuk dan surat keluar. Tidak ada surat masuk yang masih menunggu diserahkan.';
        }

        return 'Buku agenda surat masuk dan surat keluar. '.$menunggu
            .' surat masuk sudah tahu tujuannya tetapi belum sampai ke mejanya.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Record Letter'),
        ];
    }
}
