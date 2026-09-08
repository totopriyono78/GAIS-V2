<?php

namespace App\Filament\Resources\WorkOrders\Pages;

use App\Filament\Resources\WorkOrders\WorkOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkOrder extends CreateRecord
{
    protected static string $resource = WorkOrderResource::class;

    public function getTitle(): string
    {
        return 'Log Issue';
    }

    public function getHeading(): string
    {
        return 'Log Issue';
    }

    public function getSubheading(): ?string
    {
        return 'Untuk pekerjaan terjadwal, buat perintah kerjanya dari menu Jadwal pemeliharaan supaya jatuh tempo jadwalnya ikut bergerak setelah selesai.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
