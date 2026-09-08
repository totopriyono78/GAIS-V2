<?php

namespace App\Filament\Resources\WorkOrders\Pages;

use App\Filament\Resources\WorkOrders\WorkOrderResource;
use App\Models\WorkOrder;
use Filament\Resources\Pages\EditRecord;

class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;

    public function getTitle(): string
    {
        return 'Work Order '.$this->getRecord()->code;
    }

    public function getHeading(): string
    {
        return 'Work Order '.$this->getRecord()->code;
    }

    public function getSubheading(): ?string
    {
        /** @var WorkOrder $wo */
        $wo = $this->getRecord();

        return $wo->isOpen()
            ? 'Masih '.strtolower($wo->statusLabel()).'. Hasil pekerjaan diisi lewat tombol Selesaikan di daftar perintah kerja.'
            : $wo->statusLabel().'. Isinya sudah tidak berubah lagi.';
    }

    protected function getHeaderActions(): array
    {
        return [
            WorkOrderResource::selesaikanAction(iconOnly: false),
        ];
    }
}
