<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\VehicleDocument;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Dokumen kendaraan yang sudah lewat atau segera jatuh tempo.
 *
 * Tabel, bukan diagram, karena yang dibutuhkan adalah daftar kerja: kendaraan mana,
 * dokumen apa, dan berapa hari lagi. Diurutkan dari yang paling lama terlambat, dan
 * barisnya bisa diklik langsung ke kendaraannya.
 */
class KendaraanJatuhTempoTabel extends TableWidget
{
    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Dokumen kendaraan yang perlu diurus';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('vehicles.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => VehicleDocument::query()
                ->with('vehicle.asset')
                ->whereHas('vehicle', fn (Builder $query) => $query->where('is_active', true))
                ->jatuhTempo(60)
                ->orderBy('expires_at'))
            ->columns([
                TextColumn::make('expires_at')
                    ->label('Berlaku sampai')
                    ->date('d M Y')
                    ->description(fn (VehicleDocument $record): string => $record->keteranganWaktu())
                    ->color(fn (VehicleDocument $record): string => $record->keadaanColor()),
                TextColumn::make('vehicle.plate_number')
                    ->label('Nomor polisi')
                    ->fontFamily('mono'),
                TextColumn::make('type')
                    ->label('Dokumen')
                    ->state(fn (VehicleDocument $record): string => $record->jenisLabel())
                    ->description(fn (VehicleDocument $record): ?string => $record->vehicle?->namaLengkap())
                    ->wrap(),
                TextColumn::make('issuer')
                    ->label('Diterbitkan oleh')
                    ->placeholder('Tidak dicatat')
                    ->wrap(),
            ])
            ->recordUrl(fn (VehicleDocument $record): ?string => $record->vehicle
                ? VehicleResource::getUrl('view', ['record' => $record->vehicle])
                : null)
            ->paginated([10, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tidak ada dokumen yang perlu diurus')
            ->emptyStateDescription('Tidak ada pajak, KIR, atau asuransi yang jatuh tempo dalam 60 hari ke depan. Dokumen baru dicatat dari halaman kendaraannya masing masing.');
    }
}
