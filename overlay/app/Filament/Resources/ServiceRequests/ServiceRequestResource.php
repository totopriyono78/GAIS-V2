<?php

namespace App\Filament\Resources\ServiceRequests;

use App\Filament\Resources\ServiceRequests\Pages\CreateServiceRequest;
use App\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use App\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use App\Filament\Resources\WorkOrders\WorkOrderResource;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\Vendor;
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
 * Permintaan perbaikan dari karyawan.
 *
 * Layar ini dipakai tiga orang yang berbeda kepentingannya, dan itu yang membentuk
 * susunannya. Karyawan hanya ingin melapor lalu tahu tiketnya sampai mana. Kepala
 * departemen hanya ingin melihat yang menunggu tanda tangannya. Tim GA ingin melihat
 * antrean yang sudah disetujui dan mengubahnya menjadi pekerjaan.
 *
 * Karena itu daftar ini menyempit sendiri: tanpa izin service_requests.read_all,
 * seseorang hanya melihat tiketnya sendiri dan tiket departemen yang dikepalainya.
 * Tim GA yang punya izin itu melihat semuanya.
 *
 * Tiketnya sendiri tidak pernah menjadi tempat mencatat pekerjaan. Begitu diterima,
 * satu perintah kerja korektif lahir dan pekerjaannya pindah ke sana. Yang tinggal di
 * sini adalah catatan siapa melapor kapan, siapa menyetujui, dan berapa lama menunggu.
 */
class ServiceRequestResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = ServiceRequest::class;

    protected static string $moduleCode = 'service_requests';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static string|UnitEnum|null $navigationGroup = 'Maintenance';

    protected static ?string $navigationLabel = 'Corrective Maintenance';

    protected static ?string $modelLabel = 'corrective maintenance';

    protected static ?string $pluralModelLabel = 'corrective maintenance';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Lencana menu berisi yang menunggu tindakan orang yang sedang masuk, bukan jumlah
     * seluruh tiket terbuka. Angka yang tidak menuntut apa apa dari pembacanya akan
     * berhenti dibaca dalam seminggu.
     */
    public static function getNavigationBadge(): ?string
    {
        $jumlah = static::antrianSaya()->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::antrianSaya()->where('priority', 'mendesak')->exists() ? 'danger' : 'warning';
    }

    /**
     * Tiket yang menunggu orang ini. Kepala departemen menunggu menandatangani,
     * tim GA menunggu menerima. Orang yang bukan keduanya tidak menunggu apa apa.
     */
    protected static function antrianSaya(): Builder
    {
        $query = static::getEloquentQuery();

        $bisaTerima = static::allows('accept');
        $karyawan = Auth::user()?->employee;
        $departemenSaya = $karyawan?->headedDepartments()->pluck('id')->all() ?? [];

        if ($bisaTerima && $departemenSaya === []) {
            return $query->menungguGa();
        }

        if (! $bisaTerima && $departemenSaya === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $sub) use ($bisaTerima, $departemenSaya): void {
            if ($bisaTerima) {
                $sub->orWhere(fn (Builder $q) => $q->menungguGa());
            }

            if ($departemenSaya !== []) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPersetujuan()->whereIn('department_id', $departemenSaya));
            }
        });
    }

    /**
     * Penyempitan daftar. Tanpa izin read_all, seseorang melihat tiketnya sendiri dan
     * tiket departemen yang dikepalainya, karena keduanya memang urusannya.
     *
     * Ini dipakai menggantikan kolom data_scope pada role, yang tersimpan tetapi tidak
     * pernah dibaca query mana pun. Izin yang terbaca di matriks lebih jujur daripada
     * kolom yang tampak mengatur tetapi tidak mengatur apa apa.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (static::allows('read_all')) {
            return $query;
        }

        $karyawan = Auth::user()?->employee;

        if ($karyawan === null) {
            // Akun tanpa data karyawan tidak punya tiket sendiri. Daftar kosong lebih
            // benar daripada diam diam menampilkan tiket seluruh kantor.
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
            Section::make('What Needs Fixing')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Nomor permintaan')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis setelah disimpan')
                        ->visible(fn (?ServiceRequest $record): bool => $record?->exists ?? false),
                    Select::make('service_request_category_id')
                        ->label('Jenis permintaan')
                        ->options(fn (): array => ServiceRequestCategory::query()
                            ->where('is_active', true)
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (ServiceRequestCategory $k) => [$k->id => $k->pickerLabel()])
                            ->all())
                        ->searchable()
                        ->required()
                        ->live()
                        // Jenis membawa prioritas bawaannya sendiri, dan pemohon masih
                        // boleh menaikkannya di kolom sebelah.
                        ->afterStateUpdated(function ($state, $set): void {
                            $bawaan = ServiceRequestCategory::find($state)?->default_priority;

                            if (filled($bawaan)) {
                                $set('priority', $bawaan);
                            }
                        })
                        ->helperText(fn ($get): string => ServiceRequestCategory::find($get('service_request_category_id'))?->description
                            ?? 'Pilih yang paling mendekati. Tim GA bisa memindahkannya nanti.'),
                    Select::make('priority')
                        ->label('Seberapa mendesak')
                        ->options(ServiceRequest::PRIORITIES)
                        ->default('normal')
                        ->required()
                        ->live()
                        ->helperText('Mendesak berarti tidak bisa menunggu sampai besok, seperti kebocoran air atau listrik mati.'),
                    TextInput::make('title')
                        ->label('Ringkasan')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->placeholder('Contoh: AC ruang rapat lantai 3 tidak dingin')
                        ->helperText('Satu kalimat. Ini yang terbaca di daftar antrean tim GA.'),
                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull()
                        ->placeholder('Sejak kapan, seberapa parah, dan apa yang sudah dicoba.')
                        ->helperText('Makin jelas keterangannya, makin kecil kemungkinan teknisi datang tanpa membawa alat yang tepat.'),
                ]),

            Section::make('Where')
                ->columns(2)
                ->schema([
                    Select::make('location_id')
                        ->label('Lokasi')
                        ->relationship('location', 'name', fn (Builder $query) => $query->orderBy('code'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Ruangan atau area tempat masalahnya berada.'),
                    Select::make('asset_id')
                        ->label('Aset yang bermasalah')
                        ->relationship(
                            'asset',
                            'name',
                            fn (Builder $query) => $query->where('status', '!=', 'dilepas')->orderBy('code'),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Asset $record): string => $record->code.' '.$record->name)
                        ->searchable(['code', 'name'])
                        ->placeholder('Bukan aset tertentu')
                        ->helperText('Boleh dikosongkan. Lampu koridor yang mati bukan kerusakan satu aset tertentu.'),
                ]),

            Section::make('Requester')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('requester_employee_id')
                        ->label('Yang melaporkan')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->pluck('full_name', 'id')
                            ->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->default(fn (): ?int => Auth::user()?->employee?->getKey())
                        // Orang lain boleh dipilih, karena tidak semua karyawan punya
                        // akun, dan resepsionis sering melapor mewakili orang lain.
                        ->helperText('Bawaannya diri sendiri. Ganti kalau Anda melapor mewakili orang lain.'),
                    Placeholder::make('jalur_persetujuan')
                        ->label('Setelah diajukan')
                        ->content(function ($get): string {
                            $contoh = new ServiceRequest([
                                'requester_employee_id' => $get('requester_employee_id'),
                                'priority' => $get('priority'),
                            ]);
                            $contoh->department_id = Employee::find($get('requester_employee_id'))?->department_id;

                            if (blank($contoh->requester_employee_id)) {
                                return 'Pilih dulu siapa yang melaporkan.';
                            }

                            $alasan = $contoh->alasanLewatPersetujuan();

                            return $alasan === null
                                ? 'Menunggu persetujuan '.($contoh->calonPenyetuju()?->full_name ?? 'kepala departemen').', lalu diteruskan ke tim GA.'
                                : 'Langsung masuk antrean tim GA. Alasannya: '.strtolower($alasan).'.';
                        })
                        ->helperText('Ditampilkan sebelum disimpan supaya tidak ada lompatan persetujuan yang terjadi diam diam.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')
                        ->label('Nomor')
                        ->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (ServiceRequest $record): string => $record->statusLabel())
                        ->color(fn (ServiceRequest $record): string => $record->statusColor()),
                    TextEntry::make('priority')
                        ->label('Prioritas')
                        ->badge()
                        ->state(fn (ServiceRequest $record): string => $record->priorityLabel())
                        ->color(fn (ServiceRequest $record): string => $record->priorityColor()),
                    TextEntry::make('title')
                        ->label('Ringkasan')
                        ->columnSpanFull(),
                    TextEntry::make('description')
                        ->label('Keterangan')
                        ->columnSpanFull(),
                    TextEntry::make('category.name')
                        ->label('Jenis')
                        ->placeholder('Belum dikelompokkan'),
                    TextEntry::make('location.name')
                        ->label('Lokasi')
                        ->placeholder('Tidak dicatat'),
                    TextEntry::make('asset.code')
                        ->label('Aset')
                        ->state(fn (ServiceRequest $record): string => trim(($record->asset?->code ?? '').' '.($record->asset?->name ?? ''))
                            ?: 'Bukan aset tertentu'),
                ]),

            Section::make('Ticket Progress')
                ->columns(3)
                ->description('Urutan waktunya, dari yang melapor sampai pekerjaannya selesai.')
                ->schema([
                    TextEntry::make('requester.full_name')
                        ->label('Dilaporkan oleh')
                        ->placeholder('Tidak tercatat'),
                    TextEntry::make('department.name')
                        ->label('Departemen saat melapor')
                        ->placeholder('Tidak tercatat'),
                    TextEntry::make('submitted_at')
                        ->label('Diajukan')
                        ->state(fn (ServiceRequest $record): string => $record->submitted_at?->translatedFormat('d F Y, H:i')
                            ?? 'Tidak tercatat'),
                    TextEntry::make('penyetuju')
                        ->label('Persetujuan')
                        ->columnSpanFull()
                        ->state(function (ServiceRequest $record): string {
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
                    TextEntry::make('accepted_at')
                        ->label('Diterima tim GA')
                        ->state(fn (ServiceRequest $record): string => $record->accepted_at
                            ? $record->accepted_at->translatedFormat('d F Y, H:i')
                                .' oleh '.($record->acceptedByUser?->name ?? 'pengguna yang sudah dihapus')
                            : 'Belum diterima'),
                    TextEntry::make('sla_due_at')
                        ->label('Batas waktu')
                        ->state(fn (ServiceRequest $record): string => $record->slaLabel())
                        ->badge()
                        ->color(fn (ServiceRequest $record): string => $record->melewatiSla() ? 'danger' : 'gray'),
                    TextEntry::make('lama_menunggu')
                        ->label('Lama menunggu sebelum diterima')
                        ->state(fn (ServiceRequest $record): string => $record->lamaMenunggu()),
                    TextEntry::make('workOrder.code')
                        ->label('Perintah kerja')
                        ->columnSpanFull()
                        ->state(fn (ServiceRequest $record): string => $record->workOrder
                            ? $record->workOrder->code.', '.strtolower($record->workOrder->statusLabel())
                            : 'Belum ada. Perintah kerja lahir saat tim GA menerima permintaan ini.')
                        ->url(fn (ServiceRequest $record): ?string => $record->workOrder
                            ? WorkOrderResource::getUrl('edit', ['record' => $record->workOrder])
                            : null),
                    TextEntry::make('rejection_reason')
                        ->label('Alasan ditolak')
                        ->columnSpanFull()
                        ->visible(fn (ServiceRequest $record): bool => filled($record->rejection_reason)),
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
                // Jenis dan lokasi digabung sebagai keterangan di bawah ringkasan, bukan
                // dua kolom sendiri. Tabel ini berhenti di enam kolom, dan lokasi yang
                // berdiri sendiri memakan lebar yang lebih dibutuhkan kalimat masalahnya.
                TextColumn::make('title')
                    ->label('Permintaan')
                    ->description(fn (ServiceRequest $record): ?string => implode(', ', array_filter([
                        $record->category?->name,
                        $record->location?->name,
                    ])) ?: null)
                    ->searchable(['title', 'description'])
                    ->wrap()
                    ->limit(80),
                TextColumn::make('requester.full_name')
                    ->label('Pemohon')
                    ->description(fn (ServiceRequest $record): ?string => $record->department?->name)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->placeholder('Tidak dicatat')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->state(fn (ServiceRequest $record): string => $record->priorityLabel())
                    ->color(fn (ServiceRequest $record): string => $record->priorityColor())
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (ServiceRequest $record): string => $record->statusLabel())
                    ->color(fn (ServiceRequest $record): string => $record->statusColor())
                    ->description(fn (ServiceRequest $record): ?string => $record->isOpen()
                        ? 'Menunggu '.$record->lamaMenunggu()
                        : null)
                    ->sortable(),
                TextColumn::make('sla_due_at')
                    ->label('Batas waktu')
                    ->state(fn (ServiceRequest $record): string => $record->slaLabel())
                    ->color(fn (ServiceRequest $record): ?string => $record->melewatiSla() ? 'danger' : null)
                    ->toggleable(),
                TextColumn::make('workOrder.code')
                    ->label('Perintah kerja')
                    ->placeholder('Belum ada')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('submitted_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attachments_count')
                    ->label('Foto')
                    ->counts('attachments')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('submitted_at', 'desc')
            /*
             * Yang masih terbuka selalu di atas, lalu yang paling mendesak. Sama seperti
             * perintah kerja, urutan ini yang membuat layar terbaca sebagai antrean kerja
             * dan bukan sebagai arsip yang kebetulan diurutkan menurut tanggal.
             */
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->orderByRaw("case when status in ('diajukan','disetujui','diterima') then 0 else 1 end")
                ->orderByRaw("case priority when 'mendesak' then 0 when 'tinggi' then 1 when 'normal' then 2 else 3 end"))
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_persetujuan')
                    ->label('Menunggu persetujuan')
                    ->query(fn (Builder $query): Builder => $query->menungguPersetujuan()),
                Filter::make('menunggu_ga')
                    ->label('Menunggu tim GA')
                    ->query(fn (Builder $query): Builder => $query->menungguGa()),
                Filter::make('lewat_sla')
                    ->label('Lewat batas waktu')
                    ->query(fn (Builder $query): Builder => $query->lewatSla()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(ServiceRequest::STATUSES)
                    ->multiple(),
                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options(ServiceRequest::PRIORITIES)
                    ->multiple(),
                SelectFilter::make('service_request_category_id')
                    ->label('Jenis')
                    ->relationship('category', 'name')
                    ->searchable(),
                SelectFilter::make('location_id')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                static::setujuiAction(),
                static::tolakAction(),
                static::terimaAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (ServiceRequest $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (ServiceRequest $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada permintaan perbaikan')
            ->emptyStateDescription('Permintaan dibuat karyawan lewat tombol di kanan atas saat ada yang rusak. Setelah disetujui atasan dan diterima tim GA, permintaan berubah menjadi perintah kerja, dan pekerjaannya dicatat di sana.');
    }

    // ------------------------------------------------------------------ tindakan

    /**
     * Persetujuan atasan. Hanya kepala departemen pemohon yang melihat tombol ini,
     * ditambah pemegang izin approve, supaya sekretariat bisa menandatangani mewakili
     * atasan yang sedang di luar kota.
     */
    public static function setujuiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('setujui')
            ->label('Approve')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (ServiceRequest $record): bool => $record->status === 'diajukan' && static::bolehMenyetujui($record))
            ->modalHeading(fn (ServiceRequest $record): string => 'Approve '.$record->code)
            ->modalDescription('Setelah disetujui, permintaan ini masuk antrean tim GA dan menunggu diterima. Batas waktu penyelesaian baru mulai dihitung saat tim GA menerimanya, bukan sekarang.')
            ->modalSubmitActionLabel('Approve Request')
            ->schema([
                Textarea::make('approval_note')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan.')
                    ->helperText('Terbaca tim GA saat menerima permintaan ini.'),
            ])
            ->action(function (ServiceRequest $record, array $data): void {
                if (! $record->setujui($data['approval_note'] ?? null)) {
                    Notification::make()
                        ->warning()
                        ->title('Statusnya sudah berubah')
                        ->body('Permintaan ini tidak lagi menunggu persetujuan. Muat ulang halamannya untuk melihat keadaan terbaru.')
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title($record->code.' disetujui')
                    ->body('Sudah masuk antrean tim GA.')
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
            ->visible(fn (ServiceRequest $record): bool => in_array($record->status, ['diajukan', 'disetujui'], true)
                && (static::bolehMenyetujui($record) || static::allows('accept')))
            ->modalHeading(fn (ServiceRequest $record): string => 'Reject '.$record->code)
            ->modalDescription('Permintaan yang ditolak tetap tersimpan beserta alasannya, dan pemohon bisa membacanya. Kalau masalahnya belum selesai, pemohon perlu membuat permintaan baru.')
            ->modalSubmitActionLabel('Reject Request')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan ditolak')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: perbaikan ini sudah masuk pekerjaan renovasi lantai 3 bulan depan.')
                    ->helperText('Dibaca pemohon. Alasan yang jelas mencegah permintaan yang sama diajukan lagi minggu depan.'),
            ])
            ->action(function (ServiceRequest $record, array $data): void {
                if (! $record->tolak($data['rejection_reason'])) {
                    Notification::make()
                        ->warning()
                        ->title('Statusnya sudah berubah')
                        ->body('Permintaan ini tidak lagi bisa ditolak. Muat ulang halamannya untuk melihat keadaan terbaru.')
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title($record->code.' ditolak')
                    ->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Tim GA menerima permintaan, dan satu perintah kerja korektif lahir untuk
     * mengerjakannya. Penugasan boleh dikosongkan di sini, karena siapa yang
     * mengerjakan sering baru diketahui setelah teknisi melihat kerusakannya.
     */
    public static function terimaAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('terima')
            ->label('Accept')
            ->icon('heroicon-o-inbox-arrow-down')
            ->color('primary')
            ->visible(fn (ServiceRequest $record): bool => $record->status === 'disetujui' && static::allows('accept'))
            ->modalHeading(fn (ServiceRequest $record): string => 'Accept '.$record->code)
            ->modalDescription(fn (ServiceRequest $record): string => 'Satu perintah kerja korektif akan dibuat, dan pekerjaannya dicatat di sana. '
                .($record->category?->sla_hours
                    ? 'Batas waktu '.$record->category->slaLabel().' mulai dihitung sekarang.'
                    : 'Jenis permintaan ini belum punya target waktu, jadi tidak ada batas waktu yang dihitung.'))
            ->modalSubmitActionLabel('Accept and Create Work Order')
            ->schema([
                DatePicker::make('scheduled_date')
                    ->label('Rencana dikerjakan')
                    ->displayFormat('d M Y')
                    ->placeholder('Belum dijadwalkan')
                    ->helperText('Boleh dikosongkan dan diisi nanti di perintah kerjanya.'),
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
                    ->options(fn (): array => Employee::query()
                        ->where('is_active', true)
                        ->orderBy('full_name')
                        ->pluck('full_name', 'id')
                        ->all())
                    ->searchable()
                    ->placeholder('Belum ditugaskan'),
            ])
            ->action(function (ServiceRequest $record, array $data, $livewire): void {
                $wo = $record->terima($data);

                if ($wo === null) {
                    Notification::make()
                        ->warning()
                        ->title('Statusnya sudah berubah')
                        ->body('Permintaan ini tidak lagi menunggu tim GA. Muat ulang halamannya untuk melihat keadaan terbaru.')
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title('Perintah kerja '.$wo->code.' dibuat')
                    ->body('Penugasan, biaya, dan hasil pekerjaan dicatat di perintah kerja itu. Permintaan ini akan ikut selesai saat pekerjaannya selesai.')
                    ->send();

                // Langsung dibawa ke perintah kerjanya, karena yang menerima permintaan
                // hampir selalu ingin menugaskan orangnya sekarang juga, bukan mencarinya
                // lagi di menu sebelah.
                $livewire->redirect(WorkOrderResource::getUrl('edit', ['record' => $wo]));
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /** Pembatalan oleh pemohon sendiri, saat masalahnya ternyata sudah selesai. */
    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Cancel')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (ServiceRequest $record): bool => in_array($record->status, ['diajukan', 'disetujui'], true)
                && static::bolehMembatalkan($record))
            ->requiresConfirmation()
            ->modalHeading(fn (ServiceRequest $record): string => 'Cancel '.$record->code)
            ->modalDescription('Permintaan yang dibatalkan tetap tersimpan sebagai catatan, tetapi tidak lagi masuk antrean tim GA.')
            ->modalSubmitActionLabel('Cancel Request')
            ->action(function (ServiceRequest $record): void {
                if (! $record->batalkan()) {
                    Notification::make()
                        ->warning()
                        ->title('Statusnya sudah berubah')
                        ->body('Permintaan ini tidak lagi bisa dibatalkan. Muat ulang halamannya untuk melihat keadaan terbaru.')
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title($record->code.' dibatalkan')
                    ->send();
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    // ------------------------------------------------------------------ perizinan

    /**
     * Yang boleh menyetujui: kepala departemen pemohon, atau pemegang izin approve.
     * Kepala departemen tidak butuh izin khusus, karena jabatannya sudah tercatat di
     * data departemen dan menuntut ia mengurus izin lagi hanya akan membuat tiket
     * tersangkut sampai admin sempat membukanya.
     */
    protected static function bolehMenyetujui(ServiceRequest $record): bool
    {
        if (static::allows('approve')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null
            && (int) $record->approver_employee_id === (int) $karyawan->getKey();
    }

    /** Pemohon boleh membatalkan miliknya sendiri. Tim GA boleh membatalkan yang mana pun. */
    protected static function bolehMembatalkan(ServiceRequest $record): bool
    {
        if (static::allows('accept')) {
            return true;
        }

        $karyawan = Auth::user()?->employee;

        return $karyawan !== null
            && (int) $record->requester_employee_id === (int) $karyawan->getKey();
    }

    /**
     * Isi permintaan hanya bisa diubah selama belum diterima tim GA. Setelah diterima,
     * pekerjaannya sudah berjalan menurut apa yang tertulis, dan mengubah uraiannya
     * membuat perintah kerja bercerita hal yang berbeda dari tiketnya.
     */
    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record)
            && in_array($record->status, ['diajukan', 'disetujui'], true);
    }

    /**
     * Permintaan yang sudah menjadi perintah kerja tidak boleh dihapus. Menghapusnya
     * memutus jejak siapa melapor dari pekerjaan yang sudah terlanjur dikerjakan.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && blank($record->work_order_id);
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
            'index' => ListServiceRequests::route('/'),
            'create' => CreateServiceRequest::route('/create'),
            'view' => ViewServiceRequest::route('/{record}'),
        ];
    }
}
