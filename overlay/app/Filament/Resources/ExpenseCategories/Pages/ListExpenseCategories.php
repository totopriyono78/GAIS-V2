<?php

namespace App\Filament\Resources\ExpenseCategories\Pages;

use App\Filament\Resources\ExpenseCategories\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenseCategories extends ListRecords
{
    protected static string $resource = ExpenseCategoryResource::class;

    public function getSubheading(): ?string
    {
        $belumDipetakan = ExpenseCategory::query()
            ->where('is_active', true)
            ->whereNull('account_code')
            ->count();

        if ($belumDipetakan === 0) {
            return 'Tempat pagu anggaran ditetapkan dan realisasi dijumlahkan.';
        }

        return $belumDipetakan.' kategori belum punya nomor akun. Itu belum menghalangi apa pun sekarang, tetapi dibutuhkan saat jurnal otomatis dibangun.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah kategori')
                ->modalHeading('Tambah kategori biaya'),
        ];
    }
}
