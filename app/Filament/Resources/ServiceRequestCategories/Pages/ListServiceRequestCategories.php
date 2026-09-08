<?php

namespace App\Filament\Resources\ServiceRequestCategories\Pages;

use App\Filament\Resources\ServiceRequestCategories\ServiceRequestCategoryResource;
use App\Models\ServiceRequestCategory;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceRequestCategories extends ListRecords
{
    protected static string $resource = ServiceRequestCategoryResource::class;

    public function getSubheading(): ?string
    {
        $tanpaTarget = ServiceRequestCategory::query()
            ->where('is_active', true)
            ->whereNull('sla_hours')
            ->count();

        if ($tanpaTarget === 0) {
            return 'Pilihan yang dilihat karyawan saat melapor, beserta prioritas bawaan dan target waktu penyelesaiannya.';
        }

        return $tanpaTarget.' jenis belum punya target waktu. Selama kosong, tiket jenis itu tidak pernah dihitung terlambat.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Add Type'),
        ];
    }
}
