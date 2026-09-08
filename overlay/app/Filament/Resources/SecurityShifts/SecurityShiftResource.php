<?php

namespace App\Filament\Resources\SecurityShifts;

use App\Filament\Resources\SecurityShifts\Pages\ListSecurityShifts;
use App\Models\Location;
use App\Models\SecurityShift;
use App\Models\ServiceStaff;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
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
use Illuminate\Support\Carbon;
use UnitEnum;

/**
 * Jadwal jaga keamanan beserta kehadirannya.
 *
 * Menyusun jadwal dan mencatat kehadiran adalah dua pekerjaan yang berbeda waktunya, jadi
 * keduanya dipisah menjadi dua tindakan. Yang menyusun jadwal mengisi tanggal, shift, dan
 * petugas. Yang mencatat kehadiran, biasanya keesokan paginya, hanya menyentuh satu tombol
 * dan tidak bisa menggeser jadwal yang sudah terbit.
 */
class SecurityShiftResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = SecurityShift::class;

    protected static string $moduleCode = 'security_shifts';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|UnitEnum|null $navigationGroup = 'Facility Services';

    protected static ?string $navigationLabel = 'Security Shifts';

    protected static ?string $modelLabel = 'security shift';

    protected static ?string $pluralModelLabel = 'security shifts';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Yang diisi di sini hanya rencananya. Kehadiran dicatat belakangan lewat tombol tersendiri, supaya jadwal yang sudah terbit tidak ikut berubah saat orang mencatat siapa yang benar benar jaga.')
                ->columns(2)
                ->schema([
                    DatePicker::make('shift_date')
                        ->label('Tanggal jaga')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required(),

                    Select::make('shift')
                        ->label('Shift')
                        ->options(SecurityShift::SHIFTS)
                        ->default('pagi')
                        ->required()
                        ->helperText('Jam mulainya mengikuti kesepakatan perusahaan, dan jam sebenarnya dicatat saat kehadiran diisi.'),

                    Select::make('service_staff_id')
                        ->label('Petugas yang dijadwalkan')
                        ->options(fn (): array => static::pilihanPetugas())
                        ->searchable()
                        ->preload()
                        ->required()
                        /*
                         * Penjaga jadwal ganda ada dua lapis, dan keduanya memang perlu.
                         *
                         * Lapis dalam adalah indeks unik di basis data, karena jadwal jaga
                         * sering disusun dua orang sekaligus menjelang pergantian bulan dan
                         * hanya basis data yang sanggup menengahi dua penyimpanan yang tiba
                         * pada detik yang sama.
                         *
                         * Lapis ini adalah kalimatnya. Tanpa lapis ini, indeks tadi memang
                         * menolak barisnya, tetapi penolakannya berupa galat basis data yang
                         * ditelan diam diam: kotak dialognya tetap terbuka, tidak ada pesan
                         * apa pun, dan orang yang menekan Simpan tidak tahu apakah jadwalnya
                         * tersimpan atau tidak. Ketahuan saat pemeriksaan kiriman Q.
                         */
                        ->rule(fn (?SecurityShift $record, $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record, $get): void {
                            $tanggal = $get('shift_date');
                            $shift = $get('shift');

                            if (blank($value) || blank($tanggal) || blank($shift)) {
                                return;
                            }

                            $bentrok = SecurityShift::query()
                                ->whereDate('shift_date', Carbon::parse($tanggal)->toDateString())
                                ->where('shift', $shift)
                                ->where('service_staff_id', $value)
                                ->when($record, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
                                ->first();

                            if ($bentrok !== null) {
                                $fail('Petugas ini sudah dijadwalkan pada shift '
                                    .mb_strtolower($bentrok->shiftLabel()).' tanggal '
                                    .$bentrok->shift_date->format('d M Y')
                                    .'. Satu orang tidak bisa dijadwalkan dua kali pada shift yang sama.');
                            }
                        })
                        ->helperText('Hanya petugas keamanan yang masih bertugas yang muncul di sini.'),

                    Select::make('location_id')
                        ->label('Pos jaga')
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
                        ->placeholder('Tidak dibedakan')
                        ->helperText('Boleh dikosongkan kalau kantor hanya punya satu pos.'),

                    Textarea::make('notes')
                        ->label('Catatan jadwal')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Misalnya jadwal pengganti karena cuti')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /** Petugas keamanan yang masih bertugas, siap dipakai sebagai pilihan. */
    protected static function pilihanPetugas(): array
    {
        return ServiceStaff::query()
            ->keamanan()
            ->where('is_active', true)
            ->with('employee')
            ->get()
            ->mapWithKeys(fn (ServiceStaff $petugas) => [
                $petugas->id => $petugas->namaLengkap().' ('.$petugas->asalLabel().')',
            ])
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shift_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('shift')
                    ->label('Shift')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => SecurityShift::SHIFTS[$state] ?? '-')
                    ->sortable(),
                TextColumn::make('petugas')
                    ->label('Dijadwalkan')
                    ->state(fn (SecurityShift $record): string => $record->petugasDijadwalkan())
                    ->description(fn (SecurityShift $record): ?string => $record->location?->name),
                TextColumn::make('attendance')
                    ->label('Kehadiran')
                    ->badge()
                    ->state(fn (SecurityShift $record): string => $record->attendanceLabel())
                    ->color(fn (SecurityShift $record): string => $record->attendanceColor())
                    ->description(fn (SecurityShift $record): ?string => $record->attendance === 'digantikan'
                        ? 'Digantikan '.($record->replacement?->namaLengkap() ?? 'orang yang belum dicatat')
                        : null),
                TextColumn::make('jam')
                    ->label('Jam jaga')
                    ->state(fn (SecurityShift $record): string => $record->jamLabel()),
                TextColumn::make('incidents_count')
                    ->label('Insiden')
                    ->counts('incidents')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Disusun oleh')
                    ->placeholder('Tidak tercatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('shift_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['staff.employee', 'replacement.employee', 'location']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('shift')
                    ->label('Shift')
                    ->options(SecurityShift::SHIFTS),
                SelectFilter::make('attendance')
                    ->label('Kehadiran')
                    ->options(SecurityShift::ATTENDANCES),
                Filter::make('tertinggal')
                    ->label('Belum dicatat padahal sudah lewat')
                    ->query(fn (Builder $query): Builder => $query->tertinggal()),
            ])
            ->recordActions([
                static::catatKehadiranAction(),
                EditAction::make()
                    ->iconButton()
                    ->modalHeading(fn (SecurityShift $record): string => 'Edit Shift for '.$record->petugasDijadwalkan()),
                DeleteAction::make()
                    ->iconButton()
                    ->modalDescription('Jadwal yang kehadirannya sudah dicatat sebaiknya tidak dihapus, karena rekap kehadiran bulan itu ikut berubah.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada jadwal yang cocok'
                : 'Belum ada jadwal jaga')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada jadwal yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh jadwal lagi.'
                : 'Susun jadwal jaga per tanggal dan shift. Petugasnya diambil dari data induk Service Staff, jadi daftarkan satpamnya di sana lebih dulu.');
    }

    /**
     * Mencatat kehadiran, terpisah dari mengubah jadwal.
     *
     * Nama pengganti hanya diminta kalau kehadirannya memang digantikan, dan pilihannya
     * menutup nama orang yang dijadwalkan itu sendiri: orang tidak bisa menggantikan dirinya
     * sendiri, dan membiarkannya terpilih menghasilkan baris yang berbunyi seperti teka teki.
     */
    public static function catatKehadiranAction(): Action
    {
        return Action::make('catat_kehadiran')
            ->label('Record Attendance')
            ->icon('heroicon-o-clipboard-document-check')
            ->iconButton()
            ->color('primary')
            ->visible(fn (): bool => static::allows('update'))
            ->modalHeading(fn (SecurityShift $record): string => 'Attendance for '.$record->petugasDijadwalkan())
            ->modalDescription(fn (SecurityShift $record): string => 'Jaga '.mb_strtolower($record->shiftLabel())
                .' pada '.$record->shift_date->format('d M Y').'. Catat apa yang benar benar terjadi.')
            ->modalSubmitActionLabel('Save Attendance')
            ->fillForm(fn (SecurityShift $record): array => [
                'attendance' => $record->attendance === 'belum' ? 'hadir' : $record->attendance,
                'replacement_staff_id' => $record->replacement_staff_id,
                'checked_in_at' => $record->checked_in_at,
                'checked_out_at' => $record->checked_out_at,
                'notes' => $record->notes,
            ])
            ->schema([
                Select::make('attendance')
                    ->label('Kehadiran')
                    ->options(collect(SecurityShift::ATTENDANCES)->except('belum')->all())
                    ->required()
                    ->live(),
                Select::make('replacement_staff_id')
                    ->label('Digantikan oleh')
                    ->options(fn (SecurityShift $record): array => collect(static::pilihanPetugas())
                        ->except($record->service_staff_id)
                        ->all())
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get): bool => $get('attendance') === 'digantikan')
                    ->required(fn ($get): bool => $get('attendance') === 'digantikan')
                    ->helperText('Orang yang dijadwalkan tidak muncul di daftar ini, karena ia tidak bisa menggantikan dirinya sendiri.'),
                DateTimePicker::make('checked_in_at')
                    ->label('Jam masuk')
                    ->native(false)
                    ->seconds(false)
                    ->displayFormat('d M Y, H:i')
                    ->visible(fn ($get): bool => $get('attendance') !== 'tidak_hadir')
                    ->helperText('Boleh dikosongkan kalau jam pastinya tidak tercatat.'),
                DateTimePicker::make('checked_out_at')
                    ->label('Jam pulang')
                    ->native(false)
                    ->seconds(false)
                    ->displayFormat('d M Y, H:i')
                    ->visible(fn ($get): bool => $get('attendance') !== 'tidak_hadir')
                    ->after('checked_in_at')
                    ->helperText('Dikosongkan kalau shiftnya masih berjalan.'),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Misalnya alasan tidak hadir, atau sebab keterlambatan'),
            ])
            ->action(function (SecurityShift $record, array $data): void {
                $record->forceFill([
                    'attendance' => $data['attendance'],
                    'replacement_staff_id' => $data['replacement_staff_id'] ?? null,
                    'checked_in_at' => $data['checked_in_at'] ?? null,
                    'checked_out_at' => $data['checked_out_at'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ])->save();

                /*
                 * Relasi penggantinya dimuat ulang sebelum kalimatnya disusun.
                 *
                 * Menyimpan kolom replacement_staff_id tidak membuat Eloquent membaca ulang
                 * relasi replacement, jadi kalimat yang disusun tepat setelah penyimpanan
                 * masih memakai relasi lama yang kosong dan berbunyi "digantikan orang yang
                 * belum dicatat namanya", persis di sebelah baris tabel yang sudah menyebut
                 * nama penggantinya. Pemberitahuan yang membantah barisnya sendiri membuat
                 * orang mengira penyimpanannya gagal. Ketahuan saat pemeriksaan kiriman Q.
                 */
                $record->load(['staff.employee', 'replacement.employee']);

                Notification::make()
                    ->success()
                    ->title('Kehadiran tercatat')
                    ->body($record->kehadiranKalimat())
                    ->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecurityShifts::route('/'),
        ];
    }
}
