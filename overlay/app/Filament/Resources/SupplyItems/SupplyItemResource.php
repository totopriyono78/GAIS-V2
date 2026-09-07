<?php

namespace App\Filament\Resources\SupplyItems;

use App\Filament\Resources\SupplyItems\Pages\CreateSupplyItem;
use App\Filament\Resources\SupplyItems\Pages\EditSupplyItem;
use App\Filament\Resources\SupplyItems\Pages\ListSupplyItems;
use App\Filament\Resources\SupplyItems\RelationManagers\TransactionsRelationManager;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\SupplyItem;
use App\Models\SupplyTransaction;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SupplyItemResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = SupplyItem::class;

    protected static string $moduleCode = 'supply_items';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|UnitEnum|null $navigationGroup = 'Persediaan';

    protected static ?string $navigationLabel = 'Barang habis pakai';

    protected static ?string $modelLabel = 'barang habis pakai';

    protected static ?string $pluralModelLabel = 'barang habis pakai';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Barang')
                ->columns(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan'),
                    TextInput::make('name')
                        ->label('Nama barang')
                        ->required()
                        ->maxLength(150)
                        ->columnSpan(2)
                        ->placeholder('Contoh: Pulpen tinta biru 0,5 mm'),
                    Select::make('category')
                        ->label('Kategori')
                        ->options(SupplyItem::CATEGORIES)
                        ->required()
                        ->default('alat_tulis'),
                    Select::make('unit')
                        ->label('Satuan')
                        ->options(SupplyItem::UNITS)
                        ->required()
                        ->default('pcs')
                        ->helperText('Satuan terkecil yang benar benar diserahkan ke orang.'),
                    Select::make('location_id')
                        ->label('Tempat simpan')
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
                ]),

            Section::make('Batas pemesanan ulang')
                ->description('Angka ini yang membuat barang muncul di penyaring Perlu dipesan. Isi sebanyak pemakaian selama waktu tunggu pengadaan, supaya barang tidak habis sebelum kiriman berikutnya datang.')
                ->columns(3)
                ->schema([
                    TextInput::make('minimum_stock')
                        ->label('Stok minimum')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(1000000)
                        ->suffix(fn (?SupplyItem $record): string => $record?->unit ?? ''),
                    TextInput::make('last_price')
                        ->label('Harga satuan terakhir')
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Terisi sendiri dari mutasi barang masuk'),
                    TextInput::make('account_expense')
                        ->label('Akun beban COA')
                        ->maxLength(30)
                        ->placeholder('Belum diisi')
                        ->helperText('Dipakai saat pembebanan biaya ke finance dibangun.'),
                ]),

            Section::make('Keterangan')
                ->columns(1)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Barang masih dipakai')
                        ->default(true)
                        ->helperText('Matikan kalau barang ini tidak dibeli lagi. Riwayat mutasinya tetap tersimpan.'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withSum('transactions', 'quantity'))
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => SupplyItem::CATEGORIES[$state] ?? (string) $state),
                TextColumn::make('transactions_sum_quantity')
                    ->label('Stok')
                    ->state(fn (SupplyItem $record): string => $record->formatQuantity(static::stockOf($record)))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('minimum_stock')
                    ->label('Minimum')
                    ->state(fn (SupplyItem $record): string => $record->formatQuantity($record->minimum_stock))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('keadaan')
                    ->label('Keadaan')
                    ->badge()
                    ->state(fn (SupplyItem $record): string => $record->stockStateLabel(static::stockOf($record)))
                    ->color(fn (SupplyItem $record): string => $record->stockStateColor(static::stockOf($record))),
                TextColumn::make('location.code')
                    ->label('Tempat simpan')
                    ->placeholder('Belum ditentukan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_price')
                    ->label('Harga satuan terakhir')
                    ->money('IDR', locale: 'id')
                    ->placeholder('Belum ada')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_active')
                    ->label('Dipakai')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Tidak')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(SupplyItem::CATEGORIES)
                    ->multiple(),
                Filter::make('perlu_dipesan')
                    ->label('Perlu dipesan')
                    ->query(fn (Builder $query): Builder => $query->needsRestock())
                    ->toggle(),
                Filter::make('habis')
                    ->label('Stoknya habis')
                    ->query(fn (Builder $query): Builder => $query->outOfStock())
                    ->toggle(),
                Filter::make('tidak_dipakai')
                    ->label('Sudah tidak dipakai')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false))
                    ->toggle(),
            ])
            ->recordActions([
                static::catatMutasiAction(),
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyItem $record): bool => static::canDelete($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada barang habis pakai')
            ->emptyStateDescription('Daftarkan barang yang stoknya perlu dijaga, misalnya kertas A4, tinta printer, atau sabun cuci tangan. Setelah itu catat barang masuk supaya stoknya terisi.');
    }

    /**
     * Tombol tercepat di layar ini. Pekerjaan harian staf gudang adalah menyerahkan
     * barang lalu mencatatnya, dan itu tidak layak menempuh empat halaman. Barangnya
     * sudah diketahui dari barisnya, jadi yang perlu diketik tinggal jumlahnya.
     */
    public static function catatMutasiAction(bool $iconOnly = true): Action
    {
        $action = Action::make('catat_mutasi')
            ->label('Catat mutasi')
            ->icon('heroicon-o-arrows-right-left');

        if ($iconOnly) {
            $action->iconButton();
        }

        return $action
            ->visible(fn (SupplyItem $record): bool => $record->is_active && static::allows('update'))
            ->modalHeading(fn (SupplyItem $record): string => 'Catat mutasi '.$record->name)
            ->modalDescription(fn (SupplyItem $record): string => 'Stok sekarang '
                .$record->formatQuantity($record->currentStock()).'.')
            ->modalSubmitActionLabel('Simpan mutasi')
            ->fillForm(fn (): array => [
                'type' => 'keluar',
                'transaction_date' => now()->toDateString(),
            ])
            ->schema([
                Select::make('type')
                    ->label('Jenis mutasi')
                    ->options(SupplyTransaction::TYPES)
                    ->required()
                    ->live(),
                TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(1000000)
                    ->suffix(fn (SupplyItem $record): string => $record->unit)
                    /*
                     * Diperiksa sebagai aturan validasi, bukan di dalam aksi, supaya
                     * modalnya tetap terbuka beserta isian yang sudah diketik dan
                     * pesannya muncul persis di bawah kolom Jumlah. Kalau diperiksa
                     * di dalam aksi, modalnya tertutup dan orang harus mengetik ulang.
                     */
                    ->rules([
                        fn (SupplyItem $record, $get): Closure => function (string $attribute, $value, Closure $fail) use ($record, $get) {
                            $masalah = SupplyTransaction::stockProblem($record->id, $get('type'), $value);

                            if ($masalah !== null) {
                                $fail($masalah);
                            }
                        },
                    ]),
                DatePicker::make('transaction_date')
                    ->label('Tanggal')
                    ->required()
                    ->maxDate(now())
                    ->default(now()),
                Select::make('department_id')
                    ->label('Departemen yang memakai')
                    ->options(fn (): array => Department::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->visible(fn ($get): bool => $get('type') === 'keluar')
                    ->required(fn ($get): bool => $get('type') === 'keluar')
                    ->helperText('Dipakai untuk membebankan biaya ATK ke departemen yang benar.'),
                Select::make('employee_id')
                    ->label('Diterima oleh')
                    ->options(fn (): array => Employee::query()
                        ->where('is_active', true)
                        ->orderBy('full_name')
                        ->pluck('full_name', 'id')
                        ->all())
                    ->searchable()
                    ->visible(fn ($get): bool => $get('type') === 'keluar')
                    ->placeholder('Belum dicatat'),
                TextInput::make('unit_price')
                    ->label('Harga satuan')
                    ->numeric()
                    ->prefix('Rp')
                    ->visible(fn ($get): bool => $get('type') === 'masuk')
                    ->placeholder('Kosongkan kalau belum ada fakturnya'),
                TextInput::make('notes')
                    ->label('Catatan')
                    ->maxLength(255),
            ])
            ->action(function (SupplyItem $record, array $data, Action $action): void {
                $masalah = SupplyTransaction::stockProblem(
                    $record->id,
                    $data['type'] ?? null,
                    $data['quantity'] ?? null,
                );

                if ($masalah !== null) {
                    Notification::make()
                        ->title('Mutasi tidak disimpan')
                        ->body($masalah)
                        ->danger()
                        ->persistent()
                        ->send();

                    // halt() menahan modalnya tetap terbuka beserta isian yang sudah
                    // diketik. Kalau hanya return, modalnya tertutup dan orang harus
                    // membuka lagi lalu mengetik ulang hanya untuk mengubah angkanya.
                    $action->halt();
                }

                $transaction = SupplyTransaction::query()->create([
                    'supply_item_id' => $record->id,
                    'type' => $data['type'],
                    'quantity' => $data['quantity'],
                    'unit_price' => $data['unit_price'] ?? null,
                    'transaction_date' => $data['transaction_date'],
                    'department_id' => $data['department_id'] ?? null,
                    'employee_id' => $data['employee_id'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                $record->refresh();

                Notification::make()
                    ->title('Mutasi '.$transaction->code.' tersimpan')
                    ->body('Stok '.$record->name.' sekarang '.$record->formatQuantity($record->currentStock()).'.')
                    ->success()
                    ->send();
            });
    }

    /**
     * Kolom agregat hanya ada kalau kuerinya lewat tabel daftar. Di dalam modal dan
     * halaman lain, nilainya dihitung langsung. Satu tempat ini yang memutuskan.
     */
    public static function stockOf(SupplyItem $record): int
    {
        return $record->transactions_sum_quantity !== null
            ? (int) $record->transactions_sum_quantity
            : $record->currentStock();
    }

    public static function canDelete(Model $record): bool
    {
        // Menghapus barang ikut menghapus seluruh mutasinya, dan itu memutus buku stok.
        // Barang yang sudah tidak dibeli lagi dimatikan lewat sakelar Barang masih dipakai.
        if ($record->transactions()->exists()) {
            return false;
        }

        return static::allows('delete');
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplyItems::route('/'),
            'create' => CreateSupplyItem::route('/create'),
            'edit' => EditSupplyItem::route('/{record}/edit'),
        ];
    }
}
