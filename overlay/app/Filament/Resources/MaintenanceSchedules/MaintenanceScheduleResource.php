<?php

namespace App\Filament\Resources\MaintenanceSchedules;

use App\Filament\Resources\MaintenanceSchedules\Pages\ListMaintenanceSchedules;
use App\Filament\Resources\MaintenanceSchedules\Pages\ViewMaintenanceSchedule;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Employee;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceVisit;
use App\Models\Vendor;
use App\Models\WorkOrder;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
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
 * Jadwal pemeliharaan preventif.
 *
 * Layar ini adalah daftar tugas, bukan arsip. Karena itu urutannya jatuh tempo paling
 * dekat lebih dulu, bukan yang terbaru dibuat, dan tombol utamanya bukan "tambah"
 * melainkan "buat perintah kerja" pada baris yang sudah waktunya dikerjakan.
 */
class MaintenanceScheduleResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = MaintenanceSchedule::class;

    protected static string $moduleCode = 'maintenance_schedules';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?string $navigationLabel = 'Preventive Maintenance';

    protected static ?string $modelLabel = 'preventive maintenance';

    protected static ?string $pluralModelLabel = 'preventive maintenance';

    /*
     * Urutan ditulis eksplisit sejak nama menunya diganti pada 8 September 2026. Sebelumnya
     * jadwal preventif dan permintaan perbaikan sama sama bernomor 1, dan yang menentukan
     * urutannya adalah abjad namanya. Nama barunya membalik abjad itu, jadi urutan menu akan
     * berubah sendiri tanpa ada yang memintanya. Nomor yang berbeda membuatnya tetap seperti
     * yang sudah dihafal orang: preventif dulu, baru korektif, baru perintah kerjanya.
     */
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Dikirim setiap kali satu kunjungan ditutup.
     *
     * Rekap di halaman jadwal dihitung dari kunjungan, dan kunjungan ditutup dari
     * daftar di bawahnya. Keduanya komponen Livewire yang berbeda, jadi tanpa sinyal ini
     * angka rekapnya tetap memperlihatkan keadaan sebelum tombol ditekan sampai orang
     * menyegarkan halaman sendiri, dan angka yang tidak ikut berubah selalu terbaca
     * sebagai angka yang salah.
     */
    public const REFRESH_EVENT = 'gais-kunjungan-berubah';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Work')
                ->columns(2)
                ->schema([
                    Select::make('asset_id')
                        ->label('Aset')
                        ->relationship(
                            'asset',
                            'name',
                            fn (Builder $query) => $query->where('status', '!=', 'dilepas')->orderBy('code'),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Asset $record): string => $record->code.' '.$record->name)
                        ->searchable(['code', 'name'])
                        ->required()
                        ->helperText('Aset yang sudah dilepas tidak muncul di sini.'),
                    TextInput::make('name')
                        ->label('Nama pekerjaan')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('Contoh: Servis rutin dan cuci AC'),
                    Textarea::make('tasks')
                        ->label('Rincian pekerjaan')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder("Contoh:\nCuci filter dan evaporator\nPeriksa tekanan freon\nPeriksa kebocoran pipa")
                        ->helperText('Daftar ini disalin ke perintah kerja saat dibuat, jadi petugas tidak perlu mengingatnya sendiri.'),
                ]),

            Section::make('Recurrence')
                ->columns(3)
                ->schema([
                    TextInput::make('interval_months')
                        ->label('Diulang tiap (bulan)')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(120)
                        ->default(3)
                        ->helperText('3 berarti tiap tiga bulan.'),
                    DatePicker::make('last_done_date')
                        ->label('Terakhir dikerjakan')
                        ->displayFormat('d M Y')
                        ->maxDate(now())
                        ->placeholder('Belum pernah')
                        ->helperText('Dikosongkan berarti belum pernah, dan jadwalnya langsung jatuh tempo.'),
                    Toggle::make('is_active')
                        ->label('Jadwal berjalan')
                        ->default(true)
                        ->helperText('Matikan kalau pekerjaan ini dihentikan sementara. Riwayatnya tetap tersimpan.'),
                ]),

            Section::make('Assignee & Cost')
                ->columnSpanFull()
                ->columns(3)
                ->description('Boleh dikosongkan. Kalau diisi, keduanya jadi bawaan saat perintah kerja dibuat dari jadwal ini.')
                ->schema([
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
                    Select::make('technician_employee_id')
                        ->label('Teknisi internal')
                        ->relationship('technician', 'full_name', fn (Builder $query) => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->placeholder('Belum ditentukan'),
                    TextInput::make('estimated_cost')
                        ->label('Perkiraan biaya')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('Belum diperkirakan'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Schedule')
                ->columns(3)
                ->schema([
                    TextEntry::make('name')
                        ->label('Pekerjaan'),
                    TextEntry::make('asset.code')
                        ->label('Aset')
                        ->state(fn (MaintenanceSchedule $record): string => trim(($record->asset?->code ?? '').' '.($record->asset?->name ?? ''))
                            ?: 'Tidak tercatat'),
                    TextEntry::make('interval_months')
                        ->label('Perulangan')
                        ->state(fn (MaintenanceSchedule $record): string => $record->intervalLabel()),
                    TextEntry::make('next_due_date')
                        ->label('Jatuh tempo berikutnya')
                        ->state(fn (MaintenanceSchedule $record): string => $record->next_due_date?->translatedFormat('d F Y')
                            ?? 'Belum dijadwalkan')
                        ->badge()
                        ->color(fn (MaintenanceSchedule $record): string => $record->keadaanColor()),
                    TextEntry::make('last_done_date')
                        ->label('Terakhir dikerjakan')
                        ->state(fn (MaintenanceSchedule $record): string => $record->last_done_date?->translatedFormat('d F Y')
                            ?? 'Belum pernah'),
                    TextEntry::make('vendor.name')
                        ->label('Rekanan bawaan')
                        ->placeholder('Dikerjakan sendiri'),
                    TextEntry::make('tasks')
                        ->label('Rincian pekerjaan')
                        ->placeholder('Tidak dirinci')
                        ->columnSpanFull(),
                ]),

            Section::make('Summary')
                ->columns(3)
                ->description('Dihitung dari riwayat kunjungan di bawah, bukan diketik.')
                ->schema([
                    TextEntry::make('jumlah_dikerjakan')
                        ->label('Sudah dikerjakan')
                        ->state(fn (MaintenanceSchedule $record): string => $record->jumlahDikerjakan().' kunjungan'),
                    TextEntry::make('jumlah_dilewati')
                        ->label('Dilewati')
                        ->state(fn (MaintenanceSchedule $record): string => $record->jumlahDilewati().' kunjungan'),
                    TextEntry::make('total_biaya')
                        ->label('Total biaya tercatat')
                        ->state(fn (MaintenanceSchedule $record): string => Rupiah::penuh(
                            (float) $record->visits()->where('status', 'dikerjakan')->sum('cost'),
                        )),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('next_due_date')
                    ->label('Jatuh tempo')
                    ->date('d M Y')
                    ->description(fn (MaintenanceSchedule $record): string => $record->keadaanLabel())
                    ->color(fn (MaintenanceSchedule $record): string => $record->keadaanColor())
                    ->placeholder('Belum dijadwalkan')
                    ->sortable(),
                TextColumn::make('asset.code')
                    ->label('Kode aset')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Pekerjaan')
                    ->description(fn (MaintenanceSchedule $record): ?string => $record->asset?->name)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('interval_months')
                    ->label('Perulangan')
                    ->state(fn (MaintenanceSchedule $record): string => $record->intervalLabel())
                    ->sortable(),
                TextColumn::make('last_done_date')
                    ->label('Terakhir')
                    ->date('d M Y')
                    ->placeholder('Belum pernah')
                    ->sortable(),
                TextColumn::make('pelaksana')
                    ->label('Pelaksana')
                    ->state(fn (MaintenanceSchedule $record): string => $record->vendor?->name
                        ?? $record->technician?->full_name
                        ?? 'Belum ditentukan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('asset.location.name')
                    ->label('Lokasi')
                    ->placeholder('Belum ditentukan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estimated_cost')
                    ->label('Perkiraan biaya')
                    ->state(fn (MaintenanceSchedule $record): string => filled($record->estimated_cost)
                        ? Rupiah::penuh((float) $record->estimated_cost)
                        : 'Belum diperkirakan')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Daftar tugas, bukan arsip: yang paling mendesak di atas.
            ->defaultSort('next_due_date')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->filters([
                Filter::make('terlewat')
                    ->label('Sudah lewat jatuh tempo')
                    ->query(fn (Builder $query): Builder => $query->terlewat()),
                Filter::make('bulan_ini')
                    ->label('Jatuh tempo dalam 30 hari')
                    ->query(fn (Builder $query): Builder => $query->jatuhTempo(30)),
                SelectFilter::make('vendor_id')
                    ->label('Rekanan')
                    ->relationship('vendor', 'name')
                    ->searchable(),
                SelectFilter::make('kategori_aset')
                    ->label('Kategori aset')
                    ->options(fn (): array => AssetCategory::query()
                        ->where('is_active', true)
                        ->orderBy('code')
                        ->get()
                        ->mapWithKeys(fn (AssetCategory $k) => [$k->id => $k->pickerLabel()])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('asset', fn (Builder $query) => $query->where('asset_category_id', $data['value']))
                        : $query),
                TernaryFilter::make('is_active')
                    ->label('Jadwal berjalan')
                    ->placeholder('Semua')
                    ->trueLabel('Hanya yang berjalan')
                    ->falseLabel('Hanya yang dihentikan'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Riwayat')
                    ->icon('heroicon-o-clock')
                    ->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->emptyStateHeading('Belum ada jadwal pemeliharaan')
            ->emptyStateDescription('Jadwal preventif menyebut pekerjaan apa, pada aset mana, dan diulang tiap berapa bulan. Setelah dibuat, jatuh temponya bergerak sendiri setiap kali perintah kerjanya diselesaikan.');
    }

    /**
     * Mencatat bahwa kunjungan ini sudah dikerjakan.
     *
     * Ini jalur cepat untuk kunjungan vendor rutin: vendor datang, mengerjakan, pergi,
     * dan yang perlu dicatat hanya tanggal, siapa, dan apa hasilnya. Membuat perintah
     * kerja untuk pekerjaan seperti itu hanya menambah satu layar tanpa menambah
     * informasi. Perintah kerja tetap ada untuk pekerjaan yang perlu penugasan, lampiran
     * foto, dan uraian panjang.
     */
    public static function catatKunjunganAction(): Action
    {
        return Action::make('catat_kunjungan')
            ->label('Mark as Done')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->iconButton()
            ->visible(fn (MaintenanceVisit $record): bool => $record->isOpen())
            ->modalHeading(fn (MaintenanceVisit $record): string => 'Log Visit '.$record->sequence)
            ->modalDescription(fn (MaintenanceVisit $record): string => 'Jatuh tempo kunjungan ini '.$record->due_date->translatedFormat('d F Y').'. Setelah disimpan, kunjungan berikutnya dibuat sendiri, dihitung dari tanggal pengerjaan di bawah ditambah interval jadwalnya.')
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn (MaintenanceVisit $record): array => [
                'completed_date' => now()->toDateString(),
                'vendor_id' => $record->vendor_id,
                'technician_employee_id' => $record->technician_employee_id,
            ])
            ->schema([
                DatePicker::make('completed_date')
                    ->label('Tanggal dikerjakan')
                    ->displayFormat('d M Y')
                    ->required()
                    ->maxDate(now())
                    ->helperText('Tanggal vendor atau teknisi benar benar datang, bukan tanggal pencatatan.'),
                Select::make('vendor_id')
                    ->label('Dikerjakan oleh rekanan')
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
                    ->options(fn (): array => Employee::query()
                        ->where('is_active', true)
                        ->orderBy('full_name')
                        ->pluck('full_name', 'id')
                        ->all())
                    ->searchable()
                    ->placeholder('Tidak ada'),
                Textarea::make('result')
                    ->label('Hasil pekerjaan')
                    ->rows(3)
                    ->required()
                    ->placeholder('Contoh: filter dan evaporator dicuci, freon ditambah 0,3 kg, tidak ada kebocoran.'),
                TextInput::make('cost')
                    ->label('Biaya')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Tidak ada biaya'),
            ])
            ->action(function (MaintenanceVisit $record, array $data, $livewire): void {
                $record->fill($data + ['status' => 'dikerjakan'])->save();
                $record->refresh();

                $livewire->dispatch(self::REFRESH_EVENT);

                $berikutnya = $record->maintenanceSchedule?->refresh()->next_due_date;

                Notification::make()
                    ->success()
                    ->title('Kunjungan ke '.$record->sequence.' tercatat')
                    ->body('Kunjungan berikutnya jatuh tempo '
                        .($berikutnya?->translatedFormat('d F Y') ?? 'belum ditentukan').'.')
                    ->send();
            });
    }

    /**
     * Menandai kunjungan ini sengaja tidak dikerjakan.
     *
     * Tanpa tombol ini, satu satunya cara melewatkan servis adalah dengan tidak melakukan
     * apa apa, dan setahun kemudian tidak ada yang bisa membedakan keputusan yang diambil
     * sadar dari kelalaian. Karena itu alasannya wajib diisi.
     */
    public static function lewatiKunjunganAction(): Action
    {
        return Action::make('lewati_kunjungan')
            ->label('Skip')
            ->icon('heroicon-o-forward')
            ->color('gray')
            ->iconButton()
            ->visible(fn (MaintenanceVisit $record): bool => $record->isOpen())
            ->modalHeading(fn (MaintenanceVisit $record): string => 'Skip Visit '.$record->sequence)
            ->modalDescription(fn (MaintenanceVisit $record): string => 'Kunjungan yang jatuh tempo '.$record->due_date->translatedFormat('d F Y').' ditandai tidak dikerjakan, dan alasannya ikut tersimpan di riwayat. Kunjungan berikutnya dihitung dari tanggal jatuh tempo ini, bukan dari hari ini, supaya siklusnya tetap menempel di kalender.')
            ->modalSubmitActionLabel('Mark as Skipped')
            ->schema([
                Textarea::make('skip_reason')
                    ->label('Alasan tidak dikerjakan')
                    ->rows(3)
                    ->required()
                    ->placeholder('Contoh: unit sedang tidak dipakai karena ruangannya direnovasi.'),
            ])
            ->action(function (MaintenanceVisit $record, array $data, $livewire): void {
                $record->fill($data + ['status' => 'dilewati'])->save();
                $record->refresh();

                $livewire->dispatch(self::REFRESH_EVENT);

                $berikutnya = $record->maintenanceSchedule?->refresh()->next_due_date;

                Notification::make()
                    ->warning()
                    ->title('Kunjungan ke '.$record->sequence.' ditandai dilewati')
                    ->body('Kunjungan berikutnya jatuh tempo '
                        .($berikutnya?->translatedFormat('d F Y') ?? 'belum ditentukan').'.')
                    ->send();
            });
    }

    /**
     * Membuat perintah kerja untuk satu kunjungan. Isinya terisi dari jadwalnya, jadi
     * yang tersisa bagi petugas hanya menekan tombol.
     *
     * Disembunyikan kalau kunjungan ini sudah punya perintah kerja yang belum selesai.
     * Dua perintah kerja terbuka untuk satu kunjungan hanya akan membuat salah satunya
     * diselesaikan dan satunya menggantung selamanya.
     */
    public static function buatPerintahKerjaAction(): Action
    {
        return Action::make('buat_perintah_kerja')
            ->label('Create Work Order')
            ->icon('heroicon-o-wrench-screwdriver')
            ->color('primary')
            ->iconButton()
            ->requiresConfirmation()
            ->modalHeading(fn (MaintenanceVisit $record): string => 'Create Work Order for Visit '.$record->sequence)
            ->modalDescription(fn (MaintenanceVisit $record): string => 'Dipakai kalau pekerjaannya perlu penugasan, lampiran foto, atau uraian panjang. Untuk kunjungan vendor rutin, tombol Catat sudah dikerjakan lebih cepat. Menyelesaikan perintah kerjanya nanti ikut menutup kunjungan ini.')
            ->modalSubmitActionLabel('Create Work Order')
            ->visible(fn (MaintenanceVisit $record): bool => $record->isOpen()
                && ! $record->workOrders()->whereIn('status', WorkOrder::OPEN_STATUSES)->exists())
            ->action(function (MaintenanceVisit $record, $livewire): void {
                $jadwal = $record->maintenanceSchedule;

                $wo = WorkOrder::query()->create([
                    'asset_id' => $record->asset_id,
                    'type' => 'preventif',
                    'maintenance_schedule_id' => $record->maintenance_schedule_id,
                    'maintenance_visit_id' => $record->getKey(),
                    'priority' => 'normal',
                    'status' => 'dibuka',
                    'reported_date' => now()->toDateString(),
                    'problem' => ($jadwal?->name ?? 'Pemeliharaan preventif')
                        .(filled($jadwal?->tasks) ? "\n\n".$jadwal->tasks : ''),
                    'scheduled_date' => $record->due_date->toDateString(),
                    'vendor_id' => $record->vendor_id,
                    'technician_employee_id' => $record->technician_employee_id,
                ]);

                $livewire->dispatch(self::REFRESH_EVENT);

                Notification::make()
                    ->success()
                    ->title('Perintah kerja '.$wo->code.' dibuat')
                    ->body('Menyelesaikannya nanti otomatis menutup kunjungan ke '.$record->sequence.'.')
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VisitsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceSchedules::route('/'),
            // Halaman tersendiri, bukan kotak, karena isinya riwayat yang bisa panjang
            // dan sering perlu ditautkan ke orang lain saat vendor ditagih.
            'view' => ViewMaintenanceSchedule::route('/{record}'),
        ];
    }
}
