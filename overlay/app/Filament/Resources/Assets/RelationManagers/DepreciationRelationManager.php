<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Models\DepreciationEntry;
use App\Support\Periode;
use App\Support\Rupiah;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Riwayat penyusutan bulanan satu aset.
 *
 * Ini yang menjawab pertanyaan "kenapa nilai buku aset ini segini", dan menjawabnya
 * bulan per bulan sehingga bisa dicocokkan dengan kertas kerja siapa pun. Isinya hanya
 * periode yang sudah ditutup, karena hanya periode tertutup yang angkanya resmi.
 */
class DepreciationRelationManager extends RelationManager
{
    protected static string $relationship = 'depreciationEntries';

    protected static ?string $title = 'Riwayat penyusutan';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period')
                    ->label('Periode')
                    ->state(fn (DepreciationEntry $record): string => Periode::label($record->period))
                    ->description(fn (DepreciationEntry $record): ?string => $record->isCatchUp()
                        ? 'Susulan '.$record->coverageLabel()
                        : null)
                    ->sortable(),
                TextColumn::make('expense')
                    ->label('Beban')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->expense))
                    ->color(fn (DepreciationEntry $record): string => $record->isCatchUp() ? 'warning' : 'gray')
                    ->alignEnd(),
                TextColumn::make('accumulated_after')
                    ->label('Akumulasi')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->accumulated_after))
                    ->alignEnd(),
                TextColumn::make('book_value_after')
                    ->label('Nilai buku')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->book_value_after))
                    ->alignEnd(),
            ])
            ->defaultSort('period', 'desc')
            ->paginated([12, 24, 60])
            ->defaultPaginationPageOption(12)
            ->emptyStateHeading('Belum ada penyusutan yang tercatat')
            ->emptyStateDescription('Baris di sini muncul setelah periode bulanan ditutup di menu Penyusutan aset. Aset sewaan, aset berkategori tidak disusutkan, dan aset yang umur ekonomisnya sudah habis memang tidak akan pernah punya baris di sini.');
    }

    // Riwayat penyusutan lahir dari penutupan periode. Mengubah satu baris di sini
    // akan membuat total periodenya tidak cocok lagi dengan isinya.
    public function canCreate(): bool
    {
        return false;
    }
}
