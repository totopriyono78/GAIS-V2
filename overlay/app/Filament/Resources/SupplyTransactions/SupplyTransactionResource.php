<?php

namespace App\Filament\Resources\SupplyTransactions;

use App\Filament\Resources\SupplyTransactions\Pages\CreateSupplyTransaction;
use App\Filament\Resources\SupplyTransactions\Pages\EditSupplyTransaction;
use App\Filament\Resources\SupplyTransactions\Pages\ListSupplyTransactions;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SupplyItem;
use App\Models\SupplyTransaction;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Buku stok seluruh barang. Layar ini yang dipakai kalau pertanyaannya
 * "apa saja yang keluar bulan ini", bukan "berapa stok barang tertentu".
 */
class SupplyTransactionResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = SupplyTransaction::class;

    protected static string $moduleCode = 'supply_transactions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|UnitEnum|null $navigationGroup = 'Persediaan';

    protected static ?string $navigationLabel = 'Mutasi barang';

    protected static ?string $modelLabel = 'mutasi barang';

    protected static ?string $pluralModelLabel = 'mutasi barang';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Mutasi')
                ->columns(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Nomor')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan'),
                    Select::make('supply_item_id')
                        ->label('Barang')
                        ->options(fn (): array => SupplyItem::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (SupplyItem $item) => [
                                $item->id => $item->name.' ('.$item->code.')',
                            ])
                            ->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->columnSpan(2)
                        ->helperText(fn ($state): ?string => static::stokSekarang($state)),
                    Select::make('type')
                        ->label('Jenis mutasi')
                        ->options(SupplyTransaction::TYPES)
                        ->required()
                        ->default('masuk')
                        ->live(),
                    TextInput::make('quantity')
                        ->label('Jumlah')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(1000000)
                        // Yang tersimpan bertanda, yang tampil selalu positif. Tanda
                        // ditentukan jenis mutasi, jadi tidak ada yang bisa salah tanda.
                        ->formatStateUsing(fn (?int $state): ?int => $state === null ? null : abs($state))
                        ->suffix(fn ($get): string => SupplyItem::query()->find($get('supply_item_id'))?->unit ?? ''),
                    DatePicker::make('transaction_date')
                        ->label('Tanggal')
                        ->required()
                        ->default(now())
                        ->maxDate(now())
                        ->helperText('Tidak bisa diisi tanggal yang belum terjadi.'),
                ]),

            Section::make('Barang masuk')
                ->description('Diisi kalau barang datang dari pembelian atau kiriman.')
                ->columns(3)
                ->visible(fn ($get): bool => $get('type') === 'masuk')
                ->schema([
                    TextInput::make('unit_price')
                        ->label('Harga satuan')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('Kosongkan kalau fakturnya belum ada')
                        ->helperText('Angka terakhir yang diisi di sini menjadi harga satuan terakhir barang.'),
                    TextInput::make('supplier')
                        ->label('Pemasok')
                        ->maxLength(150)
                        ->placeholder('Nama toko atau vendor'),
                    TextInput::make('reference')
                        ->label('Nomor faktur atau surat jalan')
                        ->maxLength(100)
                        ->placeholder('Belum ada'),
                ]),

            Section::make('Barang keluar')
                ->description('Departemen wajib diisi, karena angka inilah yang nanti dipakai membandingkan anggaran ATK tiap departemen dengan pemakaian sebenarnya.')
                ->columns(2)
                ->visible(fn ($get): bool => $get('type') === 'keluar')
                ->schema([
                    Select::make('department_id')
                        ->label('Departemen yang memakai')
                        ->options(fn (): array => Department::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required(fn ($get): bool => $get('type') === 'keluar'),
                    Select::make('employee_id')
                        ->label('Diterima oleh')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Belum dicatat'),
                ]),

            Section::make('Catatan')
                ->columns(1)
                ->schema([
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Contoh: alasan koreksi, atau nomor berita acara'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['item', 'department']))
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
                TextColumn::make('item.name')
                    ->label('Barang')
                    ->description(fn (SupplyTransaction $record): ?string => $record->item?->code)
                    ->searchable()
                    ->sortable()
                    ->wrap(),
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
                TextColumn::make('supplier')
                    ->label('Pemasok')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label('Faktur atau surat jalan')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('unit_price')
                    ->label('Harga satuan')
                    ->money('IDR', locale: 'id')
                    ->placeholder('Tidak dicatat')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dicatat oleh')
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(SupplyTransaction::TYPES)
                    ->multiple(),
                SelectFilter::make('supply_item_id')
                    ->label('Barang')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('rentang_tanggal')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $q, $tanggal) => $q->whereDate('transaction_date', '>=', $tanggal))
                        ->when($data['sampai'] ?? null, fn (Builder $q, $tanggal) => $q->whereDate('transaction_date', '<=', $tanggal))),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->emptyStateHeading('Belum ada mutasi barang')
            ->emptyStateDescription('Setiap barang masuk, barang keluar, dan koreksi stok tercatat di sini. Daftar ini juga yang menjadi dasar perhitungan stok tiap barang.');
    }

    /**
     * Teks bantuan di bawah pilihan barang. Angkanya dihitung saat itu juga dari
     * basis data, supaya orang tahu stoknya sebelum mengetik jumlah, bukan setelah
     * penyimpanannya ditolak.
     */
    public static function stokSekarang($itemId): ?string
    {
        if (blank($itemId)) {
            return null;
        }

        $item = SupplyItem::query()->find($itemId);

        if ($item === null) {
            return null;
        }

        return 'Stok sekarang '.$item->formatQuantity($item->currentStock())
            .', minimum '.$item->formatQuantity($item->minimum_stock).'.';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplyTransactions::route('/'),
            'create' => CreateSupplyTransaction::route('/create'),
            'edit' => EditSupplyTransaction::route('/{record}/edit'),
        ];
    }
}
