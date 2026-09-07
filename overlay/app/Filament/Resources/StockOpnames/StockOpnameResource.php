<?php

namespace App\Filament\Resources\StockOpnames;

use App\Filament\Resources\StockOpnames\Pages\CreateStockOpname;
use App\Filament\Resources\StockOpnames\Pages\EditStockOpname;
use App\Filament\Resources\StockOpnames\Pages\ListStockOpnames;
use App\Filament\Resources\StockOpnames\RelationManagers\LinesRelationManager;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Location;
use App\Models\StockOpname;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class StockOpnameResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = StockOpname::class;

    protected static string $moduleCode = 'stock_opnames';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'Aset';

    protected static ?string $navigationLabel = 'Stock opname';

    protected static ?string $modelLabel = 'sesi opname';

    protected static ?string $pluralModelLabel = 'sesi opname';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        // Satu kolom di tingkat halaman. Kalau dibiarkan dua kolom (bawaan Filament),
        // seksi Cakupan pemeriksaan hanya kebagian separuh lebar layar, dan tiga
        // pilihan di dalamnya jadi sempit sampai teksnya terpotong jadi dua baris.
        return $schema->columns(1)->components([
            Section::make('Sesi opname')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Nomor')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan'),
                    TextInput::make('name')
                        ->label('Nama sesi')
                        ->required()
                        ->maxLength(150)
                        ->placeholder('Contoh: Opname lantai 3 triwulan tiga'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Cakupan pemeriksaan')
                ->description('Kosongkan yang tidak dipakai. Kalau ketiganya kosong, seluruh aset masuk daftar. Kalau lebih dari satu diisi, aset harus memenuhi semuanya. Aset berstatus sudah dilepas tidak pernah ikut.')
                ->columns(3)
                ->schema([
                    Select::make('scope_location_id')
                        ->label('Lokasi')
                        ->options(fn (): array => Location::query()
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (Location $location) => [
                                $location->id => $location->code.' '.$location->name,
                            ])
                            ->all())
                        ->searchable()
                        ->placeholder('Semua lokasi')
                        ->helperText('Memilih satu lantai ikut mencakup seluruh ruangan di dalamnya.'),
                    Select::make('scope_department_id')
                        ->label('Departemen')
                        ->relationship('targetDepartment', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Semua departemen'),
                    Select::make('scope_asset_category_id')
                        ->label('Kategori aset')
                        ->relationship('targetCategory', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Semua kategori'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Sesi')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => StockOpname::STATUSES[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'berjalan' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('lines_count')
                    ->label('Target')
                    ->counts('lines')
                    ->alignEnd(),
                TextColumn::make('diperiksa')
                    ->label('Diperiksa')
                    ->state(fn (StockOpname $record): int => $record->lines()->where('checked', true)->count())
                    ->alignEnd(),
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->state(fn (StockOpname $record): int => $record->lines()
                        ->where('checked', true)
                        ->where(fn ($query) => $query
                            ->where('found', false)
                            ->orWhereColumn('found_location_id', '!=', 'expected_location_id')
                            ->orWhereColumn('found_condition', '!=', 'expected_condition'))
                        ->count())
                    ->alignEnd()
                    ->color(fn (?int $state): string => $state > 0 ? 'warning' : 'gray'),
                TextColumn::make('finished_at')
                    ->label('Selesai')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum selesai')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StockOpname::STATUSES)
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make()->label('Buka')->icon('heroicon-o-arrow-right-circle')->iconButton(),
                // Filament menilai tombol hapus lewat Gate, dan Gate::before di
                // AppServiceProvider meloloskan super admin untuk semua kemampuan.
                // Jadi canDelete() di bawah harus disebut sendiri di sini, kalau tidak
                // tombolnya tetap muncul pada sesi yang sudah diterapkan penyesuaiannya.
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (StockOpname $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada sesi opname')
            ->emptyStateDescription('Buat sesi baru, tentukan cakupannya, lalu susun daftar target. Daftar itu yang dibawa petugas saat memeriksa barang di lapangan.');
    }

    public static function canDelete(Model $record): bool
    {
        // Sesi yang sudah diterapkan penyesuaiannya adalah bukti kenapa data aset berubah.
        if ($record->isAdjusted()) {
            return false;
        }

        return static::allows('delete');
    }

    public static function canApprove(): bool
    {
        return static::allows('approve');
    }

    public static function getRelations(): array
    {
        return [
            LinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockOpnames::route('/'),
            'create' => CreateStockOpname::route('/create'),
            'edit' => EditStockOpname::route('/{record}/edit'),
        ];
    }
}
