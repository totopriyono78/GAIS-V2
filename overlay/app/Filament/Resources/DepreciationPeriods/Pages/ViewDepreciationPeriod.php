<?php

namespace App\Filament\Resources\DepreciationPeriods\Pages;

use App\Filament\Resources\DepreciationPeriods\DepreciationPeriodResource;
use App\Models\DepreciationPeriod;
use Filament\Resources\Pages\ViewRecord;

class ViewDepreciationPeriod extends ViewRecord
{
    protected static string $resource = DepreciationPeriodResource::class;

    public function getTitle(): string
    {
        return 'Penyusutan '.$this->getRecord()->label();
    }

    public function getHeading(): string
    {
        return 'Penyusutan '.$this->getRecord()->label();
    }

    public function getSubheading(): ?string
    {
        /** @var DepreciationPeriod $periode */
        $periode = $this->getRecord();

        if ($periode->canBeReopened()) {
            return 'Ini periode terakhir yang ditutup, jadi masih bisa dibuka kembali kalau ada yang perlu diperbaiki.';
        }

        return 'Sudah ada periode sesudahnya yang ditutup, jadi periode ini tidak bisa dibuka kembali. Buka periode terbaru lebih dulu.';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Halaman ini menghilang bersama periodenya, jadi tombolnya wajib membawa
            // orang kembali ke daftar. Tanpa itu mereka tertinggal di halaman yang
            // sudah tidak punya isi dan menemukan 404 pada klik berikutnya.
            DepreciationPeriodResource::bukaKembaliAction(
                iconOnly: false,
                setelahnya: DepreciationPeriodResource::getUrl('index'),
            ),
        ];
    }
}
