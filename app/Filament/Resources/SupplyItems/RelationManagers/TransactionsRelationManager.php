<?php

namespace App\Filament\Resources\SupplyItems\RelationManagers;

use App\Models\SupplyItem;
use App\Models\SupplyTransaction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Attributes\On;

/**
 * Buku stok satu barang. Hanya dibaca di sini, karena mutasi dicatat lewat tombol
 * Catat mutasi di kepala halaman, dan tombol itu sudah memeriksa stoknya lebih dulu.
 */
class TransactionsRelationManager extends RelationManager
{
    public const REFRESH_EVENT = 'gais-mutasi-persediaan-berubah';

    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Movement History';

    #[On(self::REFRESH_EVENT)]
    public function muatUlangRiwayat(): void
    {
        // Sengaja kosong. Livewire menggambar ulang komponen setiap kali metode
        // yang terdaftar dipanggil, dan itu yang dibutuhkan di sini.
    }

    public function form(Schema $schema): Schema
    {
        // Mutasi dicatat lewat tombol di kepala halaman, bukan diketik di tabel ini.
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (SupplyTransaction $record): string => $record->typeLabel())
                    ->color(fn (SupplyTransaction $record): string => $record->typeColor()),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->state(fn (SupplyTransaction $record): string => $record->displayQuantity())
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->placeholder('Tidak dicatat')
                    ->wrap(),
                TextColumn::make('employee.full_name')
                    ->label('Diterima oleh')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('unit_price')
                    ->label('Harga satuan')
                    ->money('IDR', locale: 'id')
                    ->placeholder('Tidak dicatat')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dicatat oleh')
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(SupplyTransaction::TYPES)
                    ->multiple(),
            ])
            ->emptyStateHeading('Belum ada mutasi')
            ->emptyStateDescription(fn (): string => 'Tekan Catat mutasi di bagian atas halaman untuk mencatat barang masuk atau barang keluar. Stok '
                .($this->getOwnerRecord() instanceof SupplyItem ? $this->getOwnerRecord()->name : 'barang ini')
                .' dihitung dari daftar ini.');
    }
}
