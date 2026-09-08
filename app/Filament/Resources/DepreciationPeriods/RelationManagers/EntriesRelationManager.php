<?php

namespace App\Filament\Resources\DepreciationPeriods\RelationManagers;

use App\Models\AssetCategory;
use App\Models\DepreciationEntry;
use App\Support\Rupiah;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Beban penyusutan per aset di dalam satu periode.
 *
 * Ini daftar yang akan dicocokkan tim finance dengan hitungan mereka sendiri, jadi
 * yang ditampilkan bukan hanya bebannya melainkan juga dasar hitungannya: metode,
 * umur ekonomis, dan nilai perolehan yang dipakai saat itu. Ketiganya dibaca dari
 * entrinya, bukan dari asetnya, karena data aset bisa berubah setelah periode ditutup.
 */
class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    protected static ?string $title = 'Charge per Asset';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.code')
                    ->label('Kode aset')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->description(fn (DepreciationEntry $record): ?string => $record->asset?->category?->pickerLabel())
                    ->searchable()
                    ->wrap(),
                TextColumn::make('expense')
                    ->label('Beban')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->expense))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('months_covered')
                    ->label('Cakupan')
                    ->state(fn (DepreciationEntry $record): string => $record->isCatchUp()
                        ? $record->months_covered.' bulan'
                        : '1 bulan')
                    ->description(fn (DepreciationEntry $record): ?string => $record->isCatchUp()
                        ? $record->coverageLabel()
                        : null)
                    ->color(fn (DepreciationEntry $record): string => $record->isCatchUp() ? 'warning' : 'gray')
                    ->alignEnd(),
                TextColumn::make('accumulated_after')
                    ->label('Akumulasi')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->accumulated_after))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('book_value_after')
                    ->label('Nilai buku')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->book_value_after))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Metode')
                    ->badge()
                    ->color('gray')
                    ->state(fn (DepreciationEntry $record): string => $record->methodLabel())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('useful_life_months')
                    ->label('Umur')
                    ->state(fn (DepreciationEntry $record): string => $record->useful_life_months.' bulan')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('acquisition_cost')
                    ->label('Nilai perolehan')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->acquisition_cost))
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('residual_value')
                    ->label('Nilai sisa')
                    ->state(fn (DepreciationEntry $record): string => Rupiah::penuh((float) $record->residual_value))
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expense', 'desc')
            ->filters([
                SelectFilter::make('method')
                    ->label('Metode')
                    ->options(AssetCategory::DEPRECIATION_METHODS),
                Filter::make('susulan')
                    ->label('Hanya beban susulan')
                    ->query(fn (Builder $query): Builder => $query->where('months_covered', '>', 1)),
            ])
            ->emptyStateHeading('Periode ini tidak membebani aset mana pun')
            ->emptyStateDescription('Tidak ada aset yang memenuhi syarat disusutkan pada bulan ini. Aset sewaan, aset berkategori tidak disusutkan, dan aset yang umur ekonomisnya sudah habis memang tidak muncul di sini.');
    }

    // Entri lahir dari penutupan periode dan mati bersamanya. Tidak ada yang boleh
    // menambah, mengubah, atau menghapus satu baris sendirian, karena satu baris yang
    // diubah akan membuat total periodenya tidak cocok lagi dengan isinya.
    public function canCreate(): bool
    {
        return false;
    }
}
