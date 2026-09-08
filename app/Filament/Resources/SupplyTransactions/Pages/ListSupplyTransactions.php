<?php

namespace App\Filament\Resources\SupplyTransactions\Pages;

use App\Filament\Resources\SupplyTransactions\SupplyTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSupplyTransactions extends ListRecords
{
    protected static string $resource = SupplyTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Record Movement'),
        ];
    }
}
