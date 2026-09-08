<?php

namespace App\Filament\Resources\AssetCategories;

use App\Filament\Resources\AssetCategories\Pages\ListAssetCategories;
use App\Models\AssetCategory;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AssetCategoryResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = AssetCategory::class;

    protected static string $moduleCode = 'asset_categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $navigationLabel = 'Asset Categories';

    protected static ?string $modelLabel = 'asset category';

    protected static ?string $pluralModelLabel = 'asset categories';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode kategori')
                        ->required()
                        ->maxLength(30)
                        ->alphaDash()
                        ->unique(ignoreRecord: true)
                        ->disabled(fn (?AssetCategory $record): bool => $record !== null)
                        ->dehydrated(fn (?AssetCategory $record): bool => $record === null)
                        ->helperText('Penanda tetap, tidak bisa diubah setelah kategori dipakai. Contoh: KOM'),
                    TextInput::make('name')
                        ->label('Nama kategori')
                        ->required()
                        ->maxLength(150),
                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(2)
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Kategori dipakai')
                        ->default(true),
                ]),

            Section::make('COA Account Number')
                ->description('Nomor akun aset tetap menjadi segmen kedua kode aset, contohnya FIN-1201-2026-0001. Karena itu nomor akun wajib diisi sebelum kategori ini bisa dipakai mencatat aset. Angkanya ditentukan tim finance, bukan ditebak sistem.')
                ->columns(3)
                ->schema([
                    TextInput::make('account_asset')
                        ->label('Akun aset tetap')
                        ->required()
                        ->maxLength(30)
                        ->helperText('Segmen kedua kode aset.'),
                    TextInput::make('account_accumulated')
                        ->label('Akun akumulasi penyusutan')
                        ->maxLength(30)
                        ->helperText('Dipakai saat jurnal penyusutan dibangun.'),
                    TextInput::make('account_expense')
                        ->label('Akun beban penyusutan')
                        ->maxLength(30)
                        ->helperText('Dipakai saat jurnal penyusutan dibangun.'),
                ]),

            Section::make('Depreciation')
                ->columnSpanFull()
                ->description('Perhitungan penyusutannya sendiri belum dibangun, jadi angka di seksi ini baru tersimpan sebagai kebijakan, belum menghasilkan nilai buku atau jurnal. Kelompok pajak menentukan masa manfaat bawaan, angkanya mengikuti PMK 72 Tahun 2023. Kalau kebijakan akuntansi perusahaan berbeda dari kelompok pajaknya, isi masa manfaat secara manual dan angka itu yang dipakai.')
                ->columns(3)
                ->schema([
                    Select::make('tax_group')
                        ->label('Kelompok pajak')
                        ->options(AssetCategory::taxGroupOptions())
                        ->placeholder('Belum ditentukan'),
                    TextInput::make('useful_life_months')
                        ->label('Masa manfaat (bulan)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(1200)
                        ->placeholder('Ikut kelompok pajak')
                        ->helperText('Kosongkan untuk mengikuti kelompok pajak. 48 berarti 4 tahun.'),
                    Select::make('depreciation_method')
                        ->label('Metode penyusutan')
                        ->options(AssetCategory::DEPRECIATION_METHODS)
                        ->default('garis_lurus')
                        ->required()
                        ->helperText('Tersimpan sebagai kebijakan. Belum ada perhitungan yang memakainya.'),
                    Toggle::make('requires_certificate')
                        ->label('Butuh nomor sertifikat')
                        ->helperText('Nyalakan untuk kategori tanah dan bangunan. Aset di kategori ini akan menampilkan kolom nomor sertifikat tanah dan bangunan.'),
                    TextInput::make('residual_percent')
                        ->label('Nilai sisa (persen)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(100),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Kategori')
                    ->description(fn (AssetCategory $record): string => $record->pickerLabel())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_asset')
                    ->label('Akun aset tetap')
                    ->placeholder('Belum diisi, kategori belum bisa dipakai')
                    ->badge()
                    ->color(fn (AssetCategory $record): string => match (true) {
                        ! $record->isUsable() => 'danger',
                        str_contains((string) $record->description, '[COA CONTOH]') => 'warning',
                        default => 'success',
                    })
                    ->tooltip(fn (AssetCategory $record): ?string => str_contains((string) $record->description, '[COA CONTOH]')
                        ? 'Nomor akun contoh untuk demo, ganti sebelum dipakai sungguhan'
                        : null)
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('assets_count')
                    ->label('Jumlah aset')
                    ->counts('assets')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('tax_group')
                    ->label('Kelompok pajak')
                    ->formatStateUsing(fn (AssetCategory $record): string => $record->taxGroupLabel())
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('useful_life_months')
                    ->label('Masa manfaat')
                    ->formatStateUsing(fn (?int $state): string => $state ? $state.' bulan' : 'Tidak disusutkan')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada kategori aset')
            ->emptyStateDescription('Jalankan php artisan db:seed --class=AssetCategorySeeder untuk mengisi daftar kelas aset standar, lalu isi nomor akun COA masing masing bersama tim finance.');
    }

    public static function canDelete(Model $record): bool
    {
        // Menghapus kategori yang sudah dipakai akan memutus kode aset yang terlanjur dicetak.
        if ($record->assets()->exists()) {
            return false;
        }

        return static::allows('delete');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetCategories::route('/'),
        ];
    }
}
