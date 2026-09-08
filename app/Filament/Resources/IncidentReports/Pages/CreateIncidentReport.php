<?php

namespace App\Filament\Resources\IncidentReports\Pages;

use App\Filament\Resources\IncidentReports\IncidentReportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncidentReport extends CreateRecord
{
    protected static string $resource = IncidentReportResource::class;

    public function getSubheading(): ?string
    {
        return 'Catat kejadiannya selagi masih segar diingat. Meneruskannya menjadi tiket perbaikan adalah langkah berikutnya, bukan sekarang.';
    }

    /**
     * Setelah disimpan, langsung ke halaman lihat, bukan kembali ke daftar. Yang paling
     * sering dilakukan orang tepat setelah mencatat insiden adalah memutuskan apakah ia
     * perlu diteruskan menjadi tiket, dan tombol itu hanya ada di halaman lihat.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
