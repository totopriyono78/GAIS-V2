<?php

namespace App\Filament\Resources\Vendors;

use App\Filament\Resources\Vendors\Pages\ListVendors;
use App\Models\Vendor;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Rekanan: tukang servis, bengkel, dan pemasok.
 *
 * Dibuat sebagai data induk sejak awal, bukan kolom teks bebas di perintah kerja, karena
 * nama yang diketik bebas akan bercabang menjadi beberapa ejaan dalam hitungan bulan dan
 * pertanyaan "berapa yang sudah kita bayar ke vendor ini" jadi tidak bisa dijawab.
 */
class VendorResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Vendor::class;

    protected static string $moduleCode = 'vendors';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?string $navigationLabel = 'Rekanan';

    protected static ?string $modelLabel = 'rekanan';

    protected static ?string $pluralModelLabel = 'rekanan';

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas rekanan')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true)
                        ->placeholder('Contoh: VND-AC-01')
                        ->helperText('Dipakai di daftar pilihan dan laporan. Kode pendek lebih mudah dicari daripada nama panjang.'),
                    TextInput::make('name')
                        ->label('Nama rekanan')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('Contoh: PT Sejuk Abadi Teknik'),
                    Select::make('type')
                        ->label('Jenis')
                        ->options(Vendor::TYPES)
                        ->default('jasa')
                        ->required()
                        ->helperText('Rekanan barang tidak muncul di pemilih pelaksana perintah kerja.'),
                    TextInput::make('specialization')
                        ->label('Bidang')
                        ->maxLength(200)
                        ->placeholder('Contoh: AC dan pendingin ruangan'),
                ]),

            Section::make('Kontak')
                ->columns(2)
                ->schema([
                    TextInput::make('contact_person')
                        ->label('Nama yang dihubungi')
                        ->maxLength(150),
                    TextInput::make('phone')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(50),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(150),
                    TextInput::make('tax_number')
                        ->label('NPWP')
                        ->maxLength(40)
                        ->placeholder('Belum ada'),
                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Catatan')
                ->schema([
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(1000)
                        ->placeholder('Contoh: respons cepat, tetapi harga di atas rata rata.'),
                    Toggle::make('is_active')
                        ->label('Masih dipakai')
                        ->default(true)
                        ->helperText('Rekanan yang tidak dipakai lagi tetap tersimpan supaya riwayat pekerjaannya masih terbaca, tetapi tidak muncul di pemilih.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Rekanan')
                    ->description(fn (Vendor $record): ?string => $record->specialization)
                    ->searchable(['name', 'specialization'])
                    ->sortable()
                    ->wrap(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->state(fn (Vendor $record): string => $record->typeLabel()),
                TextColumn::make('contact_person')
                    ->label('Kontak')
                    ->description(fn (Vendor $record): ?string => $record->phone)
                    ->placeholder('Belum diisi')
                    ->searchable(['contact_person', 'phone']),
                TextColumn::make('work_orders_count')
                    ->label('Pekerjaan')
                    ->counts('workOrders')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Dipakai')
                    ->badge()
                    ->state(fn (Vendor $record): string => $record->is_active ? 'Ya' : 'Tidak')
                    ->color(fn (Vendor $record): string => $record->is_active ? 'success' : 'gray'),
            ])
            ->defaultSort('code')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(Vendor::TYPES),
                TernaryFilter::make('is_active')
                    ->label('Masih dipakai')
                    ->placeholder('Semua')
                    ->trueLabel('Hanya yang dipakai')
                    ->falseLabel('Hanya yang tidak dipakai'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->emptyStateHeading('Belum ada rekanan')
            ->emptyStateDescription('Tambahkan tukang servis, bengkel, atau pemasok yang biasa mengerjakan pemeliharaan. Nama mereka akan muncul sebagai pilihan pelaksana saat perintah kerja dibuat.');
    }

    public static function getPages(): array
    {
        // Satu halaman saja. Menambah dan mengubah rekanan dilakukan lewat kotak,
        // sama seperti lokasi dan departemen: formulirnya pendek, dan berpindah halaman
        // untuk mengisi delapan kolom hanya membuat orang kehilangan konteks daftarnya.
        return [
            'index' => ListVendors::route('/'),
        ];
    }
}
