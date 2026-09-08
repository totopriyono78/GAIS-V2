<?php

namespace App\Filament\Resources\BusinessTrips;

use App\Filament\Resources\BusinessTrips\Pages\CreateBusinessTrip;
use App\Filament\Resources\BusinessTrips\Pages\ListBusinessTrips;
use App\Filament\Resources\BusinessTrips\Pages\ViewBusinessTrip;
use App\Models\BusinessTrip;
use App\Models\Employee;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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
 * Perjalanan dinas dengan uang muka dan pertanggungjawabannya.
 *
 * Lima langkah, dan tiap langkah punya izinnya sendiri. Yang mengajukan bukan yang menyetujui,
 * yang menyetujui bukan yang membayarkan uang muka, dan yang membayarkan bukan yang memeriksa
 * pertanggungjawabannya. Pemisahan itu mengikuti pola yang sudah berjalan pada tagihan rekanan
 * sejak kiriman K dan penggantian biaya sejak kiriman L.
 */
class BusinessTripResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = BusinessTrip::class;

    protected static string $moduleCode = 'business_trips';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static string|UnitEnum|null $navigationGroup = 'Vehicles';

    protected static ?string $navigationLabel = 'Business Trips';

    protected static ?string $modelLabel = 'business trip';

    protected static ?string $pluralModelLabel = 'business trips';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Penyempitan daftar. Tanpa izin read_all, seseorang hanya melihat perjalanannya sendiri
     * dan perjalanan departemen yang ia kepalai, karena keduanya memang urusannya.
     *
     * Sama seperti permintaan perbaikan sejak kiriman G, ini dipakai menggantikan kolom
     * data_scope pada role, yang tersimpan tetapi tidak pernah dibaca query mana pun. Izin
     * yang terbaca di matriks lebih jujur daripada kolom yang tidak berpengaruh apa apa.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::allows('read_all')) {
            return $query;
        }

        $karyawan = Auth::user()?->employee;

        if ($karyawan === null) {
            // Akun tanpa kartu karyawan tidak punya perjalanan sendiri. Daftar kosong lebih
            // benar daripada diam diam menampilkan perjalanan seluruh kantor.
            return $query->whereRaw('1 = 0');
        }

        $departemenSaya = $karyawan->headedDepartments()->pluck('id')->all();

        return $query->where(function (Builder $sub) use ($karyawan, $departemenSaya): void {
            $sub->where('employee_id', $karyawan->getKey());

            if ($departemenSaya !== []) {
                $sub->orWhereIn('department_id', $departemenSaya);
            }
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Uang muka diisi belakangan oleh tim GA setelah pengajuan ini disetujui, jadi di sini cukup perkiraan biayanya.')
                ->columns(2)
                ->schema([
                    Select::make('employee_id')
                        ->label('Penanggung jawab')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->with('department')
                            ->orderBy('full_name')
                            ->get()
                            ->mapWithKeys(fn (Employee $karyawan) => [
                                $karyawan->id => $karyawan->full_name.' ('.($karyawan->department?->name ?? 'tanpa departemen').')',
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(fn (?BusinessTrip $record): bool => $record !== null)
                        ->dehydrated()
                        ->helperText(fn (?BusinessTrip $record): string => $record !== null
                            ? 'Tidak bisa diganti setelah pengajuan terbit, karena departemen yang dibebani sudah dibekukan mengikuti orangnya.'
                            : 'Dia ikut berangkat, menerima uang mukanya, dan mempertanggungjawabkan biaya seluruh rombongan. Departemen yang dibebani mengikuti kartu karyawannya, dan dibekukan saat pengajuan ini disimpan.'),

                    Select::make('transport_mode')
                        ->label('Cara berangkat')
                        ->options(BusinessTrip::TRANSPORT_MODES)
                        ->default('darat_umum')
                        ->required(),

                    TextInput::make('destination')
                        ->label('Tujuan')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('Kota atau tempat yang dituju'),

                    TextInput::make('estimated_cost')
                        ->label('Perkiraan biaya')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('Boleh dikosongkan')
                        ->helperText('Untuk seluruh rombongan, bukan per orang. Dipakai atasan saat memutuskan, dan menjadi patokan besaran uang muka.'),

                    DatePicker::make('start_date')
                        ->label('Berangkat')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required(),

                    DatePicker::make('end_date')
                        ->label('Kembali')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required()
                        ->afterOrEqual('start_date')
                        ->helperText('Boleh sama dengan tanggal berangkat untuk perjalanan sehari.'),

                    /*
                     * Peserta lain. Penanggung jawab sengaja tidak perlu dipilih lagi di sini,
                     * karena namanya sudah tercatat di kolom di atas. Saat mengubah pengajuan
                     * yang sudah terbit, namanya juga dikeluarkan dari pilihan supaya tidak
                     * ada yang tergoda mencatatnya dua kali.
                     */
                    Select::make('participants')
                        ->label('Peserta lain yang ikut')
                        ->relationship(
                            name: 'participants',
                            titleAttribute: 'full_name',
                            modifyQueryUsing: fn (Builder $query, ?BusinessTrip $record): Builder => $query
                                ->where('is_active', true)
                                ->when($record?->employee_id, fn (Builder $sub, int $pic): Builder => $sub->whereKeyNot($pic))
                                ->orderBy('full_name'),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Employee $record): string => $record->full_name
                            .' ('.($record->department?->name ?? 'tanpa departemen').')')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull()
                        ->helperText('Kosongkan kalau berangkat sendiri. Penanggung jawab tidak perlu dipilih di sini, namanya sudah tercatat di atas. Peserta dari departemen lain boleh ikut, tetapi seluruh biayanya tetap dibebankan ke departemen penanggung jawab.'),

                    Textarea::make('purpose')
                        ->label('Keperluan')
                        ->rows(3)
                        ->required()
                        ->maxLength(1000)
                        ->placeholder('Apa yang dikerjakan di sana, dan atas permintaan siapa')
                        ->columnSpanFull(),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Boleh dikosongkan')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Trip')
                ->columns(2)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (BusinessTrip $record): string => $record->statusLabel())
                        ->color(fn (BusinessTrip $record): string => $record->statusColor()),
                    TextEntry::make('employee.full_name')->label('Penanggung jawab'),
                    TextEntry::make('department.name')->label('Departemen yang dibebani'),
                    TextEntry::make('rombongan')
                        ->label('Yang berangkat')
                        ->state(fn (BusinessTrip $record): string => $record->rombonganKalimat())
                        ->helperText(fn (BusinessTrip $record): ?string => $record->adaPesertaLuarDepartemen()
                            ? 'Ada yang berangkat dari departemen lain, tetapi seluruh biayanya tetap dibebankan ke '
                                .($record->department?->name ?? 'departemen penanggung jawab').'.'
                            : null)
                        ->columnSpanFull(),
                    TextEntry::make('destination')->label('Tujuan'),
                    TextEntry::make('periode')
                        ->label('Waktu')
                        ->state(fn (BusinessTrip $record): string => $record->periodeLabel()),
                    TextEntry::make('transport')
                        ->label('Cara berangkat')
                        ->state(fn (BusinessTrip $record): string => $record->transportLabel()),
                    TextEntry::make('perkiraan')
                        ->label('Perkiraan biaya')
                        ->state(fn (BusinessTrip $record): string => $record->estimated_cost === null
                            ? 'Tidak diperkirakan'
                            : Rupiah::penuh((float) $record->estimated_cost)),
                    TextEntry::make('purpose')->label('Keperluan')->columnSpanFull(),
                ]),

            Section::make('Money')
                ->columns(2)
                ->schema([
                    TextEntry::make('uang_muka')
                        ->label('Uang muka')
                        ->state(fn (BusinessTrip $record): string => $record->uangMukaLabel())
                        ->helperText(fn (BusinessTrip $record): string => $record->uangMukaSudahDibayar()
                            ? 'Dibayarkan '.$record->advance_paid_at->format('d M Y, H:i')
                                .' oleh '.($record->advancePaidByUser?->name ?? 'tim GA')
                            : 'Belum dibayarkan'),
                    TextEntry::make('realisasi')
                        ->label('Biaya sebenarnya')
                        ->state(fn (BusinessTrip $record): string => $record->realisasiLabel())
                        ->helperText('Dijumlahkan dari rincian di bawah, tidak pernah diketik.'),
                    TextEntry::make('selisih')
                        ->label('Penyelesaian')
                        ->badge()
                        ->state(fn (BusinessTrip $record): string => $record->selisihKalimat())
                        ->color(fn (BusinessTrip $record): ?string => $record->selisihColor())
                        ->columnSpanFull(),
                    TextEntry::make('settlement_note')
                        ->label('Catatan penutup')
                        ->placeholder('Belum ditutup')
                        ->columnSpanFull(),
                ]),

            Section::make('Trail')
                ->columns(2)
                ->schema([
                    TextEntry::make('persetujuan')
                        ->label('Persetujuan')
                        ->state(fn (BusinessTrip $record): string => match (true) {
                            filled($record->approval_skipped_reason) => 'Lewat persetujuan. '.$record->approval_skipped_reason,
                            $record->approved_at !== null => 'Disetujui '.($record->approver?->full_name ?? 'atasan')
                                .' pada '.$record->approved_at->format('d M Y, H:i'),
                            $record->status === 'ditolak' => 'Ditolak. '.($record->rejection_reason ?? ''),
                            default => 'Menunggu '.($record->approver?->full_name ?? 'atasan'),
                        })
                        ->columnSpanFull(),
                    TextEntry::make('submitted_at')->label('Diajukan')->dateTime('d M Y, H:i'),
                    TextEntry::make('reported_at')
                        ->label('Dipertanggungjawabkan')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('Belum'),
                    TextEntry::make('settled_at')
                        ->label('Ditutup')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('Belum'),
                    TextEntry::make('settledByUser.name')
                        ->label('Ditutup oleh')
                        ->placeholder('Belum'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Nomor')->fontFamily('mono')->searchable()->sortable(),
                TextColumn::make('employee.full_name')
                    ->label('Penanggung jawab')
                    // Jumlah rombongan ikut di bawah namanya, bukan menjadi kolom sendiri.
                    // Yang membaca daftar ini mencari nama, dan jumlah orang hanya berarti
                    // kalau terbaca menempel pada nama yang bertanggung jawab atas mereka.
                    ->description(function (BusinessTrip $record): ?string {
                        $bagian = array_filter([
                            $record->department?->name,
                            $record->jumlahBerangkat() > 1
                                ? 'berangkat '.$record->jumlahBerangkat().' orang'
                                : null,
                        ]);

                        return $bagian === [] ? null : implode(', ', $bagian);
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destination')
                    ->label('Tujuan')
                    ->description(fn (BusinessTrip $record): string => $record->periodeLabel())
                    ->searchable()
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (BusinessTrip $record): string => $record->statusLabel())
                    ->color(fn (BusinessTrip $record): string => $record->statusColor())
                    ->sortable(),
                TextColumn::make('uang_muka')
                    ->label('Uang muka')
                    ->state(fn (BusinessTrip $record): string => $record->uangMukaLabel())
                    ->description(fn (BusinessTrip $record): ?string => $record->advance_amount !== null && ! $record->uangMukaSudahDibayar()
                        ? 'Belum dibayarkan'
                        : null)
                    ->alignEnd(),
                TextColumn::make('penyelesaian')
                    ->label('Penyelesaian')
                    ->state(fn (BusinessTrip $record): string => $record->selisihRingkas())
                    ->color(fn (BusinessTrip $record): ?string => $record->selisihColor())
                    ->alignEnd(),
                TextColumn::make('realisasi')
                    ->label('Biaya sebenarnya')
                    ->state(fn (BusinessTrip $record): string => $record->realisasiLabel())
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rombongan')
                    ->label('Yang berangkat')
                    ->state(fn (BusinessTrip $record): string => $record->rombonganKalimat())
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('transport')
                    ->label('Cara berangkat')
                    ->state(fn (BusinessTrip $record): string => $record->transportLabel())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->label('Berangkat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('start_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['employee.department', 'department', 'approver', 'participants']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('status')->label('Status')->options(BusinessTrip::STATUSES),
                SelectFilter::make('department_id')
                    ->label('Departemen yang dibebani')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('menunggu_uang_muka')
                    ->label('Uang mukanya belum dibayarkan')
                    ->query(fn (Builder $query): Builder => $query->menungguUangMuka()),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                static::setujuiAction(),
                static::bayarUangMukaAction(),
                static::tutupAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (BusinessTrip $record): bool => $record->masihBerjalan()),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (BusinessTrip $record): bool => ! $record->isSelesai()),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada perjalanan yang cocok'
                : 'Belum ada perjalanan dinas')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada perjalanan yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh perjalanan lagi.'
                : 'Ajukan perjalanan dinas di sini. Satu pengajuan bisa memberangkatkan beberapa orang sekaligus dengan satu penanggung jawab. Setelah disetujui, uang mukanya dibayarkan tim GA kepada penanggung jawab itu, dan sepulangnya rincian biaya dicatat untuk dibandingkan dengan uang muka tersebut.');
    }

    // ---------------------------------------------------------------- tindakan

    protected static function segarkan(mixed $livewire, BusinessTrip $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    public static function setujuiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('setujui')
            ->label('Approve Trip')
            ->icon('heroicon-o-check-circle')
            ->color('primary')
            ->visible(fn (BusinessTrip $record): bool => $record->isDiajukan() && static::allows('approve'))
            ->modalHeading(fn (BusinessTrip $record): string => 'Approve '.$record->code)
            ->modalDescription(fn (BusinessTrip $record): string => 'Tujuan '.$record->destination
                .', '.$record->periodeLabel().'. '.$record->rombonganKalimat()
                .' Perkiraan biayanya '.($record->estimated_cost === null ? 'tidak disebutkan' : Rupiah::penuh((float) $record->estimated_cost))
                .' untuk seluruh rombongan. Menyetujui belum mengeluarkan uang: uang mukanya dibayarkan tim GA di langkah berikutnya.')
            ->modalSubmitActionLabel('Approve Trip')
            ->schema([
                Textarea::make('approval_note')
                    ->label('Catatan persetujuan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan'),
            ])
            ->action(function (BusinessTrip $record, array $data, Action $action, $livewire): void {
                if (! $record->isDiajukan()) {
                    static::peringatanStatusBerubah();
                    $action->halt();
                }

                $record->forceFill([
                    'status' => 'disetujui',
                    'approved_at' => now(),
                    'approval_note' => $data['approval_note'] ?? null,
                ])->save();

                Notification::make()->success()->title($record->code.' disetujui')
                    ->body('Uang mukanya sekarang bisa dibayarkan tim GA.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function tolakAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tolak')
            ->label('Reject Trip')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (BusinessTrip $record): bool => $record->isDiajukan() && static::allows('approve'))
            ->modalHeading(fn (BusinessTrip $record): string => 'Reject '.$record->code)
            ->modalDescription('Pengajuannya tetap tersimpan beserta alasan penolakannya, supaya pemohon tahu apa yang perlu diperbaiki kalau ia mengajukan lagi.')
            ->modalSubmitActionLabel('Reject Trip')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan penolakan')
                    ->rows(3)
                    ->required()
                    ->maxLength(500),
            ])
            ->action(function (BusinessTrip $record, array $data, Action $action, $livewire): void {
                if (! $record->isDiajukan()) {
                    static::peringatanStatusBerubah();
                    $action->halt();
                }

                $record->forceFill([
                    'status' => 'ditolak',
                    'rejection_reason' => $data['rejection_reason'],
                ])->save();

                Notification::make()->success()->title($record->code.' ditolak')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Membayarkan uang muka.
     *
     * Besarannya diisi di sini, bukan saat mengajukan, dan itu disengaja. Yang mengajukan
     * menyebut perkiraan biaya; yang memutuskan berapa uang yang benar benar keluar dari kas
     * adalah tim GA, setelah pengajuannya disetujui. Menyatukan keduanya berarti pemohon
     * menentukan sendiri berapa yang ia terima di muka.
     */
    public static function bayarUangMukaAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('bayar_uang_muka')
            ->label('Pay Advance')
            ->icon('heroicon-o-banknotes')
            ->color('primary')
            ->visible(fn (BusinessTrip $record): bool => $record->isDisetujui()
                && ! $record->uangMukaSudahDibayar()
                && static::allows('pay'))
            ->modalHeading(fn (BusinessTrip $record): string => 'Pay Advance for '.$record->code)
            ->modalDescription(fn (BusinessTrip $record): string => 'Uang mukanya diserahkan kepada '
                .($record->employee?->full_name ?? 'karyawan').' sebagai penanggung jawab. '
                .$record->rombonganRingkas().'. Perkiraan biayanya '
                .($record->estimated_cost === null ? 'tidak disebutkan' : Rupiah::penuh((float) $record->estimated_cost))
                .' untuk seluruh rombongan. Angka ini yang nanti dibandingkan dengan rincian pertanggungjawabannya.')
            ->modalSubmitActionLabel('Record Payment')
            ->fillForm(fn (BusinessTrip $record): array => [
                'advance_amount' => $record->estimated_cost,
                'advance_paid_at' => now(),
            ])
            ->schema([
                TextInput::make('advance_amount')
                    ->label('Uang muka dibayarkan')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->required()
                    ->helperText('Sudah terisi mengikuti perkiraan biaya. Ganti kalau yang benar benar diserahkan berbeda.'),
                DateTimePicker::make('advance_paid_at')
                    ->label('Waktu dibayarkan')
                    ->native(false)
                    ->seconds(false)
                    ->displayFormat('d M Y, H:i')
                    ->required()
                    ->maxDate(now()),
                TextInput::make('advance_note')
                    ->label('Keterangan')
                    ->maxLength(255)
                    ->placeholder('Misalnya transfer bank, atau tunai'),
            ])
            ->action(function (BusinessTrip $record, array $data, Action $action, $livewire): void {
                if (! $record->isDisetujui() || $record->uangMukaSudahDibayar()) {
                    static::peringatanStatusBerubah();
                    $action->halt();
                }

                $record->forceFill([
                    'advance_amount' => $data['advance_amount'],
                    'advance_paid_at' => $data['advance_paid_at'],
                    'advance_paid_by_user_id' => Auth::id(),
                    'advance_note' => $data['advance_note'] ?? null,
                ])->save();

                Notification::make()->success()
                    ->title('Uang muka '.Rupiah::penuh((float) $record->advance_amount).' tercatat')
                    ->body('Sepulangnya, rincian biaya dicatat di halaman ini dan dibandingkan dengan angka tersebut.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function pertanggungjawabkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('pertanggungjawabkan')
            ->label('Submit Settlement')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn (BusinessTrip $record): bool => $record->isDisetujui() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (BusinessTrip $record): string => 'Submit Settlement for '.$record->code)
            ->modalDescription(function (BusinessTrip $record): string {
                $alasan = $record->alasanBelumBisaDipertanggungjawabkan();

                return $alasan ?? 'Rinciannya berjumlah '.Rupiah::penuh($record->totalRealisasi()).'. '
                    .$record->selisihKalimat().' Setelah diajukan, rinciannya masih bisa diperbaiki sampai tim GA menutupnya.';
            })
            ->modalSubmitActionLabel('Submit Settlement')
            ->action(function (BusinessTrip $record, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDipertanggungjawabkan();

                if ($alasan !== null) {
                    Notification::make()->warning()->title('Belum bisa dipertanggungjawabkan')
                        ->body($alasan)->persistent()->send();

                    $action->halt();
                }

                $record->forceFill([
                    'status' => 'dipertanggungjawabkan',
                    'reported_at' => now(),
                ])->save();

                Notification::make()->success()->title($record->code.' menunggu diperiksa')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Menutup perjalanan.
     *
     * Inilah satu satunya langkah yang membuat biayanya masuk ke realisasi anggaran, karena
     * hanya rincian yang sudah diperiksa yang pantas memotong pagu departemen. Rincian yang
     * masih bisa berubah dan sudah terlanjur dihitung akan membuat angka anggaran bergerak
     * sendiri tanpa ada yang menyentuhnya.
     */
    public static function tutupAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tutup')
            ->label('Close Trip')
            ->icon('heroicon-o-lock-closed')
            ->color('primary')
            ->visible(fn (BusinessTrip $record): bool => $record->isDipertanggungjawabkan() && static::allows('verify'))
            ->modalHeading(fn (BusinessTrip $record): string => 'Close '.$record->code)
            ->modalDescription(fn (BusinessTrip $record): string => $record->selisihKalimat()
                .' Setelah ditutup, rinciannya tidak bisa diubah lagi, dan '
                .Rupiah::penuh($record->totalRealisasi()).' masuk ke realisasi anggaran '
                .($record->department?->name ?? 'departemen yang dibebani').'.')
            ->modalSubmitActionLabel('Close Trip')
            ->schema([
                Textarea::make('settlement_note')
                    ->label('Catatan penutup')
                    ->rows(3)
                    ->required()
                    ->maxLength(1000)
                    ->helperText('Sebutkan bagaimana selisihnya diselesaikan, misalnya sisa sudah dikembalikan tunai, atau kekurangan dibayar lewat penggantian biaya.'),
            ])
            ->action(function (BusinessTrip $record, array $data, Action $action, $livewire): void {
                if (! $record->isDipertanggungjawabkan()) {
                    static::peringatanStatusBerubah();
                    $action->halt();
                }

                $record->forceFill([
                    'status' => 'selesai',
                    'settled_at' => now(),
                    'settled_by_user_id' => Auth::id(),
                    'settlement_note' => $data['settlement_note'],
                ])->save();

                Notification::make()->success()->title($record->code.' ditutup')
                    ->body(Rupiah::penuh($record->totalRealisasi()).' masuk ke realisasi anggaran '
                        .($record->department?->name ?? 'departemen yang dibebani').'.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Cancel Trip')
            ->icon('heroicon-o-x-mark')
            ->color('gray')
            ->visible(fn (BusinessTrip $record): bool => $record->masihBerjalan()
                && ! $record->uangMukaSudahDibayar()
                && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (BusinessTrip $record): string => 'Cancel '.$record->code)
            ->modalDescription('Pengajuannya tetap tersimpan sebagai catatan. Tombol ini hilang setelah uang muka dibayarkan, karena uang yang sudah keluar perlu dipertanggungjawabkan, bukan dibatalkan.')
            ->modalSubmitActionLabel('Cancel Trip')
            ->action(function (BusinessTrip $record, $livewire): void {
                $record->forceFill(['status' => 'dibatalkan'])->save();

                Notification::make()->success()->title($record->code.' dibatalkan')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    protected static function peringatanStatusBerubah(): void
    {
        Notification::make()
            ->warning()
            ->title('Statusnya sudah berubah')
            ->body('Perjalanan ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->persistent()
            ->send();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusinessTrips::route('/'),
            'create' => CreateBusinessTrip::route('/create'),
            'view' => ViewBusinessTrip::route('/{record}'),
        ];
    }
}
