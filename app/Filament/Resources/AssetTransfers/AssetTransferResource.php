<?php

namespace App\Filament\Resources\AssetTransfers;

use App\Filament\Resources\AssetTransfers\Pages\CreateAssetTransfer;
use App\Filament\Resources\AssetTransfers\Pages\ListAssetTransfers;
use App\Filament\Resources\AssetTransfers\Pages\ViewAssetTransfer;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Serah terima aset. Dokumen ini yang menjawab pertanyaan "kenapa aset ini
 * sekarang ada di ruangan lain, dan siapa yang menyerahkannya kepada siapa".
 */
class AssetTransferResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = AssetTransfer::class;

    protected static string $moduleCode = 'asset_transfers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-right-start-on-rectangle';

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $navigationLabel = 'Asset Transfers';

    protected static ?string $modelLabel = 'asset transfer';

    protected static ?string $pluralModelLabel = 'asset transfers';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Asset Being Transferred')
                ->description('Keadaan aset sekarang disalin sendiri sebagai keadaan asal, jadi tidak perlu diketik. Yang perlu diisi hanya tujuannya.')
                ->columns(3)
                ->schema([
                    TextInput::make('code')
                        ->label('Nomor')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan'),
                    Select::make('asset_id')
                        ->label('Aset')
                        ->options(fn (): array => Asset::query()
                            ->where('status', '!=', 'dilepas')
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
                        ->live()
                        ->columnSpan(2)
                        // Keadaan aset dibacakan di sini, sebelum orang mengisi tujuannya,
                        // supaya titik berangkatnya jelas tanpa membuka layar aset.
                        ->helperText(fn ($state): string => static::describeAsset($state)),
                ]),

            Section::make('Movement')
                ->description('Kosongkan yang tidak berubah. Kolom yang dikosongkan berarti tetap seperti sekarang, bukan dikosongkan pada asetnya.')
                ->columns(3)
                ->schema([
                    Select::make('to_location_id')
                        ->label('Lokasi tujuan')
                        ->options(fn (): array => Location::query()
                            ->where('is_active', true)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (Location $location) => [
                                $location->id => $location->code.' '.$location->name,
                            ])
                            ->all())
                        ->searchable()
                        ->placeholder('Tidak pindah ruangan'),
                    Select::make('to_custodian_employee_id')
                        ->label('Penanggung jawab baru')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Tidak ganti orang'),
                    Select::make('to_department_id')
                        ->label('Departemen baru')
                        ->options(fn (): array => Department::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Tidak pindah departemen'),
                    Select::make('reason')
                        ->label('Alasan')
                        ->options(AssetTransfer::REASONS)
                        ->required()
                        ->default('mutasi_ruangan'),
                    DatePicker::make('transfer_date')
                        ->label('Tanggal serah terima')
                        ->required()
                        ->default(now())
                        ->maxDate(now())
                        ->helperText('Tidak bisa diisi tanggal yang belum terjadi.'),
                    TextInput::make('reference')
                        ->label('Nomor berita acara')
                        ->maxLength(100)
                        ->placeholder('Belum ada'),
                ]),

            Section::make('Handover')
                ->description('Dua nama inilah yang membedakan dokumen ini dari sekadar mengubah kolom lokasi di data aset.')
                ->columns(2)
                ->schema([
                    Select::make('handed_over_by_employee_id')
                        ->label('Diserahkan oleh')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Belum dicatat'),
                    Select::make('received_by_employee_id')
                        ->label('Diterima oleh')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Belum dicatat'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->placeholder('Contoh: kondisi barang saat diserahkan, kelengkapan yang ikut dibawa'),
                ]),
        ]);
    }

    /**
     * Halaman detail. Isinya sengaja dibagi tiga seksi yang sama dengan formulirnya,
     * supaya orang yang baru saja mengisi formulir mengenali susunannya kembali.
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Documents')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('transfer_date')->label('Tanggal serah terima')->date('d F Y'),
                    TextEntry::make('reason')
                        ->label('Alasan')
                        ->badge()
                        ->color('gray')
                        ->formatStateUsing(fn (AssetTransfer $record): string => $record->reasonLabel()),
                    TextEntry::make('reference')->label('Nomor berita acara')->placeholder('Belum ada'),
                    TextEntry::make('createdByUser.name')->label('Dicatat oleh')->placeholder('Tidak diketahui'),
                    TextEntry::make('created_at')->label('Dicatat pada')->dateTime('d M Y H:i'),
                ]),

            Section::make('Asset')
                ->columns(3)
                ->schema([
                    TextEntry::make('asset.code')->label('Kode aset')->fontFamily('mono'),
                    TextEntry::make('asset.name')->label('Nama aset')->columnSpan(2),
                    TextEntry::make('asset.serial_number')->label('Nomor seri')->placeholder('Tidak tercatat'),
                    TextEntry::make('asset.category.name')
                        ->label('Kategori')
                        ->state(fn (AssetTransfer $record): string => $record->asset?->category?->pickerLabel() ?? 'Tidak tercatat')
                        ->columnSpan(2),
                ]),

            Section::make('Movement')
                ->description('Kolom sebelum adalah keadaan aset pada detik dokumen ini dibuat, disalin sekali dan tidak ikut berubah kemudian.')
                ->columns(3)
                ->schema([
                    TextEntry::make('fromLocation.code')->label('Lokasi sebelum')->placeholder('Belum diisi'),
                    TextEntry::make('toLocation.code')->label('Lokasi sesudah')->placeholder('Tidak diubah'),
                    TextEntry::make('perubahan_lokasi')
                        ->label('Berubah')
                        ->state(fn (AssetTransfer $record): string => $record->to_location_id === null ? 'Tidak' : 'Ya'),
                    TextEntry::make('fromCustodian.full_name')->label('Penanggung jawab sebelum')->placeholder('Belum diisi'),
                    TextEntry::make('toCustodian.full_name')->label('Penanggung jawab sesudah')->placeholder('Tidak diubah'),
                    TextEntry::make('perubahan_pemegang')
                        ->label('Berubah')
                        ->state(fn (AssetTransfer $record): string => $record->to_custodian_employee_id === null ? 'Tidak' : 'Ya'),
                    TextEntry::make('fromDepartment.name')->label('Departemen sebelum')->placeholder('Belum diisi'),
                    TextEntry::make('toDepartment.name')->label('Departemen sesudah')->placeholder('Tidak diubah'),
                    TextEntry::make('perubahan_departemen')
                        ->label('Berubah')
                        ->state(fn (AssetTransfer $record): string => $record->to_department_id === null ? 'Tidak' : 'Ya'),
                ]),

            Section::make('Handover')
                ->columns(2)
                ->schema([
                    TextEntry::make('handedOverBy.full_name')->label('Diserahkan oleh')->placeholder('Belum dicatat'),
                    TextEntry::make('receivedBy.full_name')->label('Diterima oleh')->placeholder('Belum dicatat'),
                    TextEntry::make('notes')->label('Catatan')->placeholder('Tidak ada')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with([
                'asset', 'fromLocation', 'toLocation', 'fromCustodian', 'toCustodian',
                'fromDepartment', 'toDepartment', 'receivedBy',
            ]))
            ->columns([
                TextColumn::make('transfer_date')
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
                    ->description(fn (AssetTransfer $record): ?string => $record->asset?->code)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('reason')
                    ->label('Alasan')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (AssetTransfer $record): string => $record->reasonLabel()),
                TextColumn::make('perubahan')
                    ->label('Yang berubah')
                    ->state(fn (AssetTransfer $record): string => $record->describeChanges())
                    ->wrap(),
                TextColumn::make('receivedBy.full_name')
                    ->label('Diterima oleh')
                    ->placeholder('Belum dicatat')
                    ->wrap(),
                TextColumn::make('handedOverBy.full_name')
                    ->label('Diserahkan oleh')
                    ->placeholder('Belum dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label('Berita acara')
                    ->placeholder('Tidak ada')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dicatat oleh')
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('transfer_date', 'desc')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('reason')
                    ->label('Alasan')
                    ->options(AssetTransfer::REASONS)
                    ->multiple(),
                SelectFilter::make('to_location_id')
                    ->label('Lokasi tujuan')
                    ->relationship('toLocation', 'code')
                    ->searchable()
                    ->preload(),
                Filter::make('rentang_tanggal')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $q, $tanggal) => $q->whereDate('transfer_date', '>=', $tanggal))
                        ->when($data['sampai'] ?? null, fn (Builder $q, $tanggal) => $q->whereDate('transfer_date', '<=', $tanggal))),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Open')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->iconButton(),
                Action::make('bam')
                    ->label('Download BAM')
                    ->icon('heroicon-o-document-text')
                    ->iconButton()
                    ->url(fn (AssetTransfer $record): string => route('gais.aset.berita-acara', [
                        'transfer' => $record->getKey(),
                        'jenis' => 'bam',
                    ]))
                    ->openUrlInNewTab(),
                Action::make('bast')
                    ->label('Download BAST')
                    ->icon('heroicon-o-document-check')
                    ->iconButton()
                    ->url(fn (AssetTransfer $record): string => route('gais.aset.berita-acara', [
                        'transfer' => $record->getKey(),
                        'jenis' => 'bast',
                    ]))
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->label('Cancel Transfer')
                    ->iconButton()
                    ->visible(fn (AssetTransfer $record): bool => static::canDelete($record))
                    ->modalHeading('Cancel Asset Transfer')
                    ->modalDescription('Dokumen ini dihapus, dan aset dikembalikan ke lokasi, penanggung jawab, serta departemen sebelum serah terima ini. Hanya bisa dilakukan selama belum ada perpindahan lain sesudahnya.')
                    ->modalSubmitActionLabel('Cancel and Return'),
            ])
            ->emptyStateHeading('Belum ada serah terima aset')
            ->emptyStateDescription('Catat di sini setiap kali aset berpindah ruangan, berganti penanggung jawab, atau pindah departemen. Data asetnya ikut berubah sendiri, dan riwayatnya tersimpan sebagai dokumen.');
    }

    /**
     * Ringkasan keadaan aset yang dipilih, dibaca dari basis data saat itu juga.
     * Ini yang membuat orang tahu titik berangkatnya sebelum mengisi tujuan.
     */
    public static function describeAsset($assetId): string
    {
        if (blank($assetId)) {
            return 'Pilih asetnya dulu.';
        }

        $asset = Asset::query()->with(['location', 'custodian', 'department'])->find($assetId);

        if ($asset === null) {
            return 'Aset tidak ditemukan.';
        }

        return 'Lokasi '.($asset->location?->code.' '.$asset->location?->name ?: 'belum diisi')
            .'. Penanggung jawab '.($asset->custodian?->full_name ?: 'belum diisi')
            .'. Departemen '.($asset->department?->name ?: 'belum diisi')
            .'. Status '.$asset->statusLabel().'.';
    }

    /**
     * Dokumen serah terima tidak bisa diubah. Kalau isinya salah, yang benar adalah
     * mencatat serah terima baru yang mengembalikan barangnya. Jalur ini ditutup
     * dengan tidak mendaftarkan halaman ubah sama sekali.
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        if (! $record->canBeUndone()) {
            return false;
        }

        return static::allows('delete');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetTransfers::route('/'),
            'create' => CreateAssetTransfer::route('/create'),
            'view' => ViewAssetTransfer::route('/{record}'),
        ];
    }
}
