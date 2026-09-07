<?php

namespace App\Filament\Resources\AssetDisposals;

use App\Filament\Resources\AssetDisposals\Pages\CreateAssetDisposal;
use App\Filament\Resources\AssetDisposals\Pages\EditAssetDisposal;
use App\Filament\Resources\AssetDisposals\Pages\ListAssetDisposals;
use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\Employee;
use App\Services\PenyusutanAset;
use App\Support\Rupiah;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
 * Pelepasan aset. Menyimpan dokumen di sini membuat status aset menjadi
 * Sudah dilepas, sehingga status itu berhenti menjadi label yang bisa dipilih
 * siapa saja di formulir aset.
 */
class AssetDisposalResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = AssetDisposal::class;

    protected static string $moduleCode = 'asset_disposals';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected static string|UnitEnum|null $navigationGroup = 'Aset';

    protected static ?string $navigationLabel = 'Pelepasan aset';

    protected static ?string $modelLabel = 'pelepasan aset';

    protected static ?string $pluralModelLabel = 'pelepasan aset';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Aset yang dilepas')
                ->description('Menyimpan dokumen ini mengubah status aset menjadi Sudah dilepas. Aset yang sudah dilepas tidak ikut lagi dalam stock opname dan tidak bisa diserahterimakan.')
                ->columns(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Nomor')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan'),
                    Select::make('asset_id')
                        ->label('Aset')
                        ->options(fn (?AssetDisposal $record): array => Asset::query()
                            ->where(fn (Builder $query) => $query
                                ->where('status', '!=', 'dilepas')
                                ->orWhere('id', $record?->asset_id))
                            ->orderBy('code')
                            ->limit(500)
                            ->get()
                            ->mapWithKeys(fn (Asset $asset) => [
                                $asset->id => $asset->code.' '.$asset->name,
                            ])
                            ->all())
                        ->getSearchResultsUsing(fn (string $search): array => Asset::query()
                            ->where('status', '!=', 'dilepas')
                            ->where(fn (Builder $query) => $query
                                ->whereLike('code', "%{$search}%", caseSensitive: false)
                                ->orWhereLike('name', "%{$search}%", caseSensitive: false))
                            ->orderBy('code')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (Asset $asset) => [
                                $asset->id => $asset->code.' '.$asset->name,
                            ])
                            ->all())
                        ->getOptionLabelUsing(fn ($value): ?string => Asset::query()->find($value)?->code)
                        ->searchable()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->live()
                        ->disabled(fn (?AssetDisposal $record): bool => $record !== null)
                        ->dehydrated(fn (?AssetDisposal $record): bool => $record === null)
                        ->columnSpan(2)
                        ->helperText(fn ($state): string => static::describeAsset($state)),
                ]),

            Section::make('Cara dan hasil pelepasan')
                ->columns(3)
                ->schema([
                    Select::make('method')
                        ->label('Cara pelepasan')
                        ->options(AssetDisposal::METHODS)
                        ->required()
                        ->default('dijual')
                        ->live(),
                    DatePicker::make('disposal_date')
                        ->label('Tanggal pelepasan')
                        ->required()
                        ->default(now())
                        ->maxDate(now())
                        ->helperText('Tidak bisa diisi tanggal yang belum terjadi.'),
                    TextInput::make('reference')
                        ->label('Nomor berita acara')
                        ->maxLength(100)
                        ->placeholder('Belum ada'),
                    TextInput::make('proceeds')
                        ->label('Hasil pelepasan')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->visible(fn ($get): bool => AssetDisposal::expectsProceeds($get('method')))
                        ->required(fn ($get): bool => AssetDisposal::expectsProceeds($get('method')))
                        ->helperText('Uang yang benar benar diterima. Laba atau ruginya dihitung sendiri dari selisih terhadap nilai buku, dan muncul di daftar setelah dokumen ini tersimpan.'),
                    TextInput::make('counterparty')
                        ->label(fn ($get): string => match ($get('method')) {
                            'dihibahkan' => 'Penerima hibah',
                            'dimusnahkan' => 'Pelaksana pemusnahan',
                            'hilang' => 'Pelapor kehilangan',
                            default => 'Pembeli',
                        })
                        ->maxLength(150)
                        ->visible(fn ($get): bool => $get('method') !== null)
                        ->placeholder('Belum dicatat')
                        ->columnSpan(2),
                ]),

            Section::make('Dasar dan persetujuan')
                ->columns(2)
                ->schema([
                    Select::make('approved_by_employee_id')
                        ->label('Disetujui oleh')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Belum dicatat'),
                    FileUpload::make('document_path')
                        ->label('Berkas pendukung')
                        ->disk('public')
                        ->directory('pelepasan-aset')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(5120)
                        ->openable()
                        ->downloadable()
                        ->helperText('PDF atau foto berita acara, maksimum 5 MB. Untuk mengganti berkas, hapus dulu yang lama lalu unggah yang baru.'),
                    Textarea::make('reason')
                        ->label('Alasan pelepasan')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->placeholder('Contoh: rusak berat dan biaya perbaikan melebihi harga barang pengganti'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['asset', 'approvedBy']))
            ->columns([
                TextColumn::make('disposal_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('asset.name')
                    ->label('Aset')
                    ->description(fn (AssetDisposal $record): ?string => $record->asset?->code)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('method')
                    ->label('Cara')
                    ->badge()
                    ->formatStateUsing(fn (AssetDisposal $record): string => $record->methodLabel())
                    ->color(fn (AssetDisposal $record): string => $record->methodColor()),
                TextColumn::make('proceeds')
                    ->label('Hasil')
                    ->money('IDR', locale: 'id')
                    ->placeholder('Tidak ada')
                    ->alignEnd()
                    ->sortable(),
                /*
                 * Laba rugi pelepasan, yang sampai kiriman sebelumnya sengaja dikosongkan
                 * karena nilai bukunya belum ada. Sekarang ada, dan angkanya adalah hasil
                 * pelepasan dikurangi nilai buku terakhir yang sudah ditutup. Selama bulan
                 * pelepasannya sendiri belum ditutup, angka ini masih akan bergerak, dan
                 * kolomnya mengatakan itu alih alih diam diam menampilkan angka sementara
                 * seolah olah sudah final.
                 */
                TextColumn::make('laba_rugi')
                    ->label('Laba atau rugi')
                    ->state(function (AssetDisposal $record): string {
                        if ($record->asset === null) {
                            return 'Asetnya tidak ditemukan';
                        }

                        $mesin = app(PenyusutanAset::class);
                        $selisih = $mesin->labaRugiPelepasan($record->asset);

                        if ($selisih === null) {
                            return 'Belum bisa dihitung';
                        }

                        $awalan = $selisih > 0 ? 'Laba ' : ($selisih < 0 ? 'Rugi ' : 'Impas ');

                        return $selisih === 0.0
                            ? 'Impas'
                            : $awalan.Rupiah::penuh(abs($selisih));
                    })
                    ->description(fn (AssetDisposal $record): ?string => $record->asset !== null
                        && ! app(PenyusutanAset::class)->periodePelepasanSudahDitutup($record->asset)
                            ? 'Sementara, bulan pelepasannya belum ditutup'
                            : null)
                    ->color(function (AssetDisposal $record): string {
                        $selisih = $record->asset ? app(PenyusutanAset::class)->labaRugiPelepasan($record->asset) : null;

                        if ($selisih === null || $selisih === 0.0) {
                            return 'gray';
                        }

                        return $selisih > 0 ? 'success' : 'danger';
                    })
                    ->alignEnd(),
                TextColumn::make('nilai_buku')
                    ->label('Nilai buku saat dilepas')
                    ->state(fn (AssetDisposal $record): string => $record->asset === null
                        ? 'Tidak diketahui'
                        : Rupiah::penuh(app(PenyusutanAset::class)->nilaiBuku($record->asset)))
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nilai_perolehan')
                    ->label('Nilai perolehan')
                    ->state(fn (AssetDisposal $record): ?float => $record->asset?->acquisition_cost !== null
                        ? (float) $record->asset->acquisition_cost
                        : null)
                    ->money('IDR', locale: 'id')
                    ->placeholder('Tidak tercatat')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('counterparty')
                    ->label('Pihak terkait')
                    ->placeholder('Belum dicatat')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approvedBy.full_name')
                    ->label('Disetujui oleh')
                    ->placeholder('Belum dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label('Berita acara')
                    ->placeholder('Tidak ada')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->placeholder('Tidak dicatat')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('disposal_date', 'desc')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('method')
                    ->label('Cara pelepasan')
                    ->options(AssetDisposal::METHODS)
                    ->multiple(),
                Filter::make('rentang_tanggal')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $q, $tanggal) => $q->whereDate('disposal_date', '>=', $tanggal))
                        ->when($data['sampai'] ?? null, fn (Builder $q, $tanggal) => $q->whereDate('disposal_date', '<=', $tanggal))),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->label('Batalkan pelepasan')
                    ->iconButton()
                    ->modalHeading('Batalkan pelepasan aset')
                    ->modalDescription('Dokumen ini dihapus, dan status aset dikembalikan ke keadaan sebelum dilepas. Berkas pendukung yang sudah diunggah ikut hilang.')
                    ->modalSubmitActionLabel('Batalkan dan kembalikan'),
            ])
            ->emptyStateHeading('Belum ada aset yang dilepas')
            ->emptyStateDescription('Catat di sini setiap aset yang dijual, dihibahkan, dimusnahkan, atau hilang. Status asetnya berubah sendiri menjadi Sudah dilepas, dan alasannya tersimpan sebagai dokumen.');
    }

    public static function describeAsset($assetId): string
    {
        if (blank($assetId)) {
            return 'Pilih asetnya dulu.';
        }

        $asset = Asset::query()->with(['location', 'category'])->find($assetId);

        if ($asset === null) {
            return 'Aset tidak ditemukan.';
        }

        $nilai = $asset->acquisition_cost !== null
            ? 'Rp '.number_format((float) $asset->acquisition_cost, 0, ',', '.')
            : 'tidak tercatat';

        return 'Kategori '.($asset->category?->name ?: 'belum diisi')
            .'. Lokasi '.($asset->location?->code ?: 'belum diisi')
            .'. Kondisi '.$asset->conditionLabel()
            .'. Nilai perolehan '.$nilai.'.';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetDisposals::route('/'),
            'create' => CreateAssetDisposal::route('/create'),
            'edit' => EditAssetDisposal::route('/{record}/edit'),
        ];
    }
}
