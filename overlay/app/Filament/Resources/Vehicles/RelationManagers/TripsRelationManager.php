<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Employee;
use App\Models\VehicleBooking;
use App\Models\VehicleTrip;
use App\Services\OdometerKendaraan;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Log perjalanan satu kendaraan.
 *
 * Dicatat dua langkah, sesuai kenyataannya: berangkat dicatat saat kunci diambil,
 * kembali dicatat saat kunci dikembalikan. Memaksa keduanya diisi sekaligus berarti
 * memaksa orang menebak odometer akhir sebelum berangkat, dan tebakan itu akan
 * dituliskan juga.
 *
 * Angka odometer akhir tidak boleh lebih kecil daripada yang pernah tercatat. Aturannya
 * dipegang App\Services\OdometerKendaraan, bukan diulang di tiap layar.
 */
class TripsRelationManager extends RelationManager
{
    protected static string $relationship = 'trips';

    protected static ?string $title = 'Log perjalanan';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            DateTimePicker::make('departed_at')
                ->label('Berangkat')
                ->seconds(false)
                ->displayFormat('d M Y, H:i')
                ->default(now())
                ->required(),
            TextInput::make('start_odometer_km')
                ->label('Odometer berangkat')
                ->numeric()
                ->minValue(0)
                ->suffix('km')
                ->required()
                ->default(fn (): ?int => $this->getOwnerRecord()->last_odometer_km)
                ->helperText('Terisi dari odometer terakhir yang tercatat. Ganti kalau angka di dasbor kendaraan berbeda.'),
            TextInput::make('destination')
                ->label('Tujuan')
                ->required()
                ->maxLength(200)
                ->columnSpanFull()
                ->placeholder('Contoh: Gudang Bekasi, lalu kembali ke kantor'),
            Select::make('driver_employee_id')
                ->label('Pengemudi')
                ->options(fn (): array => Employee::query()
                    ->where('is_active', true)
                    ->orderBy('full_name')
                    ->pluck('full_name', 'id')
                    ->all())
                ->searchable()
                ->default(fn (): ?int => $this->getOwnerRecord()->default_driver_employee_id)
                ->placeholder('Tidak dicatat'),
            Select::make('vehicle_booking_id')
                ->label('Pemesanan')
                ->options(fn (): array => VehicleBooking::query()
                    ->where('vehicle_id', $this->getOwnerRecord()->getKey())
                    ->whereIn('status', ['ditugaskan', 'berjalan'])
                    ->orderBy('start_at')
                    ->get()
                    ->mapWithKeys(fn (VehicleBooking $b) => [$b->id => $b->code.' '.$b->jadwalLabel()])
                    ->all())
                ->searchable()
                ->placeholder('Tanpa pemesanan')
                ->helperText('Kosongkan untuk perjalanan operasional harian yang memang tidak dipesan lebih dulu.'),
            Textarea::make('purpose')
                ->label('Keperluan')
                ->rows(2)
                ->columnSpanFull()
                ->placeholder('Contoh: mengantar sampel barang ke gudang.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('departed_at')
                    ->label('Berangkat')
                    ->dateTime('d M Y, H:i')
                    ->description(fn (VehicleTrip $record): string => $record->lamaLabel())
                    ->sortable(),
                TextColumn::make('destination')
                    ->label('Tujuan')
                    ->description(fn (VehicleTrip $record): ?string => $record->booking?->code)
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('pengemudi')
                    ->label('Pengemudi')
                    ->state(fn (VehicleTrip $record): string => $record->pengemudi())
                    ->wrap(),
                TextColumn::make('odometer')
                    ->label('Odometer')
                    ->state(fn (VehicleTrip $record): string => number_format($record->start_odometer_km, 0, ',', '.')
                        .' sampai '
                        .(filled($record->end_odometer_km) ? number_format($record->end_odometer_km, 0, ',', '.') : 'belum dicatat'))
                    ->alignEnd(),
                TextColumn::make('jarak')
                    ->label('Jarak')
                    ->state(fn (VehicleTrip $record): string => $record->jarakLabel())
                    ->color(fn (VehicleTrip $record): ?string => $record->selesai() ? null : 'warning')
                    ->alignEnd(),
                TextColumn::make('purpose')
                    ->label('Keperluan')
                    ->placeholder('Tidak dicatat')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dicatat oleh')
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('departed_at', 'desc')
            ->filters([
                Filter::make('berjalan')
                    ->label('Belum kembali')
                    ->query(fn (Builder $query): Builder => $query->berjalan()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Catat keberangkatan')
                    ->modalHeading('Catat keberangkatan')
                    ->modalSubmitActionLabel('Simpan keberangkatan')
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
            ])
            ->recordActions([
                $this->tutupAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord()))
                    ->modalDescription('Jarak perjalanan ini keluar dari rekap pemakaian kendaraan. Odometer kendaraan tidak ikut mundur, karena angka yang sudah terlanjur tercatat di tempat lain tetap berlaku.'),
            ])
            ->emptyStateHeading('Belum ada perjalanan yang dicatat')
            ->emptyStateDescription('Catat keberangkatan saat kunci diambil, lalu tutup perjalanannya saat kunci dikembalikan. Dari dua angka odometer itulah jarak, pemakaian, dan konsumsi bahan bakar kendaraan ini dihitung.');
    }

    /**
     * Menutup perjalanan. Odometer akhir diperiksa di sini, bukan hanya di formulir,
     * karena angka yang mundur merusak seluruh perhitungan sesudahnya tanpa ada yang
     * menyadarinya.
     */
    protected function tutupAction(): Action
    {
        return Action::make('tutup')
            ->label('Catat kembali')
            ->icon('heroicon-o-flag')
            ->color('success')
            ->iconButton()
            ->visible(fn (VehicleTrip $record): bool => ! $record->selesai()
                && VehicleResource::canEdit($this->getOwnerRecord()))
            ->modalHeading(fn (VehicleTrip $record): string => 'Catat kembalinya perjalanan ke '.$record->destination)
            ->modalDescription('Setelah disimpan, odometer kendaraan ikut maju ke angka ini, dan pemesanan yang menyertainya ikut ditutup.')
            ->modalSubmitActionLabel('Simpan kepulangan')
            ->fillForm(fn (VehicleTrip $record): array => ['returned_at' => now()->format('Y-m-d H:i:s')])
            ->schema([
                DateTimePicker::make('returned_at')
                    ->label('Kembali')
                    ->seconds(false)
                    ->displayFormat('d M Y, H:i')
                    ->required(),
                TextInput::make('end_odometer_km')
                    ->label('Odometer kembali')
                    ->numeric()
                    ->suffix('km')
                    ->required()
                    ->helperText(fn (VehicleTrip $record): string => 'Odometer saat berangkat '
                        .number_format($record->start_odometer_km, 0, ',', '.').' km.'),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->placeholder('Contoh: ban depan kanan mulai botak.'),
            ])
            ->action(function (VehicleTrip $record, array $data): void {
                $akhir = (int) $data['end_odometer_km'];

                if ($akhir < $record->start_odometer_km) {
                    Notification::make()
                        ->danger()
                        ->title('Odometer kembali lebih kecil daripada saat berangkat')
                        ->body('Saat berangkat tercatat '.number_format($record->start_odometer_km, 0, ',', '.').' km. Periksa lagi angkanya.')
                        ->persistent()
                        ->send();

                    return;
                }

                $alasan = app(OdometerKendaraan::class)
                    ->alasanDitolak($this->getOwnerRecord(), $akhir);

                if ($alasan !== null) {
                    Notification::make()
                        ->danger()
                        ->title('Odometer tidak bisa mundur')
                        ->body($alasan)
                        ->persistent()
                        ->send();

                    return;
                }

                $record->fill($data)->save();

                Notification::make()
                    ->success()
                    ->title('Perjalanan ditutup')
                    ->body('Jarak tempuh '.$record->fresh()->jarakLabel().'.')
                    ->send();
            });
    }
}
