<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Models\WorkOrder;
use App\Support\Rupiah;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Riwayat pemeliharaan satu aset.
 *
 * Preventif dan korektif ditampilkan bercampur dalam satu daftar, sengaja. Orang yang
 * membuka layar ini bertanya "apa saja yang pernah dikerjakan pada barang ini", dan
 * jawabannya tidak berubah menurut asal usul pekerjaannya. Yang membedakan hanya satu
 * kolom, dan kolom itu bisa disaring kalau memang perlu.
 */
class MaintenanceRelationManager extends RelationManager
{
    protected static string $relationship = 'workOrders';

    protected static ?string $title = 'Maintenance History';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('reported_date')
                    ->label('Dilaporkan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('problem')
                    ->label('Pekerjaan')
                    ->description(fn (WorkOrder $record): ?string => $record->work_done)
                    ->limit(90)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->state(fn (WorkOrder $record): string => $record->typeLabel()),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (WorkOrder $record): string => $record->statusLabel())
                    ->color(fn (WorkOrder $record): string => $record->statusColor()),
                TextColumn::make('pelaksana')
                    ->label('Pelaksana')
                    ->state(fn (WorkOrder $record): string => $record->pelaksana())
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cost')
                    ->label('Biaya')
                    ->state(fn (WorkOrder $record): string => filled($record->cost)
                        ? Rupiah::penuh((float) $record->cost)
                        : 'Tidak ada')
                    ->alignEnd()
                    // Hanya pekerjaan yang selesai yang biayanya dijumlahkan, karena
                    // pekerjaan yang dibatalkan tidak pernah menghabiskan uang.
                    ->summarize(
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label('Total biaya selesai')
                            ->query(fn ($query) => $query->where('status', 'selesai'))
                            ->formatStateUsing(fn ($state): string => Rupiah::penuh((float) $state)),
                    ),
            ])
            ->defaultSort('reported_date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(WorkOrder::TYPES),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(WorkOrder::STATUSES)
                    ->multiple(),
            ])
            ->emptyStateHeading('Belum ada pekerjaan pemeliharaan')
            ->emptyStateDescription('Perintah kerja untuk aset ini akan muncul di sini, baik yang terjadwal maupun yang lahir dari kerusakan. Jadwal pemeliharaan rutinnya diatur di menu Jadwal pemeliharaan.');
    }

    // Perintah kerja dibuat dari menunya sendiri, supaya nomor, jadwal, dan penugasannya
    // ikut terisi. Tombol tambah di sini hanya akan menghasilkan perintah kerja setengah.
    public function canCreate(): bool
    {
        return false;
    }
}
