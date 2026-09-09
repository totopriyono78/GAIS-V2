<?php

namespace App\Filament\Resources\SupplyRequests;

use App\Filament\Resources\SupplyRequests\Pages\CreateSupplyRequest;
use App\Filament\Resources\SupplyRequests\Pages\ListSupplyRequests;
use App\Filament\Resources\SupplyRequests\Pages\ViewSupplyRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SupplyRequest;
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
 * Permintaan pemakaian ATK.
 *
 * Alurnya sengaja kembar dengan permintaan perbaikan, pemesanan kendaraan, dan penggantian
 * biaya. Orang yang meminta pulpen adalah orang yang sama yang melaporkan AC bocor, dan
 * membuat alur berbeda untuk hal yang sama sama "minta lalu disetujui" hanya membuang
 * waktunya untuk mempelajari layar yang seharusnya sudah ia kenali.
 *
 * Satu hal yang berbeda dan perlu diingat saat membaca berkas ini: langkah terakhir di sini
 * bukan penandaan, melainkan perubahan angka. Menyerahkan barang berarti stok berkurang, dan
 * stok yang berkurang langsung menjadi realisasi anggaran ATK departemen pemohon. Karena itu
 * tombolnya selalu menyebutkan apa yang akan terjadi sebelum ditekan, dan penyerahan yang
 * stoknya kurang ditolak dengan menyebut barang dan angkanya, bukan dengan pesan umum.
 */
class SupplyRequestResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = SupplyRequest::class;

    protected static string $moduleCode = 'supply_requests';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Office Supplies';

    protected static ?string $navigationLabel = 'Supply Requests';

    protected static ?string $modelLabel = 'supply request';

    protected static ?string $pluralModelLabel = 'supply requests';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Lencana menghitung yang menunggu tindakan orang yang sedang masuk: antrean tanda
     * tangan untuk kepala departemen, antrean penyerahan untuk tim GA. Yang tidak menunggu
     * siapa pun tidak diberi angka, karena angka yang tidak bisa ditindaklanjuti hanya jadi
     * noda tetap di menu.
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

        $bisaSerahkan = static::allows('issue');
        $karyawan = Auth::user()?->employee;
        $departemenSaya = $karyawan?->headedDepartments()->pluck('id')->all() ?? [];

        if (! $bisaSerahkan && $departemenSaya === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $sub) use ($bisaSerahkan, $departemenSaya): void {
            if ($bisaSerahkan) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPenyerahan());
            }

            if ($departemenSaya !== []) {
                $sub->orWhere(fn (Builder $q) => $q->menungguAtasan()->whereIn('department_id', $departemenSaya));
            }
        });
    }

    /**
     * Tanpa izin read_all, seseorang hanya melihat permintaannya sendiri dan permintaan
     * departemen yang ia kepalai. Sama seperti permintaan perbaikan: daftar belanja satu
     * orang bukan hal yang perlu dibaca seluruh kantor.
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
            Section::make('Request')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('purpose')
                        ->label('Untuk keperluan apa')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->placeholder('Contoh: perlengkapan tulis rapat bulanan tim Finance')
                        ->helperText('Satu kalimat yang menjelaskan seluruh barang di dalamnya. Ini yang dibaca atasan lebih dulu.'),

                    /*
                     * Pemilihan pemohon dikunci pada diri sendiri tanpa izin
                     * request_for_others. Keputusan pemilik proyek pada 8 September 2026:
                     * karyawan biasa mengajukan untuk dirinya, dan perwakilan departemen
                     * yang mengumpulkan kebutuhan timnya dibedakan lewat izin, bukan lewat
                     * jenis permintaan yang baru. Pilihan yang terkunci tetap ditampilkan
                     * sebagai select berisi satu nama, bukan disembunyikan, supaya pemohon
                     * tetap bisa membaca atas nama siapa permintaan ini dibuat.
                     */
                    Select::make('employee_id')
                        ->label('Yang mengajukan')
                        ->options(fn (?Model $record): array => static::pilihanPemohon($record))
                        ->searchable()
                        ->required()
                        ->live()
                        ->default(fn (): ?int => Auth::user()?->employee?->getKey())
                        ->disabled(fn (): bool => ! static::allows('request_for_others'))
                        ->dehydrated()
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $set('department_id', Employee::find($state)?->department_id);
                        })
                        ->helperText(fn (): string => static::allows('request_for_others')
                            ? 'Bawaannya diri sendiri. Ganti kalau Anda mengumpulkan kebutuhan orang lain di departemen Anda.'
                            : 'Terkunci pada diri sendiri. Untuk mengajukan atas nama orang lain, izin Ajukan atas nama orang lain perlu ditambahkan ke role Anda.'),

                    Select::make('department_id')
                        ->label('Departemen yang memakai')
                        ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->default(fn (): ?int => Auth::user()?->employee?->department_id)
                        ->helperText('Terisi sendiri dari departemen pemohon. Departemen ini yang nanti tercatat memakai barangnya, dan pemakaian itu langsung masuk ke realisasi anggaran ATK-nya.'),

                    DatePicker::make('needed_date')
                        ->label('Dibutuhkan tanggal')
                        ->displayFormat('d M Y')
                        // Batas tanggal hanya berlaku saat permintaan dibuat. Draf yang
                        // sudah lama tersimpan boleh saja tanggal butuhnya sudah lewat,
                        // dan menolaknya berarti draf itu tidak akan pernah bisa disimpan
                        // lagi tanpa mengganti tanggal yang mungkin memang benar.
                        ->minDate(fn (string $operation): ?string => $operation === 'create'
                            ? now()->startOfDay()->toDateString()
                            : null)
                        ->placeholder('Tidak menyebut tanggal')
                        ->helperText('Boleh dikosongkan. Kalau diisi, tim GA memakainya untuk mengurutkan mana yang dikerjakan lebih dulu.'),

                    Placeholder::make('jalur_persetujuan')
                        ->label('Setelah diajukan')
                        ->content(function ($get): string {
                            if (blank($get('employee_id')) || blank($get('department_id'))) {
                                return 'Pilih dulu pemohon dan departemennya.';
                            }

                            $contoh = new SupplyRequest;
                            $contoh->employee_id = $get('employee_id');
                            $contoh->department_id = $get('department_id');

                            $alasan = $contoh->alasanLewatPersetujuan();

                            return $alasan === null
                                ? 'Menunggu persetujuan '.($contoh->calonPenyetuju()?->full_name ?? 'kepala departemen').', lalu tim GA menyerahkan barangnya.'
                                : 'Langsung ke tim GA untuk diserahkan. Alasannya: '.lcfirst($alasan).'.';
                        }),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: kalau spidol hitam habis, warna biru juga boleh.'),
                ]),
        ]);
    }

    /**
     * Daftar pemohon yang boleh dipilih. Tanpa izin request_for_others isinya hanya satu
     * nama, yaitu karyawan yang sedang masuk, sehingga tidak ada cara membuat permintaan
     * atas nama orang lain lewat layar ini maupun lewat permintaan Livewire yang dikarang.
     *
     * Pemohon yang sudah tercatat di permintaan selalu ikut masuk daftar, walau ia bukan
     * pengguna yang sedang masuk dan walau ia sudah tidak aktif. Tanpa itu, staf GA yang
     * memegang izin serah tetapi tidak memegang izin ajukan atas nama orang lain akan
     * membuka draf milik orang lain dan menemukan pilihan pemohon yang kosong, lalu gagal
     * menyimpan karena kolomnya wajib isi.
     *
     * @return array<int, string>
     */
    protected static function pilihanPemohon(?Model $record = null): array
    {
        $pilihan = static::allows('request_for_others')
            ? Employee::query()
                ->where('is_active', true)
                ->orderBy('full_name')
                ->pluck('full_name', 'id')
                ->all()
            : [];

        if ($pilihan === []) {
            $karyawan = Auth::user()?->employee;

            if ($karyawan !== null) {
                $pilihan[$karyawan->getKey()] = $karyawan->full_name;
            }
        }

        if ($record instanceof SupplyRequest && filled($record->employee_id)
            && ! array_key_exists($record->employee_id, $pilihan)) {
            $pilihan[$record->employee_id] = $record->employee?->full_name
                ?? 'Karyawan sudah dihapus';
        }

        return $pilihan;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (SupplyRequest $record): string => $record->statusLabel())
                        ->color(fn (SupplyRequest $record): string => $record->statusColor())
                        ->helperText(fn (SupplyRequest $record): string => $record->tahapLabel()),
                    TextEntry::make('jenis')
                        ->label('Isi permintaan')
                        ->state(fn (SupplyRequest $record): string => $record->jenisLabel())
                        ->helperText(fn (SupplyRequest $record): ?string => $record->status === 'diserahkan'
                            && $record->barisKurang() > 0
                            ? $record->barisKurang().' barang diserahkan kurang dari yang diminta'
                            : null),
                    TextEntry::make('purpose')->label('Untuk keperluan apa')->columnSpanFull(),
                    TextEntry::make('employee.full_name')->label('Pemohon')->placeholder('Karyawan sudah dihapus'),
                    TextEntry::make('department.name')->label('Departemen yang memakai')->placeholder('Departemen sudah dihapus'),
                    TextEntry::make('needed_date')
                        ->label('Dibutuhkan')
                        ->state(fn (SupplyRequest $record): string => $record->kebutuhanLabel()),
                    TextEntry::make('notes')->label('Catatan')->placeholder('Tidak ada')->columnSpanFull(),
                ]),

            Section::make('Approval & Handover')
                ->columns(2)
                ->schema([
                    TextEntry::make('persetujuan_atasan')
                        ->label('Persetujuan atasan')
                        ->state(function (SupplyRequest $record): string {
                            if ($record->status === 'draft') {
                                return 'Belum diajukan. Daftar barangnya masih bisa diubah pemohon.';
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
                    TextEntry::make('penyerahan')
                        ->label('Penyerahan barang')
                        ->state(function (SupplyRequest $record): string {
                            if (in_array($record->status, ['draft', 'diajukan'], true)) {
                                return 'Belum sampai tim GA.';
                            }

                            if ($record->status === 'disetujui') {
                                $kurang = count($record->kekuranganStok());

                                return 'Menunggu tim GA menyerahkan barangnya.'
                                    .($kurang > 0 ? ' '.$kurang.' barang stoknya belum cukup hari ini.' : '');
                            }

                            if ($record->status !== 'diserahkan') {
                                return 'Tidak jadi diserahkan.';
                            }

                            return 'Diserahkan '.($record->issuedByUser?->name ?? 'pengguna yang sudah dihapus')
                                .($record->issued_at ? ' pada '.$record->issued_at->translatedFormat('d F Y, H:i') : '')
                                .(filled($record->issue_note) ? '. Catatan: '.$record->issue_note : '.');
                        }),
                    TextEntry::make('dampak_stok')
                        ->label('Dampak ke stok dan anggaran')
                        ->columnSpanFull()
                        ->state(fn (SupplyRequest $record): string => $record->status === 'diserahkan'
                            ? 'Stok sudah berkurang, dan tiap barisnya menunjuk mutasi barang keluar yang lahir dari penyerahan ini. Nilainya sudah terhitung sebagai pemakaian ATK '
                                .($record->department?->name ?? 'departemen yang sudah dihapus').'.'
                            : 'Belum ada. Stok baru berkurang saat barangnya benar benar diserahkan, bukan saat permintaan disetujui.'),
                    TextEntry::make('rejection_reason')
                        ->label(fn (SupplyRequest $record): string => 'Alasan ditolak '
                            .($record->rejected_stage === 'ga' ? 'tim GA' : 'atasan'))
                        ->columnSpanFull()
                        ->visible(fn (SupplyRequest $record): bool => filled($record->rejection_reason)),
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
                    ->description(fn (SupplyRequest $record): string => $record->department?->name
                        ?? 'Departemen sudah dihapus')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('purpose')
                    ->label('Untuk keperluan apa')
                    ->searchable()
                    ->wrap()
                    ->limit(70),
                // Jumlah jenis barang, bukan jumlah satuan, karena satuannya bercampur.
                // Sepuluh pulpen ditambah dua rim kertas bukan dua belas apa pun.
                TextColumn::make('lines_count')
                    ->label('Isi')
                    ->state(fn (SupplyRequest $record): string => $record->jenisLabel())
                    ->description(fn (SupplyRequest $record): string => $record->kebutuhanLabel()),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (SupplyRequest $record): string => $record->statusLabel())
                    ->color(fn (SupplyRequest $record): string => $record->statusColor())
                    ->description(fn (SupplyRequest $record): string => $record->tahapLabel())
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Diajukan')
                    ->state(fn (SupplyRequest $record): string => $record->submitted_at?->translatedFormat('d M Y') ?? 'Belum diajukan')
                    ->description(fn (SupplyRequest $record): string => $record->lamaMenunggu())
                    ->sortable(),
                TextColumn::make('issued_at')
                    ->label('Diserahkan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum diserahkan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('issuedByUser.name')
                    ->label('Diserahkan oleh')
                    ->placeholder('Belum diserahkan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Yang paling lama menunggu ada di atas, dan yang sudah selesai turun ke bawah.
            ->defaultSort('submitted_at', 'asc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['employee', 'department', 'approver', 'lines'])
                ->withCount('lines')
                ->orderByRaw("case when status in ('draft','diajukan','disetujui') then 0 else 1 end"))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_atasan')
                    ->label('Menunggu atasan')
                    ->query(fn (Builder $query): Builder => $query->menungguAtasan()),
                Filter::make('menunggu_penyerahan')
                    ->label('Menunggu diserahkan')
                    ->query(fn (Builder $query): Builder => $query->menungguPenyerahan()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(SupplyRequest::STATUSES)
                    ->multiple(),
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                static::ajukanAction(),
                static::setujuiAction(),
                static::serahkanAction(),
                static::tolakAction(),
                static::perbaikiAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyRequest $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyRequest $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada permintaan ATK')
            ->emptyStateDescription('Karyawan menuliskan barang yang dibutuhkannya di sini, lalu mengajukannya. Setelah atasan menyetujui dan tim GA menyerahkan barangnya, stok berkurang sendiri dan pemakaiannya tercatat atas nama departemen pemohon.');
    }

    // ------------------------------------------------------------------ tindakan

    /**
     * Memuat ulang halaman Lihat setelah status berubah. Daftar barang hidup di komponen
     * Livewire tersendiri dan tidak ikut digambar ulang, sehingga tombol Tambah barang
     * sempat tetap terlihat pada permintaan yang baru saja dikunci.
     */
    protected static function segarkan(mixed $livewire, SupplyRequest $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    public static function ajukanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('ajukan')
            ->label('Submit')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn (SupplyRequest $record): bool => $record->status === 'draft' && static::bolehMengubah($record))
            ->modalHeading(fn (SupplyRequest $record): string => 'Submit '.$record->code)
            ->modalDescription(function (SupplyRequest $record): string {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    return $alasan;
                }

                $lewat = $record->alasanLewatPersetujuan();
                $kurang = $record->kekuranganStok();

                return $record->jenisLabel().'. '
                    .($lewat === null
                        ? 'Akan menunggu persetujuan '.($record->calonPenyetuju()?->full_name ?? 'kepala departemen').', lalu tim GA menyerahkan barangnya.'
                        : 'Langsung ke tim GA, karena '.lcfirst($lewat).'.')
                    .($kurang !== []
                        ? ' Hari ini '.count($kurang).' barang stoknya belum cukup, dan itu tidak menghalangi pengajuan. Tim GA yang memutuskan berapa yang bisa diserahkan.'
                        : '')
                    .' Setelah diajukan, daftar barangnya tidak bisa diubah lagi.';
            })
            ->modalSubmitActionLabel('Submit')
            ->action(function (SupplyRequest $record, Action $action, $livewire): void {
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
            ->label('Approve as Supervisor')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (SupplyRequest $record): bool => $record->status === 'diajukan' && static::bolehMenyetujui($record))
            ->modalHeading(fn (SupplyRequest $record): string => 'Approve '.$record->code)
            ->modalDescription(fn (SupplyRequest $record): string => $record->jenisLabel().' untuk '
                .($record->employee?->full_name ?? 'karyawan yang sudah dihapus')
                .'. Yang Anda setujui adalah bahwa barangnya memang dibutuhkan. Stok belum berkurang sekarang, dan baru berkurang saat tim GA menyerahkannya.')
            ->modalSubmitActionLabel('Approve Request')
            ->schema([
                Textarea::make('approval_note')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan.'),
            ])
            ->action(function (SupplyRequest $record, array $data, $livewire): void {
                if (! $record->setujuiAtasan($data['approval_note'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' disetujui')
                    ->body('Sudah masuk antrean penyerahan tim GA.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Menyerahkan barang. Ini satu satunya tindakan di modul ini yang mengubah stok.
     *
     * Kekurangan stok diperiksa dua kali dengan sengaja: sekali saat kotak dialog digambar,
     * supaya tim GA membaca angkanya sebelum menekan tombol, dan sekali lagi tepat sebelum
     * disimpan, supaya dua orang yang membuka layar bersamaan tidak sama sama mengeluarkan
     * barang yang sama. Pemeriksaan pertama enak dibaca, pemeriksaan kedua yang menjaga.
     */
    public static function serahkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('serahkan')
            ->label('Issue Supplies')
            ->icon('heroicon-o-inbox-arrow-down')
            ->color('success')
            ->visible(fn (SupplyRequest $record): bool => $record->status === 'disetujui' && static::allows('issue'))
            ->modalHeading(fn (SupplyRequest $record): string => 'Issue Supplies for '.$record->code)
            ->modalDescription(function (SupplyRequest $record): string {
                $kurang = $record->kekuranganStok();

                if ($kurang !== []) {
                    return 'Belum bisa diserahkan seluruhnya. '.implode('. ', $kurang)
                        .'. Ubah jumlah serah barang itu di daftar Requested Items lebih dulu, atau tambah stoknya dari menu Supply Movements.';
                }

                return 'Menyerahkan '.$record->jenisLabel().' kepada '
                    .($record->employee?->full_name ?? 'karyawan yang sudah dihapus')
                    .'. Setelah ini stok berkurang, satu mutasi barang keluar lahir untuk tiap barang, dan pemakaiannya tercatat atas nama '
                    .($record->department?->name ?? 'departemen yang sudah dihapus')
                    .' di realisasi anggaran ATK.';
            })
            ->modalSubmitActionLabel('Issue and Reduce Stock')
            ->schema([
                Textarea::make('issue_note')
                    ->label('Catatan penyerahan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Contoh: spidol hitam diganti biru karena hitam habis.'),
            ])
            ->action(function (SupplyRequest $record, array $data, Action $action, $livewire): void {
                $kurang = $record->kekuranganStok();

                if ($kurang !== []) {
                    Notification::make()
                        ->warning()
                        ->title('Stok belum cukup')
                        ->body(implode('. ', $kurang).'.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                if (! $record->serahkan($data['issue_note'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                $record->refresh();

                Notification::make()
                    ->success()
                    ->title($record->code.' sudah diserahkan')
                    ->body('Stok sudah berkurang, dan pemakaiannya tercatat atas nama '
                        .($record->department?->name ?? 'departemen yang sudah dihapus').'.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function tolakAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tolak')
            ->label('Reject')
            ->icon('heroicon-o-hand-raised')
            ->color('danger')
            ->visible(fn (SupplyRequest $record): bool => ($record->status === 'diajukan' && static::bolehMenyetujui($record))
                || ($record->status === 'disetujui' && static::allows('issue')))
            ->modalHeading(fn (SupplyRequest $record): string => 'Reject '.$record->code)
            ->modalDescription('Permintaan yang ditolak tetap tersimpan beserta alasannya, dan pemohon bisa mengembalikannya ke draf untuk memperbaiki daftarnya. Stok tidak tersentuh.')
            ->modalSubmitActionLabel('Reject Request')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan ditolak')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: kertas A3 tidak distok kantor, silakan diajukan lewat pembelian.'),
            ])
            ->action(function (SupplyRequest $record, array $data, $livewire): void {
                // Tahap penolakan diambil dari status saat ini, bukan dari izin orangnya,
                // karena manajer GA memegang kedua izin dan tebakan berdasarkan izin akan
                // salah menuliskan siapa yang mengembalikan.
                $tahap = $record->status === 'disetujui' ? 'ga' : 'atasan';

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
            ->label('Return to Draft')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->visible(fn (SupplyRequest $record): bool => $record->status === 'ditolak' && static::bolehMengubah($record))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyRequest $record): string => 'Return '.$record->code.' to Draft')
            ->modalDescription('Daftar barangnya bisa diubah lagi, lalu diajukan ulang dari awal. Alasan penolakannya sengaja tetap tersimpan supaya bisa dibaca sambil memperbaiki.')
            ->modalSubmitActionLabel('Return to Draft')
            ->action(function (SupplyRequest $record, $livewire): void {
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
            ->label('Cancel')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (SupplyRequest $record): bool => $record->isOpen() && static::bolehMembatalkan($record))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyRequest $record): string => 'Cancel '.$record->code)
            ->modalDescription('Permintaan ini tetap tersimpan sebagai catatan. Stok tidak tersentuh, karena stok memang baru berkurang saat barangnya diserahkan.')
            ->modalSubmitActionLabel('Cancel Request')
            ->action(function (SupplyRequest $record, $livewire): void {
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
            ->body('Permintaan ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->send();
    }

    // ------------------------------------------------------------------ perizinan

    /**
     * Yang boleh menyetujui sebagai atasan: kepala departemen yang tercatat sebagai
     * penyetuju permintaan ini, atau siapa pun yang memegang izin approve supaya antrean
     * tidak tersangkut saat kepala departemennya cuti panjang.
     */
    protected static function bolehMenyetujui(SupplyRequest $record): bool
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
    public static function bolehMengubah(SupplyRequest $record): bool
    {
        if (static::allows('issue')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null && (int) $record->employee_id === (int) $karyawan->getKey();
    }

    protected static function bolehMembatalkan(SupplyRequest $record): bool
    {
        return static::bolehMengubah($record) || static::allows('approve');
    }

    /**
     * Isi permintaan hanya bisa diubah selama masih draf. Setelah diajukan, mengubahnya
     * berarti mengubah daftar yang sedang atau sudah ditandatangani orang lain tanpa ia tahu.
     */
    public static function canEdit(Model $record): bool
    {
        return static::allows('update')
            && $record->status === 'draft'
            && static::bolehMengubah($record);
    }

    /**
     * Permintaan yang sudah diserahkan tidak boleh dihapus. Barisnya menunjuk mutasi stok
     * yang sudah lahir, dan menghapus permintaannya akan memutus tautan itu sehingga mutasi
     * barang keluar tetap ada tetapi tidak lagi bisa dijelaskan datangnya dari mana.
     */
    public static function canDelete(Model $record): bool
    {
        return static::allows('delete')
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
            'index' => ListSupplyRequests::route('/'),
            'create' => CreateSupplyRequest::route('/create'),
            'view' => ViewSupplyRequest::route('/{record}'),
        ];
    }
}
