<?php

namespace App\Filament\Resources\Vehicles;

use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Vehicles\Pages\ListVehicles;
use App\Filament\Resources\Vehicles\Pages\ViewVehicle;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Kendaraan dinas.
 *
 * Layar ini dibuka untuk satu pertanyaan yang berulang setiap bulan: mana yang pajaknya,
 * KIR-nya, atau asuransinya segera habis. Karena itu kolom pertama setelah nomor polisi
 * adalah keadaan dokumen, bukan merek atau tahun, dan daftar diurutkan dari yang paling
 * mendesak, bukan menurut abjad pelat.
 *
 * Data pokok kendaraan tetap tinggal di aset: nama, kategori, harga perolehan, lokasi,
 * penanggung jawab, penyusutan, riwayat perbaikan, dan pelepasan. Yang diisi di sini
 * hanya keterangan yang khas kendaraan.
 */
class VehicleResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Vehicle::class;

    protected static string $moduleCode = 'vehicles';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|UnitEnum|null $navigationGroup = 'Kendaraan';

    protected static ?string $navigationLabel = 'Kendaraan dinas';

    protected static ?string $modelLabel = 'kendaraan';

    protected static ?string $pluralModelLabel = 'kendaraan';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'plate_number';

    /** Jumlah kendaraan yang dokumennya sudah lewat atau segera habis. */
    public static function getNavigationBadge(): ?string
    {
        $jumlah = Vehicle::query()->aktif()->dokumenJatuhTempo(30)->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Vehicle::query()->aktif()->dokumenJatuhTempo(0)->exists() ? 'danger' : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Aset yang menjadi kendaraan ini')
                ->columns(2)
                ->description('Kendaraan didaftarkan dulu sebagai aset, lalu diberi keterangan kendaraan di sini. Dengan begitu penyusutan, riwayat perbaikan, dan pelepasannya memakai jalur yang sama dengan aset lain.')
                ->schema([
                    Select::make('asset_id')
                        ->label('Aset')
                        ->options(fn (?Vehicle $record): array => Asset::query()
                            ->where('status', '!=', 'dilepas')
                            // Aset yang sudah jadi kendaraan lain tidak boleh dipilih lagi,
                            // kecuali aset milik baris yang sedang diubah.
                            ->where(fn (Builder $query) => $query
                                ->whereDoesntHave('vehicle')
                                ->when($record?->asset_id, fn (Builder $q, $id) => $q->orWhere('id', $id)))
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (Asset $a) => [$a->id => $a->code.' '.$a->name])
                            ->all())
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->helperText('Belum terdaftar sebagai aset? Daftarkan dulu lewat menu Daftar aset, lalu kembali ke sini.'),
                    TextInput::make('plate_number')
                        ->label('Nomor polisi')
                        ->required()
                        ->maxLength(20)
                        ->unique(ignoreRecord: true)
                        ->placeholder('B 1234 XYZ')
                        ->helperText('Spasi dirapikan otomatis, jadi B1234XYZ dan B 1234 XYZ tersimpan sama.'),
                    Select::make('vehicle_type')
                        ->label('Jenis kendaraan')
                        ->options(Vehicle::TYPES)
                        ->required()
                        ->live(),
                ]),

            Section::make('Cara dipakai')
                ->columns(2)
                ->schema([
                    Select::make('usage_mode')
                        ->label('Pemakaian')
                        ->options(Vehicle::USAGE_MODES)
                        ->default('pool')
                        ->required()
                        ->live()
                        ->helperText('Menentukan layar mana yang mengurusnya nanti. Kendaraan pool masuk daftar pemesanan, kendaraan pegangan tidak.'),
                    Select::make('default_driver_employee_id')
                        ->label('Sopir tetap')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Tidak ada sopir tetap')
                        ->helperText('Kosongkan untuk kendaraan yang disopiri sendiri oleh pemakainya.'),
                    Toggle::make('is_active')
                        ->label('Masih dipakai')
                        ->default(true)
                        ->columnSpanFull()
                        ->helperText('Kendaraan yang dimatikan tidak lagi diingatkan jatuh tempo dokumennya, tetapi riwayatnya tetap tersimpan. Untuk kendaraan yang benar benar dijual, pakai dokumen pelepasan aset.'),
                ]),

            Section::make('Keterangan teknis')
                ->columns(3)
                ->description('Nomor rangka dan mesin ini yang diminta Samsat saat perpanjangan, dan yang dicocokkan polisi saat kendaraan diperiksa.')
                ->schema([
                    TextInput::make('chassis_number')
                        ->label('Nomor rangka')
                        ->maxLength(50)
                        ->placeholder('Tidak dicatat'),
                    TextInput::make('engine_number')
                        ->label('Nomor mesin')
                        ->maxLength(50)
                        ->placeholder('Tidak dicatat'),
                    TextInput::make('production_year')
                        ->label('Tahun pembuatan')
                        ->numeric()
                        ->minValue(1950)
                        ->maxValue((int) now()->addYear()->format('Y'))
                        ->placeholder('Tidak dicatat'),
                    TextInput::make('color')
                        ->label('Warna')
                        ->maxLength(40)
                        ->placeholder('Tidak dicatat'),
                    Select::make('fuel_type')
                        ->label('Bahan bakar')
                        ->options(Vehicle::FUEL_TYPES)
                        ->placeholder('Tidak dicatat'),
                    Select::make('transmission')
                        ->label('Transmisi')
                        ->options(Vehicle::TRANSMISSIONS)
                        ->placeholder('Tidak dicatat'),
                    TextInput::make('seat_capacity')
                        ->label('Jumlah tempat duduk')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(60)
                        ->placeholder('Tidak dicatat')
                        ->visible(fn ($get): bool => in_array($get('vehicle_type'), ['mobil_penumpang', 'mobil_operasional', 'lainnya'], true)),
                    TextInput::make('payload_kg')
                        ->label('Daya angkut')
                        ->numeric()
                        ->minValue(1)
                        ->suffix('kg')
                        ->placeholder('Tidak dicatat')
                        ->visible(fn ($get): bool => in_array($get('vehicle_type'), ['pikap', 'truk'], true)),
                ]),

            Section::make('Odometer')
                ->columns(2)
                ->description('Diisi tangan hanya saat pendataan awal. Setelah log perjalanan dan pengisian BBM dibangun, angka ini bergerak sendiri dari catatan itu.')
                ->schema([
                    TextInput::make('last_odometer_km')
                        ->label('Odometer terakhir')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('km')
                        ->placeholder('Belum dicatat'),
                    DatePicker::make('last_odometer_date')
                        ->label('Tanggal pembacaan')
                        ->displayFormat('d M Y')
                        ->maxDate(now())
                        ->placeholder('Belum dicatat'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: ban serep tidak ada sejak Agustus 2026.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kendaraan')
                ->columns(3)
                ->schema([
                    TextEntry::make('plate_number')
                        ->label('Nomor polisi')
                        ->fontFamily('mono'),
                    TextEntry::make('vehicle_type')
                        ->label('Jenis')
                        ->state(fn (Vehicle $record): string => $record->typeLabel()),
                    TextEntry::make('usage_mode')
                        ->label('Pemakaian')
                        ->state(fn (Vehicle $record): string => $record->usageLabel()),
                    TextEntry::make('asset.code')
                        ->label('Aset')
                        ->state(fn (Vehicle $record): string => trim(($record->asset?->code ?? '').' '.($record->asset?->name ?? ''))
                            ?: 'Tidak tercatat')
                        ->url(fn (Vehicle $record): ?string => $record->asset
                            ? AssetResource::getUrl('edit', ['record' => $record->asset])
                            : null),
                    TextEntry::make('asset.custodian.full_name')
                        ->label('Penanggung jawab')
                        ->placeholder('Belum diisi'),
                    TextEntry::make('defaultDriver.full_name')
                        ->label('Sopir tetap')
                        ->placeholder('Tidak ada sopir tetap'),
                    TextEntry::make('spesifikasi')
                        ->label('Keterangan teknis')
                        ->columnSpanFull()
                        ->state(function (Vehicle $record): string {
                            $bagian = array_filter([
                                $record->namaLengkap(),
                                $record->color,
                                $record->fuel_type ? $record->fuelLabel() : null,
                                $record->transmission ? Vehicle::TRANSMISSIONS[$record->transmission] : null,
                                $record->seat_capacity ? $record->seat_capacity.' tempat duduk' : null,
                                $record->payload_kg ? number_format($record->payload_kg, 0, ',', '.').' kg daya angkut' : null,
                            ]);

                            return $bagian === [] ? 'Belum dicatat' : implode(', ', $bagian);
                        }),
                    TextEntry::make('chassis_number')
                        ->label('Nomor rangka')
                        ->fontFamily('mono')
                        ->placeholder('Tidak dicatat'),
                    TextEntry::make('engine_number')
                        ->label('Nomor mesin')
                        ->fontFamily('mono')
                        ->placeholder('Tidak dicatat'),
                    TextEntry::make('last_odometer_km')
                        ->label('Odometer')
                        ->state(fn (Vehicle $record): string => $record->odometerLabel()),
                    TextEntry::make('konsumsi')
                        ->label('Konsumsi bahan bakar')
                        ->state(fn (Vehicle $record): string => $record->konsumsiLabel())
                        ->helperText('Rata rata dari seluruh penggal antar dua pengisian penuh.'),
                    TextEntry::make('foto')
                        ->label('Foto')
                        ->state(function (Vehicle $record): string {
                            $jumlah = $record->photos()->count();

                            if ($jumlah === 0) {
                                return 'Belum ada foto';
                            }

                            $kerusakan = $record->photos()->where('type', 'kerusakan')->count();

                            return $jumlah.' foto'
                                .($kerusakan > 0 ? ', '.$kerusakan.' di antaranya foto kerusakan' : '');
                        }),
                    TextEntry::make('notes')
                        ->label('Catatan')
                        ->columnSpanFull()
                        ->visible(fn (Vehicle $record): bool => filled($record->notes)),
                ]),

            Section::make('Dokumen yang berlaku')
                ->columns(2)
                ->description('Diambil dari riwayat di bawah: untuk tiap jenis, yang tanggal berakhirnya paling jauh.')
                ->schema([
                    TextEntry::make('ringkasan_dokumen')
                        ->label('Keadaan sekarang')
                        ->columnSpanFull()
                        ->state(function (Vehicle $record): string {
                            $berlaku = $record->dokumenBerlakuSemua();

                            if ($berlaku === []) {
                                return 'Belum ada dokumen yang dicatat. Tambahkan pajak tahunan, KIR, dan asuransinya di daftar di bawah supaya jatuh temponya ikut diingatkan.';
                            }

                            $baris = [];

                            foreach (VehicleDocument::TYPES as $kunci => $nama) {
                                if (! isset($berlaku[$kunci])) {
                                    continue;
                                }

                                $dokumen = $berlaku[$kunci];
                                $baris[] = $nama.': berakhir '.$dokumen->expires_at->translatedFormat('d F Y')
                                    .', '.strtolower($dokumen->keteranganWaktu());
                            }

                            return implode(".\n", $baris).'.';
                        }),
                    TextEntry::make('biaya_dokumen_setahun')
                        ->label('Biaya dokumen 12 bulan terakhir')
                        // Rp 0 dan "belum ada yang dicatat" adalah dua hal berbeda, dan
                        // menampilkan yang pertama untuk yang kedua adalah cara halus
                        // mengarang angka.
                        ->state(function (Vehicle $record): string {
                            $adaBiaya = $record->documents()
                                ->where('issued_date', '>=', now()->subYear())
                                ->whereNotNull('cost')
                                ->exists();

                            if (! $adaBiaya) {
                                return 'Belum ada biaya yang dicatat';
                            }

                            return Rupiah::penuh((float) $record->documents()
                                ->where('issued_date', '>=', now()->subYear())
                                ->sum('cost'));
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plate_number')
                    ->label('Nomor polisi')
                    ->fontFamily('mono')
                    ->description(fn (Vehicle $record): string => $record->namaLengkap())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('keadaan')
                    ->label('Dokumen terdekat')
                    ->state(fn (Vehicle $record): string => $record->keadaanLabel())
                    ->color(fn (Vehicle $record): string => $record->keadaanColor())
                    ->description(fn (Vehicle $record): ?string => $record->jatuhTempoTerdekat()
                        ?->expires_at->translatedFormat('d F Y'))
                    ->wrap(),
                TextColumn::make('vehicle_type')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->state(fn (Vehicle $record): string => $record->typeLabel())
                    ->sortable(),
                TextColumn::make('usage_mode')
                    ->label('Pemakaian')
                    ->state(fn (Vehicle $record): string => $record->usageLabelSingkat())
                    ->sortable(),
                TextColumn::make('asset.custodian.full_name')
                    ->label('Penanggung jawab')
                    ->placeholder('Belum diisi')
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('defaultDriver.full_name')
                    ->label('Sopir tetap')
                    ->placeholder('Tidak ada')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_odometer_km')
                    ->label('Odometer')
                    ->state(fn (Vehicle $record): string => $record->odometerLabel())
                    ->alignEnd()
                    ->toggleable(),
                TextColumn::make('documents_count')
                    ->label('Dokumen')
                    ->counts('documents')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            /*
             * Diurutkan dari dokumen yang paling dekat jatuh tempo, bukan menurut pelat.
             * Layar ini dibuka untuk mencari yang perlu diurus bulan ini, dan urutan abjad
             * pelat tidak menjawab pertanyaan itu sama sekali.
             */
            ->defaultSort('plate_number')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['asset.custodian', 'defaultDriver', 'documents'])
                ->orderByRaw(
                    'coalesce(('
                    .' select min(d.expires_at) from vehicle_documents d'
                    .' where d.vehicle_id = vehicles.id'
                    .' and d.expires_at = ('
                    .'   select max(d2.expires_at) from vehicle_documents d2'
                    .'   where d2.vehicle_id = d.vehicle_id and d2.type = d.type)'
                    .'), \'9999-12-31\') asc'
                ))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('jatuh_tempo')
                    ->label('Dokumen jatuh tempo 30 hari')
                    ->query(fn (Builder $query): Builder => $query->dokumenJatuhTempo(30)),
                Filter::make('terlambat')
                    ->label('Dokumen sudah lewat')
                    ->query(fn (Builder $query): Builder => $query->dokumenJatuhTempo(-1)),
                SelectFilter::make('vehicle_type')
                    ->label('Jenis')
                    ->options(Vehicle::TYPES)
                    ->multiple(),
                SelectFilter::make('usage_mode')
                    ->label('Pemakaian')
                    ->options(Vehicle::USAGE_MODES),
                TernaryFilter::make('is_active')
                    ->label('Status pemakaian')
                    ->placeholder('Semua')
                    ->trueLabel('Masih dipakai')
                    ->falseLabel('Sudah tidak dipakai'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (Vehicle $record): bool => static::canDelete($record))
                    ->modalDescription('Yang dihapus hanya keterangan kendaraannya beserta dokumen pajak, KIR, dan asuransinya. Asetnya sendiri tetap ada di Daftar aset.'),
            ])
            ->emptyStateHeading('Belum ada kendaraan yang didata')
            ->emptyStateDescription('Kendaraan didaftarkan dulu sebagai aset di menu Daftar aset, lalu diberi keterangan kendaraan di sini: nomor polisi, nomor rangka, dan dokumen pajaknya. Setelah itu jatuh tempo pajak dan KIR ikut diingatkan di menu ini.');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\TripsRelationManager::class,
            RelationManagers\RefuelingsRelationManager::class,
            RelationManagers\PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicles::route('/'),
            'view' => ViewVehicle::route('/{record}'),
        ];
    }
}
