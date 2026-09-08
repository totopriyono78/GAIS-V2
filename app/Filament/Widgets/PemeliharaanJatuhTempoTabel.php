<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceVisit;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Jadwal pemeliharaan yang sudah lewat atau segera jatuh tempo.
 *
 * Tabel, bukan diagram, karena yang dibutuhkan di sini bukan proporsi melainkan daftar
 * kerja: pekerjaan apa, pada aset mana, sudah lewat berapa hari. Diurutkan dari yang
 * paling lama terlewat.
 */
class PemeliharaanJatuhTempoTabel extends TableWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Maintenance Due';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('maintenance_schedules.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => MaintenanceVisit::query()
                ->with(['asset.location', 'vendor', 'maintenanceSchedule'])
                ->jatuhTempo(30)
                ->orderBy('due_date'))
            ->columns([
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date('d M Y')
                    ->description(fn (MaintenanceVisit $record): string => $record->keteranganWaktu())
                    ->color(fn (MaintenanceVisit $record): string => $record->statusColor()),
                TextColumn::make('asset.code')
                    ->label('Kode aset')
                    ->fontFamily('mono'),
                TextColumn::make('maintenanceSchedule.name')
                    ->label('Pekerjaan')
                    ->description(fn (MaintenanceVisit $record): ?string => $record->asset?->name)
                    ->wrap(),
                TextColumn::make('asset.location.name')
                    ->label('Lokasi')
                    ->placeholder('Belum ditentukan'),
                TextColumn::make('pelaksana')
                    ->label('Rencananya oleh')
                    ->state(fn (MaintenanceVisit $record): string => $record->vendor?->name
                        ?? $record->technician?->full_name
                        ?? 'Belum ditentukan'),
            ])
            ->paginated([10, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tidak ada pemeliharaan yang jatuh tempo')
            ->emptyStateDescription('Tidak ada kunjungan preventif yang jatuh tempo dalam 30 hari ke depan. Jadwal baru dibuat di menu Jadwal pemeliharaan, atau sekaligus untuk banyak aset dari Daftar aset.');
    }
}
