<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\VehicleRefueling;
use App\Services\OdometerKendaraan;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Pengisian bahan bakar satu kendaraan.
 *
 * Kolom konsumsi tidak pernah menampilkan angka yang tidak bisa dipertanggungjawabkan.
 * Kilometer per liter hanya dihitung antara dua pengisian penuh, karena hanya di dua
 * titik itulah isi tangkinya diketahui sama. Baris yang belum memenuhi syarat itu
 * mengatakan alasannya, bukan menampilkan strip kosong yang terbaca seperti data hilang.
 */
class RefuelingsRelationManager extends RelationManager
{
    protected static string $relationship = 'refuelings';

    protected static ?string $title = 'Pengisian BBM';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            DatePicker::make('filled_at')
                ->label('Tanggal pengisian')
                ->displayFormat('d M Y')
                ->default(now())
                ->maxDate(now())
                ->required(),
            TextInput::make('odometer_km')
                ->label('Odometer saat mengisi')
                ->numeric()
                ->minValue(0)
                ->suffix('km')
                ->required()
                ->default(fn (): ?int => $this->getOwnerRecord()->last_odometer_km)
                ->helperText('Angka inilah yang membuat konsumsi bisa dihitung. Tanpa odometer, pengisian hanya jadi catatan pengeluaran.'),
            TextInput::make('liters')
                ->label('Jumlah liter')
                ->numeric()
                ->minValue(0.01)
                ->step('0.01')
                ->suffix('liter')
                ->required(),
            TextInput::make('cost')
                ->label('Total dibayar')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->placeholder('Tidak dicatat'),
            Toggle::make('is_full_tank')
                ->label('Diisi sampai penuh')
                ->default(true)
                ->columnSpanFull()
                ->helperText('Konsumsi kilometer per liter hanya bisa dihitung antara dua pengisian penuh. Pengisian setengah tangki tetap dicatat dan liternya tetap ikut dihitung, tetapi tidak menjadi titik ukur.'),
            TextInput::make('station')
                ->label('SPBU')
                ->maxLength(120)
                ->placeholder('Tidak dicatat'),
            Select::make('driver_employee_id')
                ->label('Yang mengisi')
                ->options(fn (): array => Employee::query()
                    ->where('is_active', true)
                    ->orderBy('full_name')
                    ->pluck('full_name', 'id')
                    ->all())
                ->searchable()
                ->default(fn (): ?int => $this->getOwnerRecord()->default_driver_employee_id)
                ->placeholder('Tidak dicatat'),
            Select::make('fuel_type')
                ->label('Jenis bahan bakar')
                ->options(Vehicle::FUEL_TYPES)
                ->default(fn (): ?string => $this->getOwnerRecord()->fuel_type)
                ->placeholder('Ikut jenis kendaraan'),
            Textarea::make('notes')
                ->label('Catatan')
                ->rows(2)
                ->columnSpanFull()
                ->placeholder('Contoh: isi di SPBU luar kota karena tangki nyaris kosong.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filled_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (VehicleRefueling $record): ?string => $record->station)
                    ->sortable(),
                TextColumn::make('odometer_km')
                    ->label('Odometer')
                    ->state(fn (VehicleRefueling $record): string => number_format($record->odometer_km, 0, ',', '.').' km')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('liters')
                    ->label('Liter')
                    ->state(fn (VehicleRefueling $record): string => $record->litersLabel()
                        .($record->is_full_tank ? '' : ', tidak penuh'))
                    ->alignEnd(),
                TextColumn::make('cost')
                    ->label('Dibayar')
                    ->state(fn (VehicleRefueling $record): string => filled($record->cost)
                        ? Rupiah::penuh((float) $record->cost)
                        : 'Tidak dicatat')
                    ->description(fn (VehicleRefueling $record): ?string => $record->hargaPerLiter()
                        ? Rupiah::penuh((float) $record->hargaPerLiter()).' per liter'
                        : null)
                    ->alignEnd(),
                TextColumn::make('konsumsi')
                    ->label('Konsumsi')
                    ->state(fn (VehicleRefueling $record): string => $record->konsumsiLabel())
                    ->color(fn (VehicleRefueling $record): ?string => $record->konsumsiKmPerLiter() === null ? 'gray' : null)
                    ->alignEnd(),
                TextColumn::make('driver.full_name')
                    ->label('Yang mengisi')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('odometer_km', 'desc')
            ->filters([
                Filter::make('penuh')
                    ->label('Hanya pengisian penuh')
                    ->query(fn (Builder $query): Builder => $query->penuh()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat pengisian')
                    ->modalHeading('Catat pengisian BBM')
                    ->modalSubmitActionLabel('Simpan pengisian')
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord()))
                    ->before(function (array $data, CreateAction $action): void {
                        $alasan = app(OdometerKendaraan::class)->alasanDitolakPadaTanggal(
                            $this->getOwnerRecord(),
                            (int) $data['odometer_km'],
                            Carbon::parse($data['filled_at']),
                        );

                        if ($alasan === null) {
                            return;
                        }

                        Notification::make()
                            ->danger()
                            ->title('Odometer tidak cocok dengan urutan waktunya')
                            ->body($alasan)
                            ->persistent()
                            ->send();

                        $action->halt();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord()))
                    ->modalDescription('Menghapus satu pengisian mengubah konsumsi penggal sesudahnya, karena liter yang dipakai menempuh jarak itu ikut hilang dari hitungan.'),
            ])
            ->emptyStateHeading('Belum ada pengisian yang dicatat')
            ->emptyStateDescription('Catat tiap kali kendaraan ini diisi, lengkap dengan odometer dan jumlah liternya. Setelah ada dua pengisian penuh, aplikasi mulai menghitung sendiri berapa kilometer per liter kendaraan ini.');
    }
}
