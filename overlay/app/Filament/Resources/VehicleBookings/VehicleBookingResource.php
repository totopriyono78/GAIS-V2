<?php

namespace App\Filament\Resources\VehicleBookings;

use App\Filament\Resources\VehicleBookings\Pages\CreateVehicleBooking;
use App\Filament\Resources\VehicleBookings\Pages\ListVehicleBookings;
use App\Filament\Resources\VehicleBookings\Pages\ViewVehicleBooking;
use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\VehicleBooking;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Pemesanan kendaraan.
 *
 * Modul ini ada untuk satu masalah: dua orang yang mengira mobil yang sama kosong pada
 * jam yang sama. Karena itu bagian terpenting layar ini bukan formulirnya, melainkan
 * pemeriksaan bentrok saat tim GA menugaskan kendaraan, dan kalender kecil berupa
 * daftar yang selalu terurut menurut waktu berangkat.
 *
 * Alurnya kembar dengan permintaan perbaikan, sengaja. Orang yang memesan mobil adalah
 * orang yang sama yang melaporkan AC bocor, dan dua alur berbeda untuk dua hal yang
 * sama sama "minta lalu disetujui" hanya membuang waktu mereka.
 */
class VehicleBookingResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = VehicleBooking::class;

    protected static string $moduleCode = 'vehicle_bookings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Vehicles';

    protected static ?string $navigationLabel = 'Vehicle Bookings';

    protected static ?string $modelLabel = 'vehicle booking';

    protected static ?string $pluralModelLabel = 'vehicle bookings';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getNavigationBadge(): ?string
    {
        $jumlah = static::antrianSaya()->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return VehicleBooking::query()->terlambatDitutup()->exists() ? 'danger' : 'warning';
    }

    /** Pemesanan yang menunggu tindakan orang yang sedang masuk. */
    protected static function antrianSaya(): Builder
    {
        $query = static::getEloquentQuery();

        $bisaTugaskan = static::allows('assign');
        $karyawan = Auth::user()?->employee;
        $departemenSaya = $karyawan?->headedDepartments()->pluck('id')->all() ?? [];

        if (! $bisaTugaskan && $departemenSaya === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $sub) use ($bisaTugaskan, $departemenSaya): void {
            if ($bisaTugaskan) {
                $sub->orWhere(fn (Builder $q) => $q->menungguKendaraan());
                $sub->orWhere(fn (Builder $q) => $q->terlambatDitutup());
            }

            if ($departemenSaya !== []) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPersetujuan()->whereIn('department_id', $departemenSaya));
            }
        });
    }

    /**
     * Sama seperti permintaan perbaikan: tanpa izin read_all, seseorang hanya melihat
     * pemesanannya sendiri dan pemesanan departemen yang ia kepalai.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::allows('read_all')) {
            return $query;
        }

        $karyawan = Auth::user()?->employee;

        if ($karyawan === null) {
            return $query->whereRaw('1 = 0');
        }

        $departemenSaya = $karyawan->headedDepartments()->pluck('id')->all();

        return $query->where(function (Builder $sub) use ($karyawan, $departemenSaya): void {
            $sub->where('requester_employee_id', $karyawan->getKey());

            if ($departemenSaya !== []) {
                $sub->orWhereIn('department_id', $departemenSaya);
            }
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Planned Trip')
                ->columns(2)
                ->schema([
                    TextInput::make('destination')
                        ->label('Tujuan')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->placeholder('Contoh: Kantor pajak Jakarta Selatan, lalu gudang Bekasi'),
                    Textarea::make('purpose')
                        ->label('Keperluan')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull()
                        ->placeholder('Contoh: mengantar dokumen pajak tahunan dan menjemput sampel barang.')
                        ->helperText('Keperluan ini yang dibaca atasan saat menyetujui, jadi tulis secukupnya untuk bisa disetujui tanpa bertanya balik.'),
                    DateTimePicker::make('start_at')
                        ->label('Berangkat')
                        ->seconds(false)
                        ->displayFormat('d M Y, H:i')
                        ->required()
                        ->live(),
                    DateTimePicker::make('end_at')
                        ->label('Perkiraan kembali')
                        ->seconds(false)
                        ->displayFormat('d M Y, H:i')
                        ->required()
                        ->after('start_at')
                        ->helperText('Perkiraan saja, dan boleh meleset. Angka ini dipakai memeriksa bentrok dengan pemesanan lain, jadi lebih baik dilebihkan daripada dikurangi.'),
                    TextInput::make('passenger_count')
                        ->label('Jumlah penumpang')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(60)
                        ->placeholder('Tidak dicatat')
                        ->helperText('Dipakai tim GA memilih kendaraan yang cukup.'),
                    Toggle::make('needs_driver')
                        ->label('Perlu sopir')
                        ->helperText('Matikan kalau pemohon menyetir sendiri.'),
                ]),

            Section::make('Requester')
                ->columns(2)
                ->schema([
                    Select::make('requester_employee_id')
                        ->label('Yang memesan')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->default(fn (): ?int => Auth::user()?->employee?->getKey())
                        ->helperText('Bawaannya diri sendiri. Ganti kalau Anda memesan mewakili orang lain.'),
                    Placeholder::make('jalur_persetujuan')
                        ->label('Setelah diajukan')
                        ->content(function ($get): string {
                            if (blank($get('requester_employee_id'))) {
                                return 'Pilih dulu siapa yang memesan.';
                            }

                            $contoh = new VehicleBooking(['requester_employee_id' => $get('requester_employee_id')]);
                            $contoh->department_id = Employee::find($get('requester_employee_id'))?->department_id;

                            $alasan = $contoh->alasanLewatPersetujuan();

                            return $alasan === null
                                ? 'Menunggu persetujuan '.($contoh->calonPenyetuju()?->full_name ?? 'kepala departemen').', lalu tim GA menugaskan kendaraannya.'
                                : 'Langsung menunggu tim GA menugaskan kendaraan. Alasannya: '.strtolower($alasan).'.';
                        }),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: barang yang dibawa memakan bagasi penuh.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Booking')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (VehicleBooking $record): string => $record->statusLabel())
                        ->color(fn (VehicleBooking $record): string => $record->statusColor()),
                    TextEntry::make('jadwal')
                        ->label('Jadwal')
                        ->state(fn (VehicleBooking $record): string => $record->jadwalLabel()),
                    TextEntry::make('destination')->label('Tujuan')->columnSpanFull(),
                    TextEntry::make('purpose')->label('Keperluan')->columnSpanFull(),
                    TextEntry::make('requester.full_name')->label('Pemesan')->placeholder('Tidak tercatat'),
                    TextEntry::make('department.name')->label('Departemen saat memesan')->placeholder('Tidak tercatat'),
                    TextEntry::make('penumpang')
                        ->label('Penumpang')
                        ->state(fn (VehicleBooking $record): string => $record->penumpangLabel()
                            .($record->needs_driver ? ', perlu sopir' : ', menyetir sendiri')),
                ]),

            Section::make('Vehicle & Approval')
                ->columns(3)
                ->schema([
                    TextEntry::make('persetujuan')
                        ->label('Persetujuan')
                        ->columnSpanFull()
                        ->state(function (VehicleBooking $record): string {
                            if (filled($record->approval_skipped_reason)) {
                                return 'Lewat persetujuan. '.$record->approval_skipped_reason.'.';
                            }

                            if (blank($record->approved_at)) {
                                return 'Menunggu '.($record->approver?->full_name ?? 'kepala departemen').'.';
                            }

                            return 'Disetujui '.($record->approver?->full_name ?? 'kepala departemen')
                                .' pada '.$record->approved_at->translatedFormat('d F Y, H:i')
                                .(filled($record->approval_note) ? '. Catatan: '.$record->approval_note : '.');
                        }),
                    TextEntry::make('vehicle.plate_number')
                        ->label('Kendaraan')
                        ->state(fn (VehicleBooking $record): string => $record->vehicle
                            ? $record->vehicle->plate_number.' '.$record->vehicle->namaLengkap()
                            : 'Belum ditugaskan')
                        ->url(fn (VehicleBooking $record): ?string => $record->vehicle
                            ? VehicleResource::getUrl('view', ['record' => $record->vehicle])
                            : null),
                    TextEntry::make('driver.full_name')
                        ->label('Sopir')
                        ->placeholder('Menyetir sendiri'),
                    TextEntry::make('assigned_at')
                        ->label('Ditugaskan')
                        ->state(fn (VehicleBooking $record): string => $record->assigned_at
                            ? $record->assigned_at->translatedFormat('d F Y, H:i')
                                .' oleh '.($record->assignedByUser?->name ?? 'pengguna yang sudah dihapus')
                            : 'Belum ditugaskan'),
                    TextEntry::make('perjalanan')
                        ->label('Perjalanan')
                        ->columnSpanFull()
                        ->state(function (VehicleBooking $record): string {
                            $perjalanan = $record->trips()->orderBy('departed_at')->get();

                            if ($perjalanan->isEmpty()) {
                                return 'Belum ada perjalanan yang dicatat. Perjalanan dicatat dari halaman kendaraannya, dan pemesanan ini ikut selesai saat perjalanannya ditutup.';
                            }

                            return $perjalanan
                                ->map(fn ($p): string => $p->departed_at->translatedFormat('d M Y, H:i').' ke '.$p->destination.', '.$p->jarakLabel())
                                ->implode(". \n");
                        }),
                    TextEntry::make('rejection_reason')
                        ->label('Alasan ditolak')
                        ->columnSpanFull()
                        ->visible(fn (VehicleBooking $record): bool => filled($record->rejection_reason)),
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
                TextColumn::make('start_at')
                    ->label('Jadwal')
                    ->state(fn (VehicleBooking $record): string => $record->jadwalLabel())
                    ->description(fn (VehicleBooking $record): string => 'Sekitar '.$record->lamaLabel())
                    ->sortable()
                    ->wrap(),
                TextColumn::make('destination')
                    ->label('Tujuan')
                    ->description(fn (VehicleBooking $record): string => $record->penumpangLabel())
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('requester.full_name')
                    ->label('Pemesan')
                    ->description(fn (VehicleBooking $record): ?string => $record->department?->name)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Kendaraan')
                    ->description(fn (VehicleBooking $record): ?string => $record->driver?->full_name
                        ?? ($record->needs_driver ? 'Sopir belum ditentukan' : 'Menyetir sendiri'))
                    ->placeholder('Belum ditugaskan')
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (VehicleBooking $record): string => $record->statusLabel())
                    ->color(fn (VehicleBooking $record): string => $record->statusColor())
                    ->sortable(),
                TextColumn::make('approver.full_name')
                    ->label('Penyetuju')
                    ->placeholder('Lewat persetujuan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Yang paling dekat berangkat ada di atas, karena itu yang perlu disiapkan
            // lebih dulu. Yang sudah selesai turun ke bawah dengan sendirinya.
            ->defaultSort('start_at', 'asc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['requester', 'department', 'vehicle', 'driver'])
                ->orderByRaw("case when status in ('diajukan','disetujui','ditugaskan','berjalan') then 0 else 1 end"))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_persetujuan')
                    ->label('Menunggu persetujuan')
                    ->query(fn (Builder $query): Builder => $query->menungguPersetujuan()),
                Filter::make('menunggu_kendaraan')
                    ->label('Menunggu kendaraan')
                    ->query(fn (Builder $query): Builder => $query->menungguKendaraan()),
                Filter::make('terlambat_ditutup')
                    ->label('Jadwalnya sudah lewat, belum ditutup')
                    ->query(fn (Builder $query): Builder => $query->terlambatDitutup()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(VehicleBooking::STATUSES)
                    ->multiple(),
                SelectFilter::make('vehicle_id')
                    ->label('Kendaraan')
                    ->relationship('vehicle', 'plate_number')
                    ->searchable(),
            ])
            ->recordActions([
                static::setujuiAction(),
                static::tugaskanAction(),
                static::tolakAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (VehicleBooking $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (VehicleBooking $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada pemesanan kendaraan')
            ->emptyStateDescription('Pemesanan dibuat karyawan lewat tombol di kanan atas. Setelah disetujui atasannya, tim GA memilih kendaraan yang kosong pada jam itu, dan aplikasi menolak kendaraan yang jadwalnya bertumpuk dengan pemesanan lain.');
    }

    // ------------------------------------------------------------------ tindakan

    public static function setujuiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('setujui')
            ->label('Approve')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (VehicleBooking $record): bool => $record->status === 'diajukan' && static::bolehMenyetujui($record))
            ->modalHeading(fn (VehicleBooking $record): string => 'Approve '.$record->code)
            ->modalDescription('Setelah disetujui, pemesanan ini masuk antrean tim GA untuk ditentukan kendaraannya. Kendaraan belum dipesan sampai langkah itu selesai.')
            ->modalSubmitActionLabel('Approve Booking')
            ->schema([
                Textarea::make('approval_note')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan.'),
            ])
            ->action(function (VehicleBooking $record, array $data): void {
                if (! $record->setujui($data['approval_note'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' disetujui')
                    ->body('Sudah masuk antrean tim GA.')->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Menugaskan kendaraan. Di sinilah bentrok diperiksa, dan pemeriksaannya dilakukan
     * lagi di dalam action, bukan hanya di validasi formulir, karena dua orang bisa
     * membuka layar ini bersamaan dan yang menekan tombol belakangan tidak boleh menang
     * hanya karena layarnya dimuat lebih dulu.
     */
    public static function tugaskanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tugaskan')
            ->label('Assign Vehicle')
            ->icon('heroicon-o-truck')
            ->color('primary')
            ->visible(fn (VehicleBooking $record): bool => in_array($record->status, ['disetujui', 'ditugaskan'], true)
                && static::allows('assign'))
            ->modalHeading(fn (VehicleBooking $record): string => 'Assign Vehicle for '.$record->code)
            ->modalDescription(fn (VehicleBooking $record): string => 'Dibutuhkan '.$record->jadwalLabel()
                .', '.strtolower($record->penumpangLabel()).'.')
            ->modalSubmitActionLabel('Assign')
            ->fillForm(fn (VehicleBooking $record): array => [
                'vehicle_id' => $record->vehicle_id,
                'driver_employee_id' => $record->driver_employee_id,
            ])
            ->schema([
                Select::make('vehicle_id')
                    ->label('Kendaraan')
                    ->options(fn (): array => Vehicle::query()
                        ->aktif()
                        ->with('asset')
                        ->orderBy('plate_number')
                        ->get()
                        ->mapWithKeys(fn (Vehicle $v) => [$v->id => $v->pickerLabel()])
                        ->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->helperText(function ($get, VehicleBooking $record): string {
                        if (blank($get('vehicle_id'))) {
                            return 'Pilih kendaraannya, lalu bentroknya diperiksa di sini sebelum disimpan.';
                        }

                        $bentrok = $record->bentrok((int) $get('vehicle_id'));

                        return $bentrok === null
                            ? 'Kendaraan ini kosong pada jam tersebut.'
                            : 'Bentrok dengan '.$bentrok->code.', '.$bentrok->jadwalLabel().', ke '.$bentrok->destination.'.';
                    }),
                Select::make('driver_employee_id')
                    ->label('Sopir')
                    ->options(fn (): array => Employee::query()
                        ->where('is_active', true)
                        ->orderBy('full_name')
                        ->pluck('full_name', 'id')
                        ->all())
                    ->searchable()
                    ->placeholder('Pemohon menyetir sendiri')
                    ->helperText('Kosongkan kalau pemohon menyetir sendiri.'),
            ])
            ->action(function (VehicleBooking $record, array $data, Action $action): void {
                $bentrok = $record->bentrok((int) $data['vehicle_id']);

                if ($bentrok !== null) {
                    Notification::make()
                        ->danger()
                        ->title('Kendaraan itu sudah dipesan')
                        ->body($bentrok->code.' memakainya '.$bentrok->jadwalLabel().' ke '.$bentrok->destination.'. Pilih kendaraan lain atau ubah jadwalnya.')
                        ->persistent()
                        ->send();

                    // Kotaknya dibiarkan terbuka. Yang sedang menugaskan perlu memilih
                    // kendaraan lain sekarang juga, dan menutup kotaknya berarti memaksa
                    // ia mengulang seluruh langkah hanya untuk mengganti satu pilihan.
                    $action->halt();
                }

                if (! $record->tugaskan((int) $data['vehicle_id'], $data['driver_employee_id'] ?? null)) {
                    static::peringatanStatusBerubah();

                    $action->halt();
                }

                // Relasi dimuat ulang sebelum dipakai di pesan, karena yang baru saja
                // disimpan adalah kolom vehicle_id, bukan objek kendaraannya.
                $record->load('vehicle');

                Notification::make()
                    ->success()
                    ->title('Kendaraan ditugaskan')
                    ->body(($record->vehicle?->plate_number ?? 'Kendaraan').' disiapkan untuk '.$record->code.'. Perjalanannya dicatat dari halaman kendaraan itu.')
                    ->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function tolakAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tolak')
            ->label('Reject')
            ->icon('heroicon-o-hand-raised')
            ->color('danger')
            ->visible(fn (VehicleBooking $record): bool => in_array($record->status, ['diajukan', 'disetujui'], true)
                && (static::bolehMenyetujui($record) || static::allows('assign')))
            ->modalHeading(fn (VehicleBooking $record): string => 'Reject '.$record->code)
            ->modalDescription('Pemesanan yang ditolak tetap tersimpan beserta alasannya, dan pemohon bisa membacanya.')
            ->modalSubmitActionLabel('Reject Booking')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan ditolak')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: seluruh kendaraan sudah dipesan pada jam itu, silakan geser ke sore.'),
            ])
            ->action(function (VehicleBooking $record, array $data): void {
                if (! $record->tolak($data['rejection_reason'])) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' ditolak')->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Cancel')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (VehicleBooking $record): bool => in_array($record->status, ['diajukan', 'disetujui', 'ditugaskan'], true)
                && static::bolehMembatalkan($record))
            ->requiresConfirmation()
            ->modalHeading(fn (VehicleBooking $record): string => 'Cancel '.$record->code)
            ->modalDescription('Kendaraannya kembali kosong pada jam itu dan bisa dipakai pemesanan lain. Pemesanan ini tetap tersimpan sebagai catatan.')
            ->modalSubmitActionLabel('Cancel Booking')
            ->action(function (VehicleBooking $record): void {
                if (! $record->batalkan()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' dibatalkan')->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    protected static function peringatanStatusBerubah(): void
    {
        Notification::make()
            ->warning()
            ->title('Statusnya sudah berubah')
            ->body('Pemesanan ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->send();
    }

    // ------------------------------------------------------------------ perizinan

    protected static function bolehMenyetujui(VehicleBooking $record): bool
    {
        if (static::allows('approve')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null && (int) $record->approver_employee_id === (int) $karyawan->getKey();
    }

    protected static function bolehMembatalkan(VehicleBooking $record): bool
    {
        if (static::allows('assign')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null && (int) $record->requester_employee_id === (int) $karyawan->getKey();
    }

    /**
     * Isi pemesanan hanya bisa diubah selama kendaraannya belum ditugaskan. Setelah itu
     * mengubah jamnya berarti mengubah pemeriksaan bentrok yang sudah terlanjur lulus.
     */
    public static function canEdit(Model $record): bool
    {
        return static::allows('update') && in_array($record->status, ['diajukan', 'disetujui'], true);
    }

    public static function canDelete(Model $record): bool
    {
        return static::allows('delete') && $record->status === 'diajukan';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleBookings::route('/'),
            'create' => CreateVehicleBooking::route('/create'),
            'view' => ViewVehicleBooking::route('/{record}'),
        ];
    }
}
