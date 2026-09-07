<?php

namespace App\Filament\Resources\StockOpnames\RelationManagers;

use App\Filament\Resources\StockOpnames\StockOpnameResource;
use App\Models\Asset;
use App\Models\Location;
use App\Models\StockOpnameLine;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

/**
 * Daftar target pemeriksaan di dalam satu sesi opname.
 *
 * Cara pakainya di lapangan: pindai barcode aset dengan pemindai USB, kodenya
 * masuk ke kotak pencarian, barisnya muncul, lalu tekan tombol centang untuk
 * menandai sesuai. Kalau ada bedanya, pakai tombol catat temuan.
 */
class LinesRelationManager extends RelationManager
{
    /**
     * Nama peristiwa yang dikirim halaman induk setiap kali daftar target atau status
     * sesi berubah. Komponen ini hidup terpisah dari halaman, jadi tanpa peristiwa ini
     * isinya tetap versi lama sampai halaman dimuat ulang.
     */
    public const REFRESH_EVENT = 'gais-daftar-target-berubah';

    protected static string $relationship = 'lines';

    protected static ?string $title = 'Daftar target pemeriksaan';

    #[On(self::REFRESH_EVENT)]
    public function muatUlangDaftar(): void
    {
        // Sengaja kosong. Livewire menggambar ulang komponen setiap kali sebuah
        // metode yang terdaftar dipanggil, dan itulah yang dibutuhkan di sini.
    }

    public function form(Schema $schema): Schema
    {
        // Baris dibuat oleh sistem dari cakupan sesi, bukan diketik satu per satu.
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('asset_code')
            ->columns([
                TextColumn::make('asset_code')
                    ->label('Kode aset')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset_name')
                    ->label('Nama')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('expectedLocation.code')
                    ->label('Lokasi tercatat')
                    ->placeholder('Belum diisi'),
                TextColumn::make('foundLocation.code')
                    ->label('Lokasi ditemukan')
                    ->placeholder('Belum dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hasil')
                    ->label('Hasil')
                    ->badge()
                    ->state(fn (StockOpnameLine $record): string => $record->resultLabel())
                    ->color(fn (StockOpnameLine $record): string => $record->resultColor()),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('checked_at')
                    ->label('Diperiksa')
                    ->dateTime('d M H:i')
                    ->placeholder('Belum')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('asset_code')
            ->filters([
                Filter::make('belum_diperiksa')
                    ->label('Belum diperiksa')
                    ->query(fn (Builder $query): Builder => $query->where('checked', false))
                    ->toggle(),
                Filter::make('ada_selisih')
                    ->label('Ada selisih')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('checked', true)
                        ->where(fn (Builder $sub) => $sub
                            ->where('found', false)
                            ->orWhereColumn('found_location_id', '!=', 'expected_location_id')
                            ->orWhereColumn('found_condition', '!=', 'expected_condition')))
                    ->toggle(),
                Filter::make('tidak_ditemukan')
                    ->label('Tidak ditemukan')
                    ->query(fn (Builder $query): Builder => $query->where('checked', true)->where('found', false))
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('sesuai')
                    ->label('Tandai sesuai catatan')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->iconButton()
                    ->visible(fn (StockOpnameLine $record): bool => $this->bolehMemeriksa() && ! $record->checked)
                    ->action(function (StockOpnameLine $record): void {
                        $record->markAsMatching();
                    }),

                Action::make('temuan')
                    ->label('Catat temuan')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->visible(fn (): bool => $this->bolehMemeriksa())
                    ->modalHeading(fn (StockOpnameLine $record): string => 'Temuan untuk '.$record->asset_code)
                    ->modalSubmitActionLabel('Simpan temuan')
                    ->fillForm(fn (StockOpnameLine $record): array => [
                        'found' => $record->found ?? true,
                        'found_location_id' => $record->found_location_id ?? $record->expected_location_id,
                        'found_condition' => $record->found_condition ?? $record->expected_condition,
                        'notes' => $record->notes,
                    ])
                    ->schema([
                        Toggle::make('found')
                            ->label('Barangnya ditemukan')
                            ->default(true)
                            ->helperText('Matikan kalau barang tidak ada di mana pun saat diperiksa.'),
                        Select::make('found_location_id')
                            ->label('Lokasi saat ditemukan')
                            ->options(fn (): array => Location::query()
                                ->where('is_active', true)
                                ->orderBy('code')
                                ->get()
                                ->mapWithKeys(fn (Location $location) => [
                                    $location->id => $location->code.' '.$location->name,
                                ])
                                ->all())
                            ->searchable()
                            ->placeholder('Belum ditentukan'),
                        Select::make('found_condition')
                            ->label('Kondisi saat ditemukan')
                            ->options(Asset::CONDITIONS)
                            ->placeholder('Belum ditentukan'),
                        TextInput::make('notes')
                            ->label('Catatan')
                            ->maxLength(255)
                            ->placeholder('Contoh: ditemukan di gudang, layar retak'),
                    ])
                    ->action(function (StockOpnameLine $record, array $data): void {
                        $ditemukan = (bool) ($data['found'] ?? true);

                        $record->forceFill([
                            'checked' => true,
                            'found' => $ditemukan,
                            'found_location_id' => $ditemukan ? ($data['found_location_id'] ?? null) : null,
                            'found_condition' => $ditemukan ? ($data['found_condition'] ?? null) : null,
                            'notes' => $data['notes'] ?? null,
                            'checked_at' => now(),
                            'checked_by_user_id' => Auth::id(),
                        ])->save();
                    }),

                Action::make('ulangi')
                    ->label('Batalkan pemeriksaan baris ini')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->iconButton()
                    ->visible(fn (StockOpnameLine $record): bool => $this->bolehMemeriksa() && $record->checked)
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan pemeriksaan baris ini')
                    ->modalDescription('Temuan yang sudah dicatat untuk baris ini dihapus, dan barisnya kembali berstatus belum diperiksa.')
                    ->action(fn (StockOpnameLine $record) => $record->resetCheck()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('sesuai_massal')
                        ->label('Tandai sesuai catatan')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (): bool => $this->bolehMemeriksa())
                        ->requiresConfirmation()
                        ->modalHeading('Tandai baris terpilih sesuai catatan')
                        ->modalDescription('Dipakai kalau satu rak atau satu ruangan sudah dicek sekaligus dan semuanya cocok. Baris yang sudah diperiksa tidak diubah.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $selectedRecords): void {
                            $jumlah = 0;

                            foreach ($selectedRecords as $record) {
                                if ($record->checked) {
                                    continue;
                                }

                                $record->markAsMatching();
                                $jumlah++;
                            }

                            Notification::make()
                                ->title("{$jumlah} baris ditandai sesuai catatan")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('Daftar target masih kosong')
            ->emptyStateDescription('Tekan Susun daftar target di bagian atas halaman untuk mengambil aset yang masuk cakupan sesi ini.');
    }

    /**
     * Temuan hanya boleh dicatat selama sesi berjalan. Sesi yang masih draf belum
     * dimulai, dan sesi yang sudah ditutup tidak boleh berubah lagi.
     */
    protected function bolehMemeriksa(): bool
    {
        return $this->getOwnerRecord()->isRunning()
            && StockOpnameResource::canEdit($this->getOwnerRecord());
    }
}
