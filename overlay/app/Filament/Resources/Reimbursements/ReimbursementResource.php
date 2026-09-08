<?php

namespace App\Filament\Resources\Reimbursements;

use App\Filament\Resources\Reimbursements\Pages\CreateReimbursement;
use App\Filament\Resources\Reimbursements\Pages\ListReimbursements;
use App\Filament\Resources\Reimbursements\Pages\ViewReimbursement;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Reimbursement;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Penggantian biaya karyawan.
 *
 * Modul ini dipakai tiga kelompok orang yang membaca layar yang sama dengan pertanyaan
 * berbeda. Karyawan bertanya "sudah sampai mana punya saya". Kepala departemen bertanya
 * "mana yang menunggu tanda tangan saya". Tim GA bertanya "mana yang struknya belum saya
 * periksa". Karena itu kolom Status selalu membawa keterangan sedang ada di meja siapa,
 * dan lencana di menu hanya menghitung yang benar benar menunggu orang yang sedang masuk.
 *
 * Alurnya kembar dengan permintaan perbaikan dan pemesanan kendaraan, sengaja. Orang yang
 * menagih ongkos taksi adalah orang yang sama yang melaporkan AC bocor, dan alur yang
 * berbeda untuk hal yang sama sama "minta lalu disetujui" hanya membuang waktunya.
 */
class ReimbursementResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Reimbursement::class;

    protected static string $moduleCode = 'reimbursements';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|UnitEnum|null $navigationGroup = 'Anggaran';

    protected static ?string $navigationLabel = 'Penggantian biaya';

    protected static ?string $modelLabel = 'penggantian biaya';

    protected static ?string $pluralModelLabel = 'penggantian biaya';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Lencana menghitung yang menunggu tindakan orang yang sedang masuk: antrean tanda
     * tangan untuk kepala departemen, antrean pemeriksaan untuk tim GA, antrean transfer
     * untuk yang boleh menandai lunas. Yang tidak menunggu siapa pun tidak diberi angka,
     * karena angka yang tidak bisa ditindaklanjuti hanya jadi noda merah tetap di menu.
     */
    public static function getNavigationBadge(): ?string
    {
        $jumlah = static::antrianSaya()->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    protected static function antrianSaya(): Builder
    {
        $query = static::getEloquentQuery();

        $bisaPeriksa = static::allows('verify');
        $bisaBayar = static::allows('pay');
        $karyawan = Auth::user()?->employee;
        $departemenSaya = $karyawan?->headedDepartments()->pluck('id')->all() ?? [];

        if (! $bisaPeriksa && ! $bisaBayar && $departemenSaya === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $sub) use ($bisaPeriksa, $bisaBayar, $departemenSaya): void {
            if ($bisaPeriksa) {
                $sub->orWhere(fn (Builder $q) => $q->menungguGa());
            }

            if ($bisaBayar) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPembayaran());
            }

            if ($departemenSaya !== []) {
                $sub->orWhere(fn (Builder $q) => $q->menungguAtasan()->whereIn('department_id', $departemenSaya));
            }
        });
    }

    /**
     * Tanpa izin read_all, seseorang hanya melihat pengajuannya sendiri dan pengajuan
     * departemen yang ia kepalai. Struk belanja adalah hal yang wajar tidak ingin dibaca
     * seluruh kantor.
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
            $sub->where('employee_id', $karyawan->getKey());

            if ($departemenSaya !== []) {
                $sub->orWhereIn('department_id', $departemenSaya);
            }
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pengajuan')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Untuk apa')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->placeholder('Contoh: transportasi dan parkir kunjungan kantor pajak, September 2026')
                        ->helperText('Satu kalimat yang menjelaskan seluruh struk di dalamnya. Ini yang dibaca atasan lebih dulu.'),
                    Select::make('employee_id')
                        ->label('Yang mengajukan')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->default(fn (): ?int => Auth::user()?->employee?->getKey())
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $set('department_id', Employee::find($state)?->department_id);
                        })
                        ->helperText('Bawaannya diri sendiri. Ganti kalau Anda mencatatkan untuk orang lain.'),
                    Select::make('department_id')
                        ->label('Departemen yang dibebani')
                        ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->live()
                        ->default(fn (): ?int => Auth::user()?->employee?->department_id)
                        ->placeholder('Belum terbebankan ke departemen')
                        ->helperText('Terisi sendiri dari departemen pemohon. Kosongkan hanya untuk belanja kantor bersama, dan angkanya lalu muncul terpisah di layar anggaran.'),
                    Placeholder::make('jalur_persetujuan')
                        ->label('Setelah diajukan')
                        ->columnSpanFull()
                        ->content(function ($get): string {
                            if (blank($get('employee_id'))) {
                                return 'Pilih dulu siapa yang mengajukan.';
                            }

                            $contoh = new Reimbursement;
                            $contoh->employee_id = $get('employee_id');
                            $contoh->department_id = $get('department_id');

                            $alasan = $contoh->alasanLewatPersetujuan();

                            return $alasan === null
                                ? 'Menunggu persetujuan '.($contoh->calonPenyetuju()?->full_name ?? 'kepala departemen').', lalu tim GA memeriksa struknya.'
                                : 'Langsung ke tim GA untuk diperiksa. Alasannya: '.lcfirst($alasan).'.';
                        }),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: struk parkir tanggal 3 hilang, nominalnya dari catatan pribadi.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Pengajuan')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (Reimbursement $record): string => $record->statusLabel())
                        ->color(fn (Reimbursement $record): string => $record->statusColor())
                        ->helperText(fn (Reimbursement $record): string => $record->tahapLabel()),
                    TextEntry::make('nilai')
                        ->label('Nilai')
                        ->state(fn (Reimbursement $record): string => $record->totalLabel())
                        ->helperText(fn (Reimbursement $record): string => 'Dijumlahkan dari '
                            .$record->lines()->count().' struk di bawah.'),
                    TextEntry::make('title')->label('Untuk apa')->columnSpanFull(),
                    TextEntry::make('employee.full_name')->label('Pemohon')->placeholder('Karyawan sudah dihapus'),
                    TextEntry::make('department.name')
                        ->label('Departemen yang dibebani')
                        ->placeholder('Belum terbebankan ke departemen'),
                    TextEntry::make('rentang')
                        ->label('Tanggal struk')
                        ->state(fn (Reimbursement $record): string => $record->rentangLabel())
                        ->helperText(fn (Reimbursement $record): ?string => $record->menyeberangTahun()
                            ? 'Struknya menyeberang tahun, jadi pembebanannya jatuh ke dua tahun anggaran.'
                            : null),
                    TextEntry::make('notes')->label('Catatan')->placeholder('Tidak ada')->columnSpanFull(),
                ]),

            Section::make('Persetujuan dan pembayaran')
                ->columns(2)
                ->schema([
                    TextEntry::make('persetujuan_atasan')
                        ->label('Persetujuan atasan')
                        ->state(function (Reimbursement $record): string {
                            if ($record->status === 'draft') {
                                return 'Belum diajukan. Struknya masih bisa diubah pemohon.';
                            }

                            if (filled($record->approval_skipped_reason)) {
                                return 'Lewat persetujuan atasan. '.$record->approval_skipped_reason.'.';
                            }

                            if ($record->status === 'diajukan') {
                                return 'Menunggu '.($record->approver?->full_name ?? 'kepala departemen').'.';
                            }

                            return 'Disetujui '.($record->approver?->full_name ?? 'kepala departemen')
                                .($record->approved_at ? ' pada '.$record->approved_at->translatedFormat('d F Y, H:i') : '')
                                .(filled($record->approval_note) ? '. Catatan: '.$record->approval_note : '.');
                        }),
                    TextEntry::make('pemeriksaan_ga')
                        ->label('Pemeriksaan tim GA')
                        ->state(function (Reimbursement $record): string {
                            if (in_array($record->status, ['draft', 'diajukan'], true)) {
                                return 'Belum sampai tim GA.';
                            }

                            if ($record->status === 'diperiksa') {
                                $tanpa = $record->strukTanpaBukti();

                                return 'Menunggu tim GA memeriksa struknya.'
                                    .($tanpa > 0 ? ' '.$tanpa.' struk belum ada fotonya.' : '');
                            }

                            if (blank($record->verified_at)) {
                                return 'Belum diperiksa.';
                            }

                            return 'Diperiksa '.($record->verifiedByUser?->name ?? 'pengguna yang sudah dihapus')
                                .' pada '.$record->verified_at->translatedFormat('d F Y, H:i').'.';
                        }),
                    TextEntry::make('pembayaran')
                        ->label('Pembayaran')
                        ->columnSpanFull()
                        ->state(function (Reimbursement $record): string {
                            if ($record->status === 'dibayar') {
                                return 'Diganti '.($record->paid_date?->translatedFormat('d F Y') ?? 'tanggalnya tidak dicatat')
                                    .' oleh '.($record->paidByUser?->name ?? 'pengguna yang sudah dihapus')
                                    .(filled($record->payment_reference) ? '. Bukti: '.$record->payment_reference : '.');
                            }

                            return $record->status === 'disetujui'
                                ? 'Sudah disetujui dan menunggu ditransfer ke pemohon. Nilainya sudah terhitung sebagai realisasi anggaran.'
                                : 'Belum sampai tahap pembayaran.';
                        }),
                    TextEntry::make('rejection_reason')
                        ->label(fn (Reimbursement $record): string => 'Alasan ditolak '
                            .($record->rejected_stage === 'ga' ? 'tim GA' : 'atasan'))
                        ->columnSpanFull()
                        ->visible(fn (Reimbursement $record): bool => filled($record->rejection_reason)),
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
                TextColumn::make('employee.full_name')
                    ->label('Pemohon')
                    ->description(fn (Reimbursement $record): string => $record->department?->name
                        ?? 'Belum terbebankan ke departemen')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('title')
                    ->label('Untuk apa')
                    ->description(fn (Reimbursement $record): string => $record->rentangLabel())
                    ->searchable()
                    ->wrap()
                    ->limit(70),
                // Nilai selalu membawa jumlah struknya, karena angka tanpa jumlah lembar
                // tidak bisa dicocokkan dengan amplop yang ada di tangan pemeriksa.
                TextColumn::make('total')
                    ->label('Nilai')
                    ->state(fn (Reimbursement $record): string => $record->totalLabel())
                    ->description(fn (Reimbursement $record): string => $record->lines_count.' struk')
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Reimbursement $record): string => $record->statusLabel())
                    ->color(fn (Reimbursement $record): string => $record->statusColor())
                    ->description(fn (Reimbursement $record): string => $record->tahapLabel())
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Diajukan')
                    ->state(fn (Reimbursement $record): string => $record->submitted_at?->translatedFormat('d M Y') ?? 'Belum diajukan')
                    ->description(fn (Reimbursement $record): string => $record->lamaMenunggu())
                    ->sortable(),
                TextColumn::make('paid_date')
                    ->label('Diganti')
                    ->date('d M Y')
                    ->placeholder('Belum diganti')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_reference')
                    ->label('Bukti transfer')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Yang paling lama menunggu ada di atas, karena uang karyawan yang belum
            // kembali adalah hal yang paling cepat dikeluhkan orang.
            ->defaultSort('submitted_at', 'asc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['employee', 'department', 'approver', 'lines'])
                ->withCount('lines')
                ->orderByRaw("case when status in ('draft','diajukan','diperiksa','disetujui') then 0 else 1 end"))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_atasan')
                    ->label('Menunggu atasan')
                    ->query(fn (Builder $query): Builder => $query->menungguAtasan()),
                Filter::make('menunggu_ga')
                    ->label('Menunggu tim GA')
                    ->query(fn (Builder $query): Builder => $query->menungguGa()),
                Filter::make('menunggu_pembayaran')
                    ->label('Menunggu ditransfer')
                    ->query(fn (Builder $query): Builder => $query->menungguPembayaran()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(Reimbursement::STATUSES)
                    ->multiple(),
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                static::ajukanAction(),
                static::setujuiAction(),
                static::periksaAction(),
                static::bayarAction(),
                static::tolakAction(),
                static::perbaikiAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (Reimbursement $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (Reimbursement $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada pengajuan penggantian biaya')
            ->emptyStateDescription('Karyawan mengumpulkan struk belanja keperluan kerja di sini, lalu mengajukannya. Setelah atasan menyetujui dan tim GA memeriksa, nilainya masuk sendiri ke realisasi anggaran departemen yang dibebani.');
    }

    // ------------------------------------------------------------------ tindakan

    /**
     * Memuat ulang halaman Lihat setelah status berubah, dengan alasan yang sama seperti
     * pada tagihan rekanan: daftar struk hidup di komponen tersendiri dan tidak ikut
     * digambar ulang, sehingga tombol Tambah struk sempat tetap terlihat pada pengajuan
     * yang baru saja dikunci.
     */
    protected static function segarkan(mixed $livewire, Reimbursement $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    public static function ajukanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('ajukan')
            ->label('Ajukan')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn (Reimbursement $record): bool => $record->status === 'draft' && static::bolehMengubah($record))
            ->modalHeading(fn (Reimbursement $record): string => 'Ajukan '.$record->code)
            ->modalDescription(function (Reimbursement $record): string {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    return $alasan;
                }

                $tanpaBukti = $record->strukTanpaBukti();
                $lewat = $record->alasanLewatPersetujuan();

                return $record->totalLabel().' dari '.$record->lines()->count().' struk. '
                    .($lewat === null
                        ? 'Akan menunggu persetujuan '.($record->calonPenyetuju()?->full_name ?? 'kepala departemen').', lalu diperiksa tim GA.'
                        : 'Langsung ke tim GA, karena '.lcfirst($lewat).'.')
                    .($tanpaBukti > 0 ? ' '.$tanpaBukti.' struk belum ada fotonya, dan tim GA akan menanyakannya.' : '')
                    .' Setelah diajukan, struknya tidak bisa diubah lagi.';
            })
            ->modalSubmitActionLabel('Ajukan')
            ->action(function (Reimbursement $record, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    Notification::make()->warning()->title('Belum bisa diajukan')->body($alasan)->persistent()->send();

                    $action->halt();
                }

                $record->ajukan();
                $record->refresh();

                Notification::make()
                    ->success()
                    ->title($record->code.' diajukan')
                    ->body($record->tahapLabel().'.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function setujuiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('setujui')
            ->label('Setujui sebagai atasan')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (Reimbursement $record): bool => $record->status === 'diajukan' && static::bolehMenyetujui($record))
            ->modalHeading(fn (Reimbursement $record): string => 'Setujui '.$record->code)
            ->modalDescription(fn (Reimbursement $record): string => $record->totalLabel().' dari '
                .($record->employee?->full_name ?? 'karyawan yang sudah dihapus')
                .'. Yang Anda setujui adalah bahwa ini memang keperluan kerja. Tim GA yang memeriksa struk dan angkanya setelah ini.')
            ->modalSubmitActionLabel('Setujui pengajuan')
            ->schema([
                Textarea::make('approval_note')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan.'),
            ])
            ->action(function (Reimbursement $record, array $data, $livewire): void {
                if (! $record->setujuiAtasan($data['approval_note'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' disetujui')
                    ->body('Sudah masuk antrean pemeriksaan tim GA.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function periksaAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('periksa')
            ->label('Selesai diperiksa')
            ->icon('heroicon-o-document-magnifying-glass')
            ->color('success')
            ->visible(fn (Reimbursement $record): bool => $record->status === 'diperiksa' && static::allows('verify'))
            ->requiresConfirmation()
            ->modalHeading(fn (Reimbursement $record): string => 'Selesai memeriksa '.$record->code)
            ->modalDescription(function (Reimbursement $record): string {
                $tanpaBukti = $record->strukTanpaBukti();

                return $record->totalLabel().' dari '.$record->lines()->count().' struk'
                    .($tanpaBukti > 0 ? ', dan '.$tanpaBukti.' di antaranya belum ada fotonya' : '')
                    .'. Setelah ini nilainya masuk ke realisasi anggaran '
                    .($record->department?->name ?? 'yang belum terbebankan ke departemen mana pun')
                    .', dan pengajuannya menunggu ditransfer.';
            })
            ->modalSubmitActionLabel('Setujui dan teruskan ke pembayaran')
            ->action(function (Reimbursement $record, $livewire): void {
                if (! $record->verifikasi()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' selesai diperiksa')
                    ->body('Nilainya sekarang terhitung sebagai realisasi anggaran, dan pengajuannya menunggu ditransfer.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function bayarAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('bayar')
            ->label('Tandai sudah diganti')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->visible(fn (Reimbursement $record): bool => $record->status === 'disetujui' && static::allows('pay'))
            ->modalHeading(fn (Reimbursement $record): string => 'Tandai '.$record->code.' sudah diganti')
            ->modalDescription(fn (Reimbursement $record): string => $record->totalLabel().' ke '
                .($record->employee?->full_name ?? 'karyawan yang sudah dihapus')
                .'. Realisasi anggarannya tidak berubah, karena nilainya sudah terhitung sejak selesai diperiksa.')
            ->modalSubmitActionLabel('Simpan pembayaran')
            ->schema([
                DatePicker::make('paid_date')
                    ->label('Tanggal transfer')
                    ->displayFormat('d M Y')
                    ->default(now())
                    ->maxDate(now())
                    ->required(),
                TextInput::make('payment_reference')
                    ->label('Bukti transfer')
                    ->maxLength(80)
                    ->placeholder('Tidak dicatat')
                    ->helperText('Nomor transfer atau nomor voucher. Ini yang dicari saat pemohon menanyakan uangnya sudah masuk atau belum.'),
            ])
            ->action(function (Reimbursement $record, array $data, $livewire): void {
                if (! $record->tandaiDibayar($data['paid_date'], $data['payment_reference'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' ditandai sudah diganti')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function tolakAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tolak')
            ->label('Tolak')
            ->icon('heroicon-o-hand-raised')
            ->color('danger')
            ->visible(fn (Reimbursement $record): bool => ($record->status === 'diajukan' && static::bolehMenyetujui($record))
                || ($record->status === 'diperiksa' && static::allows('verify')))
            ->modalHeading(fn (Reimbursement $record): string => 'Tolak '.$record->code)
            ->modalDescription('Pengajuan yang ditolak tetap tersimpan beserta alasannya, dan pemohon bisa mengembalikannya ke draf untuk memperbaiki struknya.')
            ->modalSubmitActionLabel('Tolak pengajuan')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan ditolak')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: struk tanggal 3 tidak terbaca nominalnya, mohon difoto ulang.'),
            ])
            ->action(function (Reimbursement $record, array $data, $livewire): void {
                // Tahap penolakan diambil dari status saat ini, bukan dari izin orangnya,
                // karena manajer GA memegang kedua izin dan tebakan berdasarkan izin akan
                // salah menuliskan siapa yang mengembalikan.
                $tahap = $record->status === 'diperiksa' ? 'ga' : 'atasan';

                if (! $record->tolak($data['rejection_reason'], $tahap)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' ditolak')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function perbaikiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('perbaiki')
            ->label('Kembalikan ke draf')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->visible(fn (Reimbursement $record): bool => $record->status === 'ditolak' && static::bolehMengubah($record))
            ->requiresConfirmation()
            ->modalHeading(fn (Reimbursement $record): string => 'Kembalikan '.$record->code.' ke draf')
            ->modalDescription('Struknya bisa diubah lagi, lalu diajukan ulang dari awal. Alasan penolakannya sengaja tetap tersimpan supaya bisa dibaca sambil memperbaiki.')
            ->modalSubmitActionLabel('Kembalikan ke draf')
            ->action(function (Reimbursement $record, $livewire): void {
                if (! $record->kembalikanKeDraft()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' kembali menjadi draf')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Batalkan')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (Reimbursement $record): bool => $record->isOpen() && static::bolehMembatalkan($record))
            ->requiresConfirmation()
            ->modalHeading(fn (Reimbursement $record): string => 'Batalkan '.$record->code)
            ->modalDescription(fn (Reimbursement $record): string => $record->status === 'disetujui'
                ? 'Pengajuan ini sudah selesai diperiksa, jadi nilainya sedang terhitung sebagai realisasi anggaran. Membatalkannya mengeluarkan nilai itu dari realisasi.'
                : 'Pengajuan ini tetap tersimpan sebagai catatan, dan tidak masuk hitungan anggaran mana pun.')
            ->modalSubmitActionLabel('Batalkan pengajuan')
            ->action(function (Reimbursement $record, $livewire): void {
                if (! $record->batalkan()) {
                    static::peringatanStatusBerubah();

                    return;
                }

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
            ->body('Pengajuan ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->send();
    }

    // ------------------------------------------------------------------ perizinan

    /**
     * Yang boleh menyetujui sebagai atasan: kepala departemen yang tercatat sebagai
     * penyetuju pengajuan ini, atau siapa pun yang memegang izin approve supaya antrean
     * tidak tersangkut saat kepala departemennya cuti panjang.
     */
    protected static function bolehMenyetujui(Reimbursement $record): bool
    {
        if (static::allows('approve')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null && (int) $record->approver_employee_id === (int) $karyawan->getKey();
    }

    /**
     * Yang boleh menyusun dan mengajukan: pemohonnya sendiri, atau tim GA yang memang
     * mencatatkan untuk orang lain.
     */
    protected static function bolehMengubah(Reimbursement $record): bool
    {
        if (static::allows('verify')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null && (int) $record->employee_id === (int) $karyawan->getKey();
    }

    protected static function bolehMembatalkan(Reimbursement $record): bool
    {
        return static::bolehMengubah($record) || static::allows('approve');
    }

    /**
     * Isi pengajuan hanya bisa diubah selama masih draf. Setelah diajukan, mengubahnya
     * berarti mengubah angka yang sedang atau sudah ditandatangani orang lain tanpa ia tahu.
     */
    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record)
            && $record->status === 'draft'
            && static::bolehMengubah($record);
    }

    /**
     * Pengajuan yang sudah selesai diperiksa tidak boleh dihapus, karena nilainya sudah
     * menjadi bagian dari realisasi anggaran dan menghapusnya mengubah angka tahun
     * berjalan tanpa jejak. Yang batal diganti dibatalkan, bukan dihapus.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record)
            && in_array($record->status, ['draft', 'ditolak', 'dibatalkan'], true);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReimbursements::route('/'),
            'create' => CreateReimbursement::route('/create'),
            'view' => ViewReimbursement::route('/{record}'),
        ];
    }
}
