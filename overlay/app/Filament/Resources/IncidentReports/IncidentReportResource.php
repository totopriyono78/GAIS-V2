<?php

namespace App\Filament\Resources\IncidentReports;

use App\Filament\Resources\IncidentReports\Pages\CreateIncidentReport;
use App\Filament\Resources\IncidentReports\Pages\ListIncidentReports;
use App\Filament\Resources\IncidentReports\Pages\ViewIncidentReport;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\Employee;
use App\Models\IncidentReport;
use App\Models\Location;
use App\Models\SecurityShift;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceStaff;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Laporan kejadian keamanan.
 *
 * Insiden yang butuh perbaikan fisik diteruskan menjadi tiket perbaikan, dan sejak saat itu
 * pekerjaannya hidup di sana. Insiden ini berhenti menjadi pekerjaan dan berubah menjadi
 * catatan, pola yang sama persis dengan tiket yang berubah menjadi perintah kerja sejak
 * kiriman G.
 */
class IncidentReportResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = IncidentReport::class;

    protected static string $moduleCode = 'incident_reports';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|UnitEnum|null $navigationGroup = 'Facility Services';

    protected static ?string $navigationLabel = 'Incident Reports';

    protected static ?string $modelLabel = 'incident report';

    protected static ?string $pluralModelLabel = 'incident reports';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Catat apa yang terjadi selengkap yang Anda tahu. Insiden yang butuh perbaikan fisik bisa diteruskan menjadi tiket perbaikan setelah tersimpan.')
                ->columns(2)
                ->schema([
                    DateTimePicker::make('occurred_at')
                        ->label('Waktu kejadian')
                        ->native(false)
                        ->seconds(false)
                        ->displayFormat('d M Y, H:i')
                        ->default(now())
                        ->required()
                        ->maxDate(now())
                        ->helperText('Waktu kejadiannya, bukan waktu laporannya ditulis.'),

                    Select::make('category')
                        ->label('Jenis kejadian')
                        ->options(IncidentReport::CATEGORIES)
                        ->required()
                        ->native(false),

                    Select::make('severity')
                        ->label('Berat kejadian')
                        ->options(IncidentReport::SEVERITIES)
                        ->default('sedang')
                        ->required()
                        ->helperText('Menentukan prioritas yang diusulkan kalau insiden ini nanti diteruskan menjadi tiket perbaikan.'),

                    Select::make('location_id')
                        ->label('Tempat kejadian')
                        ->options(fn (): array => Location::query()
                            ->where('is_active', true)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (Location $lokasi) => [
                                $lokasi->id => $lokasi->code.' '.$lokasi->name,
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->placeholder('Tidak dicatat'),

                    Select::make('reported_by_staff_id')
                        ->label('Dilaporkan petugas')
                        ->options(fn (): array => ServiceStaff::query()
                            ->keamanan()
                            ->where('is_active', true)
                            ->with('employee')
                            ->get()
                            ->mapWithKeys(fn (ServiceStaff $petugas) => [
                                $petugas->id => $petugas->namaLengkap(),
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->placeholder('Bukan dari petugas keamanan')
                        ->helperText('Dikosongkan kalau yang melapor karyawan biasa atau tamu.'),

                    Select::make('security_shift_id')
                        ->label('Shift yang sedang jaga')
                        ->options(fn (): array => SecurityShift::query()
                            ->with('staff.employee')
                            ->orderByDesc('shift_date')
                            ->limit(60)
                            ->get()
                            ->mapWithKeys(fn (SecurityShift $jaga) => [
                                $jaga->id => $jaga->shift_date->format('d M Y').' '.$jaga->shiftLabel()
                                    .', '.$jaga->petugasDijadwalkan(),
                            ])
                            ->all())
                        ->searchable()
                        ->placeholder('Tidak dihubungkan ke shift mana pun')
                        ->helperText('Hanya 60 jadwal terbaru yang muncul. Boleh dikosongkan kalau kejadiannya di luar jam jaga.'),

                    Textarea::make('description')
                        ->label('Uraian kejadian')
                        ->rows(4)
                        ->required()
                        ->maxLength(2000)
                        ->placeholder('Apa yang terjadi, siapa yang terlibat, dan apa yang sudah dilakukan di tempat')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Incident')
                ->columns(2)
                ->schema([
                    TextEntry::make('code')
                        ->label('Nomor')
                        ->fontFamily('mono'),
                    TextEntry::make('occurred_at')
                        ->label('Waktu kejadian')
                        ->dateTime('d M Y, H:i'),
                    TextEntry::make('category')
                        ->label('Jenis kejadian')
                        ->state(fn (IncidentReport $record): string => $record->categoryLabel()),
                    TextEntry::make('severity')
                        ->label('Berat kejadian')
                        ->badge()
                        ->state(fn (IncidentReport $record): string => $record->severityLabel())
                        ->color(fn (IncidentReport $record): string => $record->severityColor()),
                    TextEntry::make('location.name')
                        ->label('Tempat kejadian')
                        ->placeholder('Tidak dicatat'),
                    TextEntry::make('pelapor')
                        ->label('Dilaporkan petugas')
                        ->state(fn (IncidentReport $record): ?string => $record->reporter?->namaLengkap())
                        ->placeholder('Bukan dari petugas keamanan'),
                    TextEntry::make('shift')
                        ->label('Shift yang sedang jaga')
                        ->state(fn (IncidentReport $record): ?string => $record->shift === null
                            ? null
                            : $record->shift->shift_date->format('d M Y').' '.$record->shift->shiftLabel()
                                .', '.$record->shift->kehadiranKalimat())
                        ->placeholder('Tidak dihubungkan ke shift mana pun'),
                    TextEntry::make('tindak_lanjut')
                        ->label('Tindak lanjut')
                        ->badge()
                        ->state(fn (IncidentReport $record): string => $record->tindakLanjutLabel())
                        ->color(fn (IncidentReport $record): string => $record->tindakLanjutColor()),
                    TextEntry::make('description')
                        ->label('Uraian kejadian')
                        ->columnSpanFull(),
                ]),

            Section::make('Handling')
                ->columns(2)
                ->schema([
                    TextEntry::make('tiket')
                        ->label('Tiket perbaikan')
                        ->state(fn (IncidentReport $record): ?string => $record->serviceRequest === null
                            ? null
                            : $record->serviceRequest->code.', '.$record->serviceRequest->statusLabel())
                        ->placeholder('Belum ada, dan mungkin memang tidak perlu')
                        ->helperText(fn (IncidentReport $record): ?string => $record->punyaTiket()
                            ? 'Pekerjaan perbaikannya hidup di tiket itu, bukan di halaman ini.'
                            : null),
                    TextEntry::make('closed_at')
                        ->label('Ditutup')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('Belum ditutup'),
                    TextEntry::make('closing_note')
                        ->label('Catatan penutup')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),
                    TextEntry::make('createdByUser.name')
                        ->label('Dicatat oleh')
                        ->placeholder('Tidak tercatat'),
                    TextEntry::make('closedByUser.name')
                        ->label('Ditutup oleh')
                        ->placeholder('Belum ditutup'),
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
                TextColumn::make('occurred_at')
                    ->label('Waktu kejadian')
                    ->dateTime('d M Y, H:i')
                    ->description(fn (IncidentReport $record): ?string => $record->location?->name)
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Jenis')
                    ->state(fn (IncidentReport $record): string => $record->categoryLabel())
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where('description', 'ilike', '%'.$search.'%')),
                TextColumn::make('severity')
                    ->label('Berat')
                    ->badge()
                    ->state(fn (IncidentReport $record): string => $record->severityLabel())
                    ->color(fn (IncidentReport $record): string => $record->severityColor())
                    ->sortable(),
                TextColumn::make('tindak_lanjut')
                    ->label('Tindak lanjut')
                    ->badge()
                    ->state(fn (IncidentReport $record): string => $record->tindakLanjutLabel())
                    ->color(fn (IncidentReport $record): string => $record->tindakLanjutColor()),
                TextColumn::make('pelapor')
                    ->label('Dilaporkan')
                    ->state(fn (IncidentReport $record): ?string => $record->reporter?->namaLengkap())
                    ->placeholder('Bukan petugas keamanan'),
                TextColumn::make('description')
                    ->label('Uraian')
                    ->wrap()
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dicatat oleh')
                    ->placeholder('Tidak tercatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['location', 'reporter.employee', 'serviceRequest', 'createdByUser']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('category')
                    ->label('Jenis kejadian')
                    ->options(IncidentReport::CATEGORIES),
                SelectFilter::make('severity')
                    ->label('Berat kejadian')
                    ->options(IncidentReport::SEVERITIES),
                Filter::make('menunggu')
                    ->label('Belum ditindaklanjuti')
                    ->query(fn (Builder $query): Builder => $query->menunggu()),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                static::buatTiketAction(),
                static::tutupAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (IncidentReport $record): bool => ! $record->isClosed()),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (IncidentReport $record): bool => ! $record->punyaTiket()),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada insiden yang cocok'
                : 'Belum ada laporan insiden')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada insiden yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh laporan lagi.'
                : 'Belum ada kejadian yang dicatat, dan itu kabar baik. Catat di sini kalau ada kehilangan, kerusakan, orang tidak dikenal, atau kejadian lain yang perlu diingat.');
    }

    // ---------------------------------------------------------------- tindakan

    protected static function segarkan(mixed $livewire, IncidentReport $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    /**
     * Meneruskan insiden menjadi tiket perbaikan.
     *
     * Tiketnya dibuat atas nama orang yang menekan tombol, bukan atas nama petugas keamanan
     * yang melapor. Alasannya bukan sepele: alur tiket sejak kiriman G menentukan penyetuju
     * dari departemen pemohonnya, dan petugas keamanan yang tenaga rekanan tidak punya
     * departemen sama sekali. Membuat tiket atas namanya berarti membuat tiket yang tidak
     * pernah bisa disetujui siapa pun.
     */
    public static function buatTiketAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('buat_tiket')
            ->label('Create Repair Ticket')
            ->icon('heroicon-o-wrench-screwdriver')
            ->color('primary')
            ->visible(fn (IncidentReport $record): bool => ! $record->punyaTiket()
                && ! $record->isClosed()
                && ServiceRequestResource::canCreate())
            ->modalHeading(fn (IncidentReport $record): string => 'Create Repair Ticket from '.$record->code)
            ->modalDescription(function (IncidentReport $record): string {
                $karyawan = static::karyawanPengguna();

                if ($karyawan === null) {
                    return 'Akun Anda belum tertaut ke kartu karyawan mana pun, jadi tiket tidak bisa dibuat atas nama Anda. Tiket perbaikan selalu punya pemohon, dan pemohonnya menentukan siapa yang menyetujui. Mintalah administrator menautkan akun Anda ke kartu karyawan lebih dulu.';
                }

                return 'Tiketnya dibuat atas nama '.$karyawan->full_name
                    .', karena tiket perbaikan selalu punya pemohon dan pemohonnya yang menentukan siapa yang menyetujui. '
                    .'Uraian insiden ikut disalin ke tiket, dan insiden ini akan menunjuk ke tiket itu.';
            })
            ->modalSubmitActionLabel('Create Ticket')
            ->fillForm(fn (IncidentReport $record): array => [
                'title' => $record->usulanJudulTiket(),
                'description' => $record->description,
                'priority' => $record->usulanPrioritasTiket(),
            ])
            ->schema([
                Select::make('service_request_category_id')
                    ->label('Jenis permintaan')
                    ->options(fn (): array => ServiceRequestCategory::query()
                        ->where('is_active', true)
                        ->orderBy('code')
                        ->get()
                        ->mapWithKeys(fn (ServiceRequestCategory $jenis) => [$jenis->id => $jenis->pickerLabel()])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Menentukan target waktu penyelesaian tiketnya.'),
                TextInput::make('title')
                    ->label('Judul tiket')
                    ->required()
                    ->maxLength(150),
                Select::make('priority')
                    ->label('Prioritas')
                    ->options(ServiceRequest::PRIORITIES)
                    ->required()
                    ->helperText('Sudah diisi mengikuti berat insidennya, dan masih boleh diubah.'),
                Textarea::make('description')
                    ->label('Uraian untuk tim perbaikan')
                    ->rows(4)
                    ->required()
                    ->maxLength(2000),
            ])
            ->action(function (IncidentReport $record, array $data, Action $action, $livewire): void {
                $karyawan = static::karyawanPengguna();

                if ($karyawan === null) {
                    Notification::make()
                        ->warning()
                        ->title('Tiket tidak bisa dibuat')
                        ->body('Akun Anda belum tertaut ke kartu karyawan, jadi tiketnya tidak punya pemohon.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                if ($record->punyaTiket()) {
                    Notification::make()
                        ->warning()
                        ->title('Sudah ada tiketnya')
                        ->body('Insiden ini sudah menunjuk ke '.$record->serviceRequest?->code.'. Muat ulang halamannya untuk melihat keadaan terbaru.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                $tiket = ServiceRequest::query()->create([
                    'requester_employee_id' => $karyawan->id,
                    'service_request_category_id' => $data['service_request_category_id'],
                    'location_id' => $record->location_id,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'priority' => $data['priority'],
                ]);

                $record->forceFill(['service_request_id' => $tiket->id])->save();

                Notification::make()
                    ->success()
                    ->title($tiket->code.' dibuat')
                    ->body('Insiden '.$record->code.' sekarang menunjuk ke tiket itu, dan pekerjaan perbaikannya berjalan di sana.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function tutupAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tutup')
            ->label('Close Incident')
            ->icon('heroicon-o-check-circle')
            ->color('gray')
            ->visible(fn (IncidentReport $record): bool => ! $record->isClosed() && static::allows('update'))
            ->modalHeading(fn (IncidentReport $record): string => 'Close '.$record->code)
            ->modalDescription(fn (IncidentReport $record): string => $record->punyaTiket()
                ? 'Menutup insiden berarti urusan keamanannya selesai. Tiket perbaikan '
                    .$record->serviceRequest?->code.' tidak ikut ditutup, dan pekerjaannya tetap berjalan di sana.'
                : 'Menutup insiden berarti tidak ada lagi yang perlu dikerjakan atasnya. Catatannya tetap tersimpan dan tetap terbaca.')
            ->modalSubmitActionLabel('Close Incident')
            ->schema([
                Textarea::make('closing_note')
                    ->label('Catatan penutup')
                    ->rows(3)
                    ->required()
                    ->maxLength(1000)
                    ->helperText('Sebutkan bagaimana kejadian ini berakhir. Inilah yang dibaca orang setahun kemudian saat kejadian serupa terulang.'),
            ])
            ->action(function (IncidentReport $record, array $data, $livewire): void {
                $record->forceFill([
                    'closed_at' => now(),
                    'closing_note' => $data['closing_note'],
                    'closed_by_user_id' => Auth::id(),
                ])->save();

                Notification::make()
                    ->success()
                    ->title($record->code.' ditutup')
                    ->body('Catatannya tetap terbaca di daftar insiden.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /** Kartu karyawan milik pengguna yang sedang masuk, atau null kalau akunnya belum tertaut. */
    protected static function karyawanPengguna(): ?Employee
    {
        $id = Auth::id();

        return $id === null
            ? null
            : Employee::query()->where('user_id', $id)->first();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidentReports::route('/'),
            'create' => CreateIncidentReport::route('/create'),
            'view' => ViewIncidentReport::route('/{record}'),
        ];
    }
}
