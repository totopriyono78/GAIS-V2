<?php

namespace App\Filament\Resources\WorkOrders;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Filament\Resources\WorkOrders\Pages\CreateWorkOrder;
use App\Filament\Resources\WorkOrders\Pages\EditWorkOrder;
use App\Filament\Resources\WorkOrders\Pages\ListWorkOrders;
use App\Models\Asset;
use App\Models\Vendor;
use App\Models\WorkOrder;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

/**
 * Perintah kerja pemeliharaan, preventif maupun korektif.
 *
 * Layar ini menampung dua pekerjaan yang berbeda sifatnya. Membuat perintah kerja adalah
 * mencatat masalah, dan itu perlu cepat: aset, apa yang rusak, seberapa mendesak. Menutup
 * perintah kerja adalah mencatat hasil, dan itu perlu lengkap: apa yang dikerjakan, biaya,
 * kondisi akhir. Karena itu penyelesaian dibuat sebagai tindakan tersendiri dengan
 * formulirnya sendiri, bukan sebagai kolom status yang bisa diganti diam diam di layar ubah.
 */
class WorkOrderResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = WorkOrder::class;

    protected static string $moduleCode = 'work_orders';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?string $navigationLabel = 'Work Orders';

    protected static ?string $modelLabel = 'work order';

    protected static ?string $pluralModelLabel = 'work orders';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'code';

    /** Jumlah pekerjaan yang masih menggantung, muncul sebagai lencana di menu. */
    public static function getNavigationBadge(): ?string
    {
        $terbuka = WorkOrder::query()->terbuka()->count();

        return $terbuka > 0 ? (string) $terbuka : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return WorkOrder::query()->terbuka()->where('priority', 'mendesak')->exists()
            ? 'danger'
            : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Problem')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Nomor perintah kerja')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan'),
                    Select::make('asset_id')
                        ->label('Aset')
                        ->relationship(
                            'asset',
                            'name',
                            fn (Builder $query) => $query->where('status', '!=', 'dilepas')->orderBy('code'),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Asset $record): string => $record->code.' '.$record->name)
                        ->searchable(['code', 'name'])
                        ->live()
                        ->placeholder('Bukan aset tertentu')
                        // Boleh dikosongkan. Lampu mati di koridor dan keran bocor di
                        // toilet umum bukan kerusakan aset yang terdaftar, dan memaksa
                        // memilih aset hanya akan mengisi riwayat aset terdekat dengan
                        // pekerjaan yang tidak pernah menyentuhnya.
                        ->helperText('Boleh dikosongkan kalau kerusakannya bukan pada aset tertentu. Aset yang sudah dilepas tidak muncul di sini.'),
                    Placeholder::make('keadaan_aset')
                        ->label('Keadaan aset sekarang')
                        ->columnSpanFull()
                        ->content(function ($get): string {
                            $aset = filled($get('asset_id')) ? Asset::with(['location', 'custodian'])->find($get('asset_id')) : null;

                            if ($aset === null) {
                                return 'Belum ada aset yang dipilih. Kalau kerusakannya bukan pada aset tertentu, biarkan kosong dan jelaskan lokasinya di uraian masalah.';
                            }

                            return 'Lokasi '.($aset->location?->name ?? 'belum ditentukan')
                                .'. Penanggung jawab '.($aset->custodian?->full_name ?? 'belum diisi')
                                .'. Kondisi '.strtolower($aset->conditionLabel())
                                .'. Status '.strtolower($aset->statusLabel()).'.';
                        }),
                    Select::make('type')
                        ->label('Jenis pekerjaan')
                        ->options(WorkOrder::TYPES)
                        ->default('korektif')
                        ->required()
                        ->helperText('Preventif berarti terjadwal. Korektif berarti ada yang rusak.'),
                    Select::make('priority')
                        ->label('Prioritas')
                        ->options(WorkOrder::PRIORITIES)
                        ->default('normal')
                        ->required(),
                    DatePicker::make('reported_date')
                        ->label('Tanggal lapor')
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->maxDate(now())
                        ->required(),
                    /*
                     * Nama parameter penutup di bawah wajib $query, bukan singkatan.
                     *
                     * Filament mengisi argumen penutup menurut namanya lebih dulu, dan
                     * baru menurut tipenya kalau namanya tidak dikenal. Nama yang tidak
                     * dikenal membuat Filament mencari Illuminate\Database\Eloquent\Builder
                     * di service container, dan yang didapat adalah builder tanpa model,
                     * yang meledak dengan "Call to a member function qualifyColumn() on
                     * null" saat halaman dibuka. Gejalanya jauh dari sebabnya, jadi ini
                     * ditulis di sini.
                     */
                    Select::make('reported_by_employee_id')
                        ->label('Pelapor')
                        ->relationship('reportedBy', 'full_name', fn (Builder $query) => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->placeholder('Tidak dicatat'),
                    Placeholder::make('asal_permintaan')
                        ->label('Asal perintah kerja')
                        ->columnSpanFull()
                        ->visible(fn (?WorkOrder $record): bool => filled($record?->service_request_id))
                        ->content(fn (?WorkOrder $record): string => 'Lahir dari permintaan '
                            .($record?->serviceRequest?->code ?? '')
                            .' yang diajukan '.($record?->serviceRequest?->requester?->full_name ?? 'karyawan yang tidak tercatat')
                            .'. Permintaan itu ikut selesai saat perintah kerja ini diselesaikan.'),
                    Textarea::make('problem')
                        ->label('Uraian masalah atau pekerjaan')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull()
                        ->placeholder('Contoh: AC ruang rapat besar tidak dingin, air menetes dari indoor unit.'),
                ]),

            Section::make('Assignment')
                ->columns(3)
                ->description('Boleh dikosongkan saat perintah kerja baru dibuka, dan diisi setelah ada yang menerima pekerjaannya.')
                ->schema([
                    DatePicker::make('scheduled_date')
                        ->label('Rencana dikerjakan')
                        ->displayFormat('d M Y')
                        ->placeholder('Belum dijadwalkan'),
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
                    Select::make('technician_employee_id')
                        ->label('Teknisi internal')
                        ->relationship('technician', 'full_name', fn (Builder $query) => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->placeholder('Belum ditugaskan'),
                ]),

            /*
             * Hasil pekerjaan hanya bisa dibaca di layar ubah, tidak bisa diketik. Yang
             * mengisinya adalah tindakan Selesaikan, karena mengisi hasil dan mengubah
             * status harus terjadi bersamaan: hasil tanpa status berarti pekerjaan yang
             * tampak selesai tetapi masih terhitung menggantung, dan status tanpa hasil
             * berarti riwayat aset yang tidak menjelaskan apa apa.
             */
            Section::make('Work Result')
                ->columnSpanFull()
                ->columns(3)
                ->visible(fn (?WorkOrder $record): bool => $record?->exists ?? false)
                ->description('Diisi lewat tombol Selesaikan di daftar perintah kerja, bukan diketik di sini.')
                ->schema([
                    Placeholder::make('status_sekarang')
                        ->label('Status')
                        ->content(fn (?WorkOrder $record): string => $record?->statusLabel() ?? 'Baru'),
                    Placeholder::make('tanggal_selesai')
                        ->label('Tanggal selesai')
                        ->content(fn (?WorkOrder $record): string => $record?->completed_date?->translatedFormat('d F Y')
                            ?? 'Belum selesai'),
                    Placeholder::make('biaya')
                        ->label('Biaya')
                        ->content(fn (?WorkOrder $record): string => filled($record?->cost)
                            ? Rupiah::penuh((float) $record->cost)
                            : 'Belum ada'),
                    Placeholder::make('uraian_hasil')
                        ->label('Yang dikerjakan')
                        ->columnSpanFull()
                        ->content(fn (?WorkOrder $record): string => filled($record?->work_done)
                            ? $record->work_done
                            : 'Belum dicatat.'),
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
                TextColumn::make('asset.code')
                    ->label('Aset')
                    ->description(fn (WorkOrder $record): ?string => $record->asset?->name)
                    ->placeholder('Bukan aset tertentu')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('problem')
                    ->label('Masalah')
                    ->limit(70)
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
                /*
                 * Tiga kolom di bawah ini disembunyikan secara bawaan supaya tabel ini
                 * berhenti di enam kolom. Jenis pekerjaan sudah terbaca dari ada atau
                 * tidaknya jadwal pemeliharaan, tanggal lapor sudah terangkum sebagai
                 * "Terbuka N hari" di bawah status, dan biaya baru berarti setelah
                 * pekerjaannya selesai. Ketiganya satu klik jauhnya lewat Pilih kolom.
                 */
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->state(fn (WorkOrder $record): string => $record->typeLabel())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->state(fn (WorkOrder $record): string => $record->priorityLabel())
                    ->color(fn (WorkOrder $record): string => $record->priorityColor())
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (WorkOrder $record): string => $record->statusLabel())
                    ->color(fn (WorkOrder $record): string => $record->statusColor())
                    ->description(fn (WorkOrder $record): ?string => $record->isOpen()
                        ? 'Terbuka '.$record->umurHari().' hari'
                        : null)
                    ->sortable(),
                TextColumn::make('pelaksana')
                    ->label('Pelaksana')
                    ->state(fn (WorkOrder $record): string => $record->pelaksana())
                    ->wrap(),
                TextColumn::make('reported_date')
                    ->label('Dilaporkan')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('completed_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('Belum')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cost')
                    ->label('Biaya')
                    ->state(fn (WorkOrder $record): string => filled($record->cost)
                        ? Rupiah::penuh((float) $record->cost)
                        : 'Belum ada')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('serviceRequest.code')
                    ->label('Dari permintaan')
                    ->placeholder('Dibuat langsung')
                    ->url(fn (WorkOrder $record): ?string => $record->serviceRequest
                        ? ServiceRequestResource::getUrl('view', ['record' => $record->serviceRequest])
                        : null)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attachments_count')
                    ->label('Lampiran')
                    ->counts('attachments')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            /*
             * Yang terbuka lebih dulu, lalu yang paling mendesak, lalu yang paling lama
             * menggantung. Urutan ini yang membuat layar terbaca sebagai daftar kerja,
             * bukan sebagai arsip yang kebetulan diurutkan menurut tanggal.
             */
            ->defaultSort('reported_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->orderByRaw("case when status in ('dibuka','dikerjakan') then 0 else 1 end")
                ->orderByRaw("case priority when 'mendesak' then 0 when 'tinggi' then 1 when 'normal' then 2 else 3 end"))
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(WorkOrder::STATUSES)
                    ->multiple(),
                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options(WorkOrder::PRIORITIES)
                    ->multiple(),
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(WorkOrder::TYPES),
                SelectFilter::make('vendor_id')
                    ->label('Rekanan')
                    ->relationship('vendor', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                static::selesaikanAction(),
                static::batalkanAction(),
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (WorkOrder $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada perintah kerja')
            ->emptyStateDescription('Perintah kerja korektif dibuat lewat tombol di kanan atas saat ada yang rusak. Yang preventif dibuat dari menu Jadwal pemeliharaan, supaya jatuh tempo jadwalnya ikut bergerak setelah selesai.');
    }

    /**
     * Menyelesaikan pekerjaan. Formulirnya menuntut uraian hasil, karena riwayat aset
     * yang hanya berisi "selesai" tidak menjawab apa pun setahun kemudian.
     */
    public static function selesaikanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('selesaikan')
            ->label($iconOnly ? 'Selesaikan' : 'Selesaikan pekerjaan')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (WorkOrder $record): bool => $record->isOpen() && static::canEdit($record))
            ->modalHeading(fn (WorkOrder $record): string => 'Complete '.$record->code)
            ->modalDescription(fn (WorkOrder $record): string => $record->type === 'preventif' && $record->maintenance_schedule_id
                ? 'Setelah disimpan, jatuh tempo jadwal pemeliharaannya bergeser sesuai tanggal selesai di bawah.'
                : 'Uraian hasil di bawah masuk ke riwayat pemeliharaan aset ini.')
            ->modalSubmitActionLabel('Save Result')
            ->fillForm(fn (WorkOrder $record): array => [
                'completed_date' => now()->toDateString(),
                'cost' => $record->cost,
            ])
            ->schema([
                DatePicker::make('completed_date')
                    ->label('Tanggal selesai')
                    ->displayFormat('d M Y')
                    ->required()
                    ->maxDate(now())
                    ->helperText('Tanggal pekerjaannya benar benar selesai, bukan tanggal pencatatan.'),
                Textarea::make('work_done')
                    ->label('Yang dikerjakan')
                    ->rows(4)
                    ->required()
                    ->placeholder('Contoh: cuci filter dan evaporator, tambah freon 0,3 kg, ganti kapasitor outdoor.'),
                TextInput::make('cost')
                    ->label('Biaya')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Tidak ada biaya')
                    ->helperText('Kosongkan kalau dikerjakan sendiri tanpa biaya.'),
                Select::make('condition_after')
                    ->label('Kondisi aset setelah dikerjakan')
                    ->options(Asset::CONDITIONS)
                    ->placeholder('Tidak berubah')
                    // Disembunyikan untuk perintah kerja yang tidak menyebut aset.
                    // Pertanyaan tentang kondisi aset yang tidak ada hanya menyita
                    // perhatian tanpa pernah bisa dijawab.
                    ->visible(fn (WorkOrder $record): bool => $record->asset !== null)
                    ->helperText('Kalau diisi, kondisi fisik asetnya ikut diperbarui.'),
            ])
            ->action(function (WorkOrder $record, array $data): void {
                $record->fill($data + ['status' => 'selesai'])->save();

                $pesan = $record->asset
                    ? 'Hasilnya sudah masuk ke riwayat aset '.$record->asset->code.'.'
                    : 'Perintah kerja ini tidak menyebut aset, jadi hasilnya tercatat di sini saja.';

                if ($record->maintenanceSchedule) {
                    $record->maintenanceSchedule->refresh();
                    $pesan .= ' Jatuh tempo jadwal berikutnya '
                        .($record->maintenanceSchedule->next_due_date?->translatedFormat('d F Y') ?? 'belum ditentukan').'.';
                }

                Notification::make()
                    ->success()
                    ->title($record->code.' selesai')
                    ->body($pesan)
                    ->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function batalkanAction(): Action
    {
        return Action::make('batalkan')
            ->label('Cancel')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->iconButton()
            ->visible(fn (WorkOrder $record): bool => $record->isOpen() && static::canEdit($record))
            ->modalHeading(fn (WorkOrder $record): string => 'Cancel '.$record->code)
            ->modalDescription('Perintah kerja yang dibatalkan tetap tersimpan dan tetap terbaca di riwayat aset, tetapi tidak lagi terhitung sebagai pekerjaan yang menggantung. Jadwal pemeliharaannya tidak ikut bergerak, karena pekerjaannya memang tidak jadi dikerjakan.')
            ->modalSubmitActionLabel('Cancel Work Order')
            ->schema([
                Textarea::make('cancel_reason')
                    ->label('Alasan dibatalkan')
                    ->rows(2)
                    ->required()
                    ->placeholder('Contoh: aset sudah diganti unit baru, perbaikan tidak jadi dilakukan.'),
            ])
            ->action(function (WorkOrder $record, array $data): void {
                $record->fill($data + ['status' => 'dibatalkan'])->save();

                Notification::make()
                    ->success()
                    ->title($record->code.' dibatalkan')
                    ->send();
            });
    }

    /**
     * Perintah kerja yang sudah selesai tidak boleh dihapus. Angkanya sudah masuk ke
     * biaya pemeliharaan aset, dan menghapusnya membuat total biaya berubah tanpa jejak.
     * Yang dibatalkan pun tetap disimpan, karena pembatalan itu sendiri adalah riwayat.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && $record->status === 'dibuka';
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkOrders::route('/'),
            'create' => CreateWorkOrder::route('/create'),
            'edit' => EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
