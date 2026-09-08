<?php

namespace App\Filament\Resources\DepreciationPeriods\Pages;

use App\Filament\Resources\DepreciationPeriods\DepreciationPeriodResource;
use App\Services\PenyusutanAset;
use App\Support\Periode;
use Filament\Resources\Pages\ListRecords;

class ListDepreciationPeriods extends ListRecords
{
    protected static string $resource = DepreciationPeriodResource::class;

    public function getHeading(): string
    {
        return 'Depreciation';
    }

    /**
     * Subjudul menyebut periode mana yang berikutnya, supaya orang tidak perlu membuka
     * kotak penutupan hanya untuk tahu bulan apa yang sedang menunggu.
     */
    public function getSubheading(): ?string
    {
        $periode = app(PenyusutanAset::class)->periodeBerikutnya();

        if ($periode === null) {
            return 'Seluruh bulan sampai bulan berjalan sudah ditutup. Periode berikutnya bisa ditutup setelah bulannya berakhir.';
        }

        return 'Periode yang menunggu ditutup: '.Periode::label($periode).'. Penyusutan dihitung berurutan, satu bulan setelah bulan sebelumnya.';
    }

    protected function getHeaderActions(): array
    {
        return [
            DepreciationPeriodResource::tutupAction(),
        ];
    }
}
