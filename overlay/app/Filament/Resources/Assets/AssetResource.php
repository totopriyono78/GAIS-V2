<?php

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Filament\Resources\Assets\RelationManagers\DepreciationRelationManager;
use App\Filament\Resources\Assets\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Assets\RelationManagers\MaintenanceRelationManager;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Location;
use App\Models\MaintenanceSchedule;
use App\Models\Vendor;
use App\Services\PenyusutanAset;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use App\Support\Periode;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AssetResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = Asset::class;

    protected static string $moduleCode = 'assets';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $navigationLabel = 'Assets';

    protected static ?string $modelLabel = 'asset';

    protected static ?string $pluralModelLabel = 'assets';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asset Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode aset')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan')
                        ->helperText('Kode dibentuk dari kode departemen, nomor akun COA kategori, tahun perolehan, dan nomor urut. Contohnya FIN-1201-2026-0001. Kode ini yang dicetak jadi barcode.'),
                    TextInput::make('name')
                        ->label('Nama aset')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('Contoh: Laptop Dell Latitude 5440'),
                    Select::make('asset_category_id')
                        ->label('Kategori')
                        ->options(fn (): array => AssetCategory::query()
                            ->where('is_active', true)
                            ->whereNotNull('account_asset')
                            ->where('account_asset', '!=', '')
                            ->orderBy('code')
                            ->get()
                            // Nomor akun di depan, karena itu yang dipakai finance mencocokkan,
                            // dan urutannya sama dengan yang tercetak di label barang.
                            ->mapWithKeys(fn (AssetCategory $category) => [
                                $category->id => $category->pickerLabel(),
                            ])
                            ->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->helperText('Nomor akun kategori jadi segmen kedua kode aset. Kategori yang nomor akunnya belum diisi tidak muncul di sini, isi dulu di menu Kategori aset.'),
                    Select::make('asset_type')
                        ->label('Jenis')
                        ->options(Asset::TYPES)
                        ->default('bergerak')
                        ->required()
                        ->helperText('Aset bergerak bisa berpindah lokasi dan pemegang. Aset tetap melekat pada bangunan.'),
                    TextInput::make('brand')
                        ->label('Merek')
                        ->maxLength(100),
                    TextInput::make('model')
                        ->label('Model atau tipe')
                        ->maxLength(100),
                    TextInput::make('serial_number')
                        ->label('Nomor seri')
                        ->maxLength(100)
                        ->helperText('Nomor dari pabrik, berbeda dari kode aset GAIS.'),
                ]),

            Section::make('Placement')
                ->columns(2)
                ->schema([
                    Select::make('location_id')
                        ->label('Lokasi')
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
                    Select::make('custodian_employee_id')
                        ->label('Penanggung jawab')
                        ->relationship('custodian', 'full_name', fn (Builder $query) => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->placeholder('Belum ditentukan'),
                    Select::make('department_id')
                        ->label('Departemen pemakai')
                        ->options(fn (): array => Department::query()
                            ->where('is_active', true)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (Department $department) => [
                                $department->id => $department->code.' '.$department->name,
                            ])
                            ->all())
                        ->searchable()
                        ->required()
                        ->helperText('Kodenya jadi segmen pertama kode aset, dan dipakai untuk membebankan biaya pemeliharaan serta penyusutan.'),
                    Select::make('status')
                        ->label('Status')
                        /*
                         * Sudah dilepas sengaja dibuang dari pilihan. Sejak ada layar
                         * Pelepasan aset, status itu adalah akibat dari satu peristiwa
                         * yang ada tanggal, cara, dan dokumennya, bukan label yang bisa
                         * dipilih siapa saja di sini. Aset yang memang sudah dilepas
                         * tetap menampilkan statusnya, tetapi tidak bisa diubah dari sini.
                         */
                        ->options(fn (?Asset $record): array => $record?->status === 'dilepas'
                            ? Asset::STATUSES
                            : array_diff_key(Asset::STATUSES, ['dilepas' => null]))
                        ->disabled(fn (?Asset $record): bool => $record?->status === 'dilepas')
                        ->dehydrated(fn (?Asset $record): bool => $record?->status !== 'dilepas')
                        ->default('aktif')
                        ->required()
                        ->helperText(fn (?Asset $record): string => $record?->status === 'dilepas'
                            ? 'Aset ini sudah dilepas. Untuk mengembalikannya, batalkan dokumen pelepasannya di menu Pelepasan aset.'
                            : 'Untuk melepas aset, pakai menu Pelepasan aset supaya tanggal, cara, dan dokumennya ikut tercatat.'),
                    Select::make('condition')
                        ->label('Kondisi fisik')
                        ->options(Asset::CONDITIONS)
                        ->default('baik')
                        ->required(),
                ]),

            Section::make('Ownership')
                ->description('Barang sewaan tetap perlu dicatat karena ikut dipakai dan ikut diperiksa saat stock opname, tetapi bukan milik perusahaan.')
                ->columns(3)
                ->schema([
                    Radio::make('ownership_type')
                        ->label('Status kepemilikan')
                        ->options(Asset::OWNERSHIPS)
                        ->default('milik')
                        ->required()
                        ->live()
                        ->columnSpanFull()
                        ->helperText('Aset sewaan tidak disusutkan, karena yang disusutkan hanya barang milik sendiri.'),
                    DatePicker::make('lease_start_date')
                        ->label('Sewa mulai')
                        ->displayFormat('d M Y')
                        ->visible(fn ($get): bool => $get('ownership_type') === 'sewa')
                        ->required(fn ($get): bool => $get('ownership_type') === 'sewa'),
                    DatePicker::make('lease_end_date')
                        ->label('Sewa sampai')
                        ->displayFormat('d M Y')
                        ->visible(fn ($get): bool => $get('ownership_type') === 'sewa')
                        ->required(fn ($get): bool => $get('ownership_type') === 'sewa')
                        ->afterOrEqual('lease_start_date')
                        ->helperText('Dipakai untuk mengingatkan sewa yang akan habis di dasbor.'),
                    TextInput::make('lease_contract_number')
                        ->label('Nomor kontrak sewa')
                        ->maxLength(100)
                        ->visible(fn ($get): bool => $get('ownership_type') === 'sewa')
                        ->placeholder('Belum ada'),
                    TextInput::make('lessor')
                        ->label('Pemberi sewa')
                        ->maxLength(150)
                        ->visible(fn ($get): bool => $get('ownership_type') === 'sewa')
                        ->columnSpan(2)
                        ->placeholder('Nama perusahaan atau perorangan'),
                ]),

            Section::make('Acquisition & Value')
                ->columns(3)
                ->schema([
                    DatePicker::make('acquisition_date')
                        ->label('Tanggal perolehan')
                        ->displayFormat('d M Y')
                        ->helperText('Tahun dari tanggal ini dipakai di kode aset.'),
                    Select::make('acquisition_source')
                        ->label('Sumber perolehan')
                        // Sewa dikeluarkan dari pilihan sejak kepemilikan punya kolom sendiri.
                        // Aset lama yang terlanjur memakainya tetap menampilkan labelnya.
                        ->options(fn (?Asset $record): array => in_array($record?->acquisition_source, Asset::LEGACY_SOURCES, true)
                            ? Asset::SOURCES
                            : array_diff_key(Asset::SOURCES, array_flip(Asset::LEGACY_SOURCES)))
                        ->default('pembelian')
                        ->required()
                        ->live(),
                    Radio::make('acquisition_condition')
                        ->label('Kondisi saat diperoleh')
                        ->options(Asset::ACQUISITION_CONDITIONS)
                        ->default('baru')
                        ->helperText('Barang bekas biasanya umur ekonomisnya lebih pendek dari bawaan kategori.'),
                    TextInput::make('project_name')
                        ->label('Nama proyek')
                        ->maxLength(150)
                        ->visible(fn ($get): bool => $get('acquisition_source') === 'proyek')
                        ->required(fn ($get): bool => $get('acquisition_source') === 'proyek')
                        ->columnSpan(2)
                        ->helperText('Proyek yang menjadi asal barang ini, supaya bisa ditelusuri saat proyeknya diaudit.'),
                    TextInput::make('acquisition_cost')
                        ->label('Nilai perolehan')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->prefix('Rp'),
                    // Dikosongkan berarti mengikuti kebijakan penyusutan, sama seperti
                    // umur ekonomis yang kosong berarti mengikuti kategori. Bawaan nol
                    // yang dulu dipakai membuat kebijakan "ikut kategori" tidak pernah
                    // bisa berlaku, karena nol terbaca sebagai jawaban yang disengaja.
                    TextInput::make('residual_value')
                        ->label('Nilai sisa')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('Ikut kebijakan')
                        ->helperText('Perkiraan nilai di akhir umur ekonomis. Kosongkan untuk mengikuti kebijakan penyusutan di menu Pengaturan.'),
                    TextInput::make('useful_life_months')
                        ->label('Umur ekonomis (bulan)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(1200)
                        ->placeholder('Ikut kategori')
                        ->helperText('Kosongkan kalau mengikuti bawaan kategori.'),
                ]),

            /*
             * Seksi ini hanya muncul pada aset yang sudah tersimpan, karena seluruh
             * isinya dihitung dari data aset itu sendiri. Pada formulir tambah, belum
             * ada yang bisa dihitung, dan kotak kosong berisi tanda hubung hanya akan
             * membuat orang mengira ada yang perlu diisi.
             */
            Section::make('Depreciation')
                // Selebar layar penuh karena tampilnya bersyarat. Kartu yang kadang ada
                // dan kadang tidak akan mengubah jumlah kartu per baris, dan begitu
                // jumlahnya ganjil ada satu kartu yang berdiri sendiri dengan separuh
                // layar kosong di sebelahnya.
                ->columnSpanFull()
                ->columns(3)
                ->visible(fn (?Asset $record): bool => $record?->exists ?? false)
                ->description('Angka di bawah dihitung ulang setiap kali halaman ini dibuka, kecuali akumulasi yang sudah dibekukan oleh periode yang ditutup.')
                ->schema([
                    /*
                     * Seluruh penutup di bawah menerima aset yang boleh kosong, walaupun
                     * seksinya sudah disembunyikan pada formulir tambah. Menyandarkan
                     * keselamatan pada urutan pemanggilan Filament berarti bertaruh pada
                     * hal yang bisa berubah di versi berikutnya, dan taruhannya adalah
                     * layar tambah aset yang gagal terbuka sama sekali.
                     */
                    Placeholder::make('penyusutan_metode')
                        ->label('Metode dan umur')
                        ->content(function (?Asset $record): string {
                            if ($record === null || ! $record->exists) {
                                return 'Tersedia setelah aset disimpan.';
                            }

                            $mesin = app(PenyusutanAset::class);
                            $alasan = $mesin->alasanTidakDisusutkan($record);

                            if ($alasan !== null) {
                                return 'Tidak disusutkan. '.$alasan.'.';
                            }

                            return (AssetCategory::DEPRECIATION_METHODS[$mesin->metode($record)] ?? $mesin->metode($record))
                                .', '.$mesin->umurBulan($record).' bulan';
                        }),
                    Placeholder::make('penyusutan_akumulasi')
                        ->label('Akumulasi penyusutan')
                        ->content(fn (?Asset $record): string => $record?->exists
                            ? Rupiah::penuh(app(PenyusutanAset::class)->akumulasi($record))
                            : 'Belum ada'),
                    Placeholder::make('penyusutan_nilai_buku')
                        ->label('Nilai buku')
                        ->content(fn (?Asset $record): string => $record?->exists
                            ? Rupiah::penuh(app(PenyusutanAset::class)->nilaiBuku($record))
                            : 'Belum ada'),
                    Placeholder::make('penyusutan_terakhir')
                        ->label('Dihitung sampai')
                        ->columnSpanFull()
                        ->content(function (?Asset $record): string {
                            if ($record === null || ! $record->exists) {
                                return 'Tersedia setelah aset disimpan.';
                            }

                            $entri = app(PenyusutanAset::class)->entriTerakhir($record);

                            if ($entri === null) {
                                return 'Belum ada periode yang ditutup untuk aset ini. Angka akumulasi di atas adalah akumulasi awal, yaitu hitungan sampai bulan sebelum aplikasi ini mulai menghitung.';
                            }

                            return Periode::label($entri->period).', beban bulan itu '
                                .Rupiah::penuh((float) $entri->expense).'.';
                        }),
                    TextInput::make('opening_accumulated_depreciation')
                        ->label('Akumulasi awal')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('Hitung sendiri dari jadwal')
                        ->helperText('Isi hanya kalau buku Anda menyebut angka yang berbeda dari hitungan jadwal, misalnya karena aset ini pernah direvaluasi. Dikosongkan berarti dihitung sendiri.'),
                    Textarea::make('opening_depreciation_note')
                        ->label('Alasan akumulasi awal diisi tangan')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpan(2)
                        ->placeholder('Contoh: mengikuti kertas kerja audit 2025.'),
                ]),

            Section::make('Warranty')
                ->columns(3)
                ->schema([
                    Radio::make('has_warranty')
                        ->label('Bergaransi')
                        ->boolean('Ya', 'Tidak')
                        ->default(false)
                        ->required()
                        ->live()
                        ->columnSpanFull(),
                    DatePicker::make('warranty_from')
                        ->label('Garansi mulai')
                        ->displayFormat('d M Y')
                        ->visible(fn ($get): bool => (bool) $get('has_warranty'))
                        ->required(fn ($get): bool => (bool) $get('has_warranty')),
                    DatePicker::make('warranty_until')
                        ->label('Garansi sampai')
                        ->displayFormat('d M Y')
                        ->visible(fn ($get): bool => (bool) $get('has_warranty'))
                        ->required(fn ($get): bool => (bool) $get('has_warranty'))
                        ->afterOrEqual('warranty_from')
                        ->helperText('Dipakai untuk mengingatkan garansi yang akan habis di dasbor.'),
                ]),

            Section::make('Certificate')
                // Sama seperti Penyusutan: tampilnya bersyarat, jadi selebar layar penuh.
                ->columnSpanFull()
                ->description('Hanya berlaku untuk kategori tanah dan bangunan. Penandanya diatur per kategori di menu Kategori aset, jadi kategori baru bisa ikut memunculkan seksi ini tanpa mengubah kode.')
                ->columns(2)
                ->visible(fn ($get): bool => AssetCategory::query()->whereKey($get('asset_category_id'))->value('requires_certificate') == true)
                ->schema([
                    TextInput::make('land_certificate_number')
                        ->label('Nomor sertifikat tanah')
                        ->maxLength(100)
                        ->placeholder('Contoh: SHM 1234/Kelurahan'),
                    TextInput::make('building_certificate_number')
                        ->label('Nomor sertifikat bangunan')
                        ->maxLength(100)
                        ->placeholder('Contoh: IMB atau PBG 5678'),
                ]),

            Section::make('Notes & Photos')
                ->collapsed()
                ->columns(2)
                ->schema([
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(3)
                        ->columnSpanFull(),
                    FileUpload::make('photo_path')
                        ->label('Foto aset')
                        ->image()
                        ->disk('public')
                        ->directory('foto-aset')
                        ->visibility('public')
                        ->maxSize(4096)
                        ->imagePreviewHeight('180')
                        ->openable()
                        ->downloadable()
                        ->helperText('Opsional, berguna saat mencocokkan barang di lapangan dengan datanya. '
                            .'Untuk mengganti foto, hapus dulu yang lama lewat tanda silang di pojok gambar, lalu unggah yang baru.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->disk('public')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('code')
                    ->label('Kode aset')
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('Kode aset disalin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn (Asset $record): ?string => trim(($record->brand ?? '').' '.($record->model ?? '')) ?: null)
                    ->searchable(['name', 'brand', 'model', 'serial_number'])
                    ->sortable()
                    ->wrap(),
                // Satu kolom kategori saja, dengan bentuk yang sama seperti di pemilih
                // kategori dan di berita acara: nomor akun, lalu namanya. Nomor akunnya
                // tidak lagi ditulis terpisah sebagai keterangan di bawah, karena sudah
                // ada di depan namanya. Diurutkan menurut nama kategori, bukan menurut
                // teks gabungannya, supaya urutannya tetap seperti yang orang harapkan.
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->formatStateUsing(fn (Asset $record): string => $record->category?->pickerLabel() ?? 'Belum diisi')
                    ->wrap()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('department.code')
                    ->label('Departemen')
                    ->description(fn (Asset $record): ?string => $record->department?->name)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->description(fn (Asset $record): ?string => $record->location?->code)
                    ->placeholder('Belum ditentukan')
                    ->sortable(),
                TextColumn::make('custodian.full_name')
                    ->label('Penanggung jawab')
                    ->placeholder('Belum ditentukan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Asset::STATUSES[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'aktif' => 'success',
                        'dilepas' => 'gray',
                        'perbaikan' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('condition')
                    ->label('Kondisi')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Asset::CONDITIONS[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'baik' => 'success',
                        'perlu_perbaikan' => 'warning',
                        'rusak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('acquisition_cost')
                    ->label('Nilai perolehan')
                    ->money('IDR')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('acquisition_date')
                    ->label('Tanggal perolehan')
                    ->date('d M Y')
                    ->placeholder('Belum diisi')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ownership_type')
                    ->label('Kepemilikan')
                    ->badge()
                    ->color(fn (Asset $record): string => $record->isLeased() ? 'warning' : 'gray')
                    ->formatStateUsing(fn (Asset $record): string => $record->ownershipLabel())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('warranty_until')
                    ->label('Garansi sampai')
                    ->date('d M Y')
                    ->placeholder('Tidak bergaransi')
                    ->color(function (Asset $record): string {
                        $sisa = $record->warrantyDaysLeft();

                        return match (true) {
                            $sisa === null => 'gray',
                            $sisa < 0 => 'danger',
                            $sisa <= 90 => 'warning',
                            default => 'success',
                        };
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lease_end_date')
                    ->label('Sewa sampai')
                    ->date('d M Y')
                    ->placeholder('Bukan sewaan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('documents_count')
                    ->label('Dokumen')
                    ->counts('documents')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('code')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->filters([
                SelectFilter::make('asset_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Asset::STATUSES)
                    ->multiple(),
                SelectFilter::make('condition')
                    ->label('Kondisi')
                    ->options(Asset::CONDITIONS)
                    ->multiple(),
                SelectFilter::make('asset_type')
                    ->label('Jenis')
                    ->options(Asset::TYPES),
                SelectFilter::make('ownership_type')
                    ->label('Kepemilikan')
                    ->options(Asset::OWNERSHIPS),
                Filter::make('garansi_segera_habis')
                    ->label('Garansi habis dalam 3 bulan')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('has_warranty', true)
                        ->whereNotNull('warranty_until')
                        ->whereBetween('warranty_until', [now()->toDateString(), now()->addMonths(3)->toDateString()]))
                    ->toggle(),
                Filter::make('garansi_sudah_habis')
                    ->label('Garansi sudah habis')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('has_warranty', true)
                        ->whereNotNull('warranty_until')
                        ->whereDate('warranty_until', '<', now()->toDateString()))
                    ->toggle(),
                Filter::make('sewa_segera_habis')
                    ->label('Sewa habis dalam 3 bulan')
                    ->query(fn (Builder $query): Builder => $query
                        ->where('ownership_type', 'sewa')
                        ->whereNotNull('lease_end_date')
                        ->whereBetween('lease_end_date', [now()->toDateString(), now()->addMonths(3)->toDateString()]))
                    ->toggle(),
                Filter::make('tanpa_lokasi')
                    ->label('Belum punya lokasi')
                    ->query(fn (Builder $query): Builder => $query->whereNull('location_id'))
                    ->toggle(),
                Filter::make('tanpa_penanggung_jawab')
                    ->label('Belum punya penanggung jawab')
                    ->query(fn (Builder $query): Builder => $query->whereNull('custodian_employee_id'))
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('riwayat')
                    ->label('History Card')
                    ->icon('heroicon-o-clock')
                    ->iconButton()
                    ->url(fn (Asset $record): string => route('gais.aset.riwayat', ['asset' => $record->getKey()]))
                    ->openUrlInNewTab()
                    // Kartu riwayat adalah dokumen cetak, sama seperti label, jadi izinnya
                    // izin cetak. Filament tidak pernah memeriksa tindakan buatan sendiri.
                    ->visible(fn (): bool => static::canPrint()),
                Action::make('label')
                    ->label('Print Labels')
                    ->icon('heroicon-o-printer')
                    ->iconButton()
                    ->url(fn (Asset $record): string => route('gais.cetak.label-aset', ['ids' => $record->getKey()]))
                    ->openUrlInNewTab()
                    ->visible(fn (): bool => static::canPrint()),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('cetak_label')
                        ->label('Print Selected Labels')
                        ->icon('heroicon-o-printer')
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn (): bool => static::allows('print'))
                        ->action(fn (Collection $selectedRecords) => redirect()->route('gais.cetak.label-aset', [
                            'ids' => $selectedRecords->pluck('id')->take(500)->implode(','),
                        ])),
                    /*
                     * Membuat jadwal pemeliharaan untuk banyak aset sekaligus.
                     *
                     * Empat puluh AC yang diservis tiap tiga bulan berarti empat puluh
                     * jadwal, dan mengetiknya satu per satu adalah pekerjaan yang tidak
                     * akan pernah selesai. Jadwalnya tetap satu per aset supaya riwayat,
                     * vendor, dan biayanya bisa berbeda per barang; yang dipangkas hanya
                     * pengetikannya.
                     */
                    BulkAction::make('buat_jadwal_pemeliharaan')
                        ->label('Create Preventive Maintenance')
                        ->icon('heroicon-o-calendar-days')
                        ->color('primary')
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn (): bool => Auth::user()?->hasPermission('maintenance_schedules.create') ?? false)
                        ->modalHeading('Create Preventive Maintenance for Selected Assets')
                        ->modalDescription('Satu jadwal dibuat untuk tiap aset yang dipilih, dengan isi yang sama. Aset yang sudah punya jadwal bernama sama dilewati, jadi tindakan ini aman diulang.')
                        ->modalSubmitActionLabel('Create Schedule')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama pekerjaan')
                                ->required()
                                ->maxLength(200)
                                ->placeholder('Contoh: Servis rutin dan cuci AC'),
                            TextInput::make('interval_months')
                                ->label('Diulang tiap (bulan)')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(120)
                                ->default(3),
                            DatePicker::make('last_done_date')
                                ->label('Terakhir dikerjakan')
                                ->displayFormat('d M Y')
                                ->maxDate(now())
                                ->placeholder('Belum pernah')
                                ->helperText('Dikosongkan berarti seluruh jadwal langsung jatuh tempo hari ini.'),
                            Select::make('vendor_id')
                                ->label('Rekanan')
                                ->options(fn (): array => Vendor::query()
                                    ->where('is_active', true)
                                    ->doingServices()
                                    ->orderBy('code')
                                    ->get()
                                    ->mapWithKeys(fn (Vendor $v) => [$v->id => $v->pickerLabel()])
                                    ->all())
                                ->searchable()
                                ->placeholder('Dikerjakan sendiri'),
                            Textarea::make('tasks')
                                ->label('Rincian pekerjaan')
                                ->rows(3)
                                ->placeholder("Contoh:\nCuci filter dan evaporator\nPeriksa tekanan freon"),
                        ])
                        ->action(function (Collection $selectedRecords, array $data): void {
                            $dibuat = 0;
                            $dilewati = 0;

                            foreach ($selectedRecords as $aset) {
                                $sudahAda = MaintenanceSchedule::query()
                                    ->where('asset_id', $aset->getKey())
                                    ->where('name', $data['name'])
                                    ->exists();

                                if ($sudahAda) {
                                    $dilewati++;

                                    continue;
                                }

                                MaintenanceSchedule::query()->create($data + [
                                    'asset_id' => $aset->getKey(),
                                    'is_active' => true,
                                ]);

                                $dibuat++;
                            }

                            Notification::make()
                                ->success()
                                ->title($dibuat.' jadwal dibuat')
                                ->body($dilewati > 0
                                    ? $dilewati.' aset dilewati karena sudah punya jadwal dengan nama yang sama.'
                                    : 'Kunjungan pertama tiap jadwal sudah ikut dibuat, dan bisa dilihat di menu Jadwal pemeliharaan.')
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            // Dua pesan kosong, bukan satu. Tabel yang kosong karena datanya memang belum
            // ada dan tabel yang kosong karena penyaringnya terlalu sempit adalah dua
            // keadaan yang berbeda, dan saran yang benar untuk keduanya juga berbeda.
            // Menyuruh orang menambah aset padahal asetnya ada 154 hanya membuat mereka
            // curiga datanya hilang.
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada aset yang cocok'
                : 'Belum ada aset yang tercatat')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada aset yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh aset lagi.'
                : 'Tambahkan aset satu per satu, atau impor dari berkas CSV lewat tombol di kanan atas. Buat kategorinya dulu kalau belum ada.');
    }

    public static function canPrint(): bool
    {
        return static::allows('print');
    }

    /**
     * Dipakai oleh layar impor untuk menampilkan kategori yang sudah siap dipakai.
     */
    public static function activeCategoryNames(): string
    {
        return AssetCategory::query()
            ->where('is_active', true)
            ->whereNotNull('account_asset')
            ->where('account_asset', '!=', '')
            ->orderBy('code')
            ->pluck('code')
            ->implode(', ');
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            DepreciationRelationManager::class,
            MaintenanceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
