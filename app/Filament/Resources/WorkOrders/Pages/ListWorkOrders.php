<?php

namespace App\Filament\Resources\WorkOrders\Pages;

use App\Filament\Resources\WorkOrders\WorkOrderResource;
use App\Models\WorkOrder;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWorkOrders extends ListRecords
{
    protected static string $resource = WorkOrderResource::class;

    public function getSubheading(): ?string
    {
        $terbuka = WorkOrder::query()->terbuka()->count();

        if ($terbuka === 0) {
            return 'Tidak ada pekerjaan yang menggantung.';
        }

        $mendesak = WorkOrder::query()->terbuka()->where('priority', 'mendesak')->count();

        return $terbuka.' pekerjaan belum selesai'
            .($mendesak > 0 ? ', '.$mendesak.' di antaranya mendesak' : '')
            .'. Yang belum selesai selalu ada di urutan teratas.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Catat kerusakan'),
        ];
    }
}
