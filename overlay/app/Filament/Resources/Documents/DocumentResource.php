<?php

namespace App\Filament\Resources\Documents;

use App\Enums\Confidentiality;
use App\Enums\DocumentStatus;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Pages\ViewDocument;
use App\Filament\Resources\Documents\RelationManagers\VersionsRelationManager;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Services\FormMetadata;
use App\Support\Berkas;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
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
 * Daftar dan pengisian dokumen.
 *
 * Dua hal di layar ini yang tidak ada di modul GAIS lain.
 *
 * Pertama, formulirnya berubah mengikuti jenis dokumen yang dipilih. Field yang
 * ditanyakan bukan ditulis di berkas ini, melainkan dibaca dari skema metadata
 * jenis itu. Menambah jenis dokumen baru karena itu tidak pernah menyentuh kode
 * layar ini.
 *
 * Kedua, daftarnya disaring dua lapis sekaligus sebelum sampai ke layar:
 * klasifikasi kerahasiaan dibanding tingkat kewenangan pembacanya, lalu cakupan
 * data dibanding departemennya. Penyaringan itu dikerjakan sebagai query, bukan
 * sebagai penyembunyian baris setelah diambil, supaya penghitung jumlah hasil
 * dan halaman berikutnya ikut benar. Menyaring setelah paginasi adalah cara
 * paling umum membuat halaman kedua kosong tanpa ada yang tahu sebabnya.
 */
class DocumentResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Document::class;

    protected static string $moduleCode = 'documents';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Documents';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?string $modelLabel = 'document';

    protected static ?string $pluralModelLabel = 'documents';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document')
                ->columns(2)
                ->schema([
                    Select::make('document_type_id')
                        ->label('Jenis dokumen')
                        ->options(fn (): array => DocumentType::query()
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->all())
                        ->required()
                        ->searchable()
                        ->live()
                        /*
                         * Dikunci setelah dokumennya ada. Mengganti jenis berarti
                         * mengganti seluruh arti kolom metadatanya, dan isian yang
                         * sudah ada akan menggantung tanpa definisi. Kalau memang
                         * salah jenis, dokumennya dibuat ulang.
                         */
                        ->disabled(fn (?Document $record): bool => $record !== null)
                        ->dehydrated(fn (?Document $record): bool => $record === null)
                        ->helperText(fn (?Document $record): string => $record !== null
                            ? 'Jenis tidak bisa diganti setelah dokumen tersimpan.'
                            : 'Menentukan field apa saja yang ditanyakan di bawah, dan perlu tidaknya dokumen ini disahkan.')
                        /*
                         * Menyiapkan kunci metadata kosong begitu jenis dipilih.
                         *
                         * Filament baru membuat cabang metadata di state ketika
                         * salah satu fieldnya terisi, sedangkan komponen Alpine di
                         * dalamnya, yaitu pemilih tanggal dan pilihan bercari,
                         * menautkan diri ke jalur itu saat digambar. Tanpa benih
                         * ini, setiap field menghasilkan satu galat penautan di
                         * console, dan tautan yang gagal tidak akan pulih sendiri
                         * ketika nilainya kemudian diisi.
                         */
                        ->afterStateUpdated(function ($state, callable $set): void {
                            $jenis = DocumentType::query()->find($state);

                            $benih = [];

                            foreach (array_keys($jenis?->metadata_schema ?? []) as $field) {
                                $benih[$field] = null;
                            }

                            $set('metadata', $benih);
                        }),
                    Select::make('category_id')
                        ->label('Kategori')
                        ->options(fn (): array => DocumentCategory::query()
                            ->where('is_active', true)
                            ->with('parent')
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (DocumentCategory $k): array => [$k->id => $k->namaLengkap()])
                            ->all())
                        ->searchable()
                        ->placeholder('Belum ditentukan')
                        ->helperText('Tempat menaruh, bukan jenis dokumennya. Boleh dikosongkan dulu.'),
                    TextInput::make('title')
                        ->label('Judul dokumen')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull(),
                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(2)
                        ->columnSpanFull(),
                    Select::make('confidentiality')
                        ->label('Klasifikasi')
                        ->options(Confidentiality::opsi())
                        ->default(Confidentiality::Internal->value)
                        ->required()
                        ->live()
                        ->helperText(fn (?string $state): string => Confidentiality::tryFrom((string) $state)?->keterangan()
                            ?? 'Menentukan siapa yang boleh melihat dokumen ini, terpisah dari izin modul.'),
                    Select::make('owner_department_id')
                        ->label('Departemen pemilik')
                        ->relationship('ownerDepartment', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Ikut departemen pengunggah')
                        ->helperText('Dipakai membatasi siapa yang melihat dokumen ini kalau perannya bercakupan departemen.'),
                ]),

            Section::make('File')
                ->columnSpanFull()
                ->description('Berkas versi pertama. Berkasnya disimpan di penyimpanan tertutup, dan hanya bisa diunduh lewat tombol di aplikasi ini.')
                // Hanya saat membuat. Versi berikutnya ditambahkan lewat tombol
                // tersendiri, bukan dengan menimpa berkas yang sudah disahkan.
                ->visible(fn (?Document $record): bool => $record === null)
                ->schema([
                    FileUpload::make('berkas')
                        ->hiddenLabel()
                        ->required()
                        ->disk(Berkas::DISK)
                        /*
                         * storeFiles(false) membuat berkasnya tetap berupa objek
                         * unggahan sementara, bukan jalur yang sudah tersimpan.
                         * Itu yang dibutuhkan: yang menentukan letak, nama, dan
                         * sidik jari berkas adalah lapisan layanan, bukan form,
                         * supaya berkas yang masuk lewat layar dan lewat kode
                         * tersimpan dengan aturan yang sama persis.
                         */
                        ->storeFiles(false)
                        ->maxSize(51200)
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg', 'image/png', 'image/webp',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->helperText('PDF, gambar, Word, atau Excel. Maksimal 50 MB.'),
                ]),

            Section::make('Metadata')
                ->columnSpanFull()
                ->columns(2)
                /*
                 * Jalur penyimpanannya dipegang di sini, bukan di nama tiap
                 * field. Inilah yang membuat Filament menyiapkan larik metadata
                 * di state sejak awal, sehingga komponen Alpine di dalamnya
                 * punya tempat untuk menautkan diri.
                 */
                ->statePath('metadata')
                ->description(fn ($get): string => filled($get('document_type_id'))
                    ? 'Field di bawah ditentukan oleh jenis dokumen yang dipilih.'
                    : 'Pilih jenis dokumen lebih dulu, dan field yang perlu diisi akan muncul di sini.')
                /*
                 * Isi seksi ini dibangun dari data, bukan ditulis di sini.
                 * Closure-nya dijalankan ulang setiap kali jenis dokumen berganti,
                 * karena pilihannya bertanda live().
                 */
                ->schema(function ($get): array {
                    $jenis = DocumentType::query()->find($get('document_type_id'));

                    if ($jenis === null || blank($jenis->metadata_schema)) {
                        return [];
                    }

                    return app(FormMetadata::class)->komponen($jenis->metadata_schema);
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('Nomor')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Tanpa nomor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->description(fn (Document $record): string => trim(
                        ($record->type?->name ?? 'Tanpa jenis')
                        .($record->category ? ' · '.$record->category->namaLengkap() : ''),
                    ))
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('confidentiality')
                    ->label('Klasifikasi')
                    ->badge()
                    ->color(fn (Document $record): string => $record->confidentiality->warna())
                    ->formatStateUsing(fn (Document $record): string => $record->confidentiality->label()),
                TextColumn::make('current_version_id')
                    ->label('Versi berlaku')
                    ->state(fn (Document $record): string => $record->currentVersion === null
                        ? 'Belum ada'
                        : 'v'.$record->currentVersion->version_number)
                    ->description(fn (Document $record): ?string => $record->currentVersion?->effective_from?->translatedFormat('d M Y'))
                    ->badge()
                    ->color(fn (Document $record): string => $record->currentVersion === null ? 'warning' : 'success'),
                TextColumn::make('ownerDepartment.name')
                    ->label('Departemen')
                    ->placeholder('Tidak ditentukan')
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Document $record): string => $record->status->warna())
                    ->formatStateUsing(fn (Document $record): string => $record->status->label())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label('Diunggah oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn (Document $record): string => ViewDocument::getUrl([$record]))
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('document_type_id')
                    ->label('Jenis dokumen')
                    ->relationship('type', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua'),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua'),
                SelectFilter::make('confidentiality')
                    ->label('Klasifikasi')
                    ->options(Confidentiality::opsi())
                    ->placeholder('Semua'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(DocumentStatus::opsi())
                    ->default(DocumentStatus::Aktif->value)
                    ->placeholder('Semua'),
                /*
                 * Penyaring metadata, dibangun dari skema jenis yang dipilih.
                 *
                 * Hanya field bertanda filterable yang muncul, dan tidak satu pun
                 * ditulis di berkas ini. Menambah field yang bisa disaring cukup
                 * dilakukan di layar Document Types.
                 */
                Filter::make('metadata')
                    ->schema([
                        Select::make('jenis')
                            ->label('Jenis dokumen')
                            ->options(fn (): array => DocumentType::query()
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set): void {
                                $jenis = DocumentType::query()->find($state);

                                $benih = [];

                                foreach ($jenis?->metadata_schema ?? [] as $field => $definisi) {
                                    if (($definisi['filterable'] ?? false) === true) {
                                        $benih[$field] = null;
                                    }
                                }

                                $set('metadata', $benih);
                            })
                            ->helperText('Pilih jenis lebih dulu untuk memunculkan penyaring metadatanya.'),
                        // Group, bukan Section: di dalam dropdown penyaring, Section
                        // menggambar kartu bergaris yang membuat isinya terasa
                        // seperti halaman tersendiri. Group hanya mengelompokkan.
                        Group::make()
                            ->columns(2)
                            ->statePath('metadata')
                            ->schema(function ($get): array {
                                $jenis = DocumentType::query()->find($get('jenis'));

                                if ($jenis === null) {
                                    return [];
                                }

                                return app(FormMetadata::class)->komponenFilter($jenis->metadata_schema ?? []);
                            }),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['jenis'] ?? null)) {
                            $query->where('document_type_id', $data['jenis']);
                        }

                        foreach ($data['metadata'] ?? [] as $field => $nilai) {
                            if ($nilai === null || $nilai === '' || $nilai === []) {
                                continue;
                            }

                            $query->metadata($field, $nilai);
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $tanda = [];

                        foreach ($data['metadata'] ?? [] as $field => $nilai) {
                            if ($nilai === null || $nilai === '' || $nilai === []) {
                                continue;
                            }

                            $tanda[] = str_replace('_', ' ', $field).': '.(is_bool($nilai) ? ($nilai ? 'ya' : 'tidak') : $nilai);
                        }

                        return $tanda;
                    }),
            ])
            ->recordActions([
                Action::make('unduh')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->color('gray')
                    ->url(fn (Document $record): ?string => $record->currentVersion === null
                        ? null
                        : route('gais.dokumen.unduh', [$record, $record->currentVersion]))
                    ->openUrlInNewTab()
                    /*
                     * Tombolnya hanya muncul kalau ada yang bisa diunduh dan
                     * pemakainya memang boleh mengunduh. Izin download memang
                     * dipisah dari read, jadi daftar ini bisa dibuka lebih banyak
                     * orang daripada berkasnya.
                     */
                    ->visible(fn (Document $record): bool => $record->currentVersion !== null
                        && $record->currentVersion->bolehDiunduh()
                        && static::allows('download')),
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada dokumen')
            ->emptyStateDescription('Tekan Add Document untuk mengunggah yang pertama. Field yang ditanyakan akan menyesuaikan jenis dokumen yang Anda pilih, dan berkasnya disimpan di penyimpanan tertutup yang hanya bisa dibuka lewat aplikasi ini.');
    }

    /**
     * Dua lapis penyaringan sebelum satu baris pun sampai ke layar: klasifikasi
     * kerahasiaan, lalu cakupan data. Dikerjakan di sini, bukan di tiap layar
     * yang memakai model ini, supaya tidak ada jalur yang terlewat.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['type', 'category', 'currentVersion', 'ownerDepartment']);

        $user = Auth::user();

        return $user === null ? $query->whereRaw('false') : $query->terlihatOleh($user);
    }

    /**
     * Penjagaan yang sama untuk satu baris, dipakai saat alamatnya dibuka
     * langsung. Query di atas menyembunyikannya dari daftar, tetapi menebak
     * nomornya di alamat adalah jalur lain yang perlu ditutup sendiri.
     */
    public static function canView(Model $record): bool
    {
        return static::allows('read') && static::dalamJangkauan($record);
    }

    public static function canEdit(Model $record): bool
    {
        return static::allows('update') && static::dalamJangkauan($record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::allows('delete') && static::dalamJangkauan($record);
    }

    private static function dalamJangkauan(Model $record): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return Document::query()->whereKey($record->getKey())->terlihatOleh($user)->exists();
    }

    /**
     * allows() bersifat protected, dan memang pantas begitu. Halaman detail
     * berada di kelas lain dan tetap perlu menanyakan hal yang sama, jadi
     * pertanyaannya dibukakan satu pintu, bukan izinnya dibuka seluruhnya.
     */
    /**
     * Riwayat versi tampil sebagai relation manager, bukan sebagai tabel di
     * dalam halaman detail, karena ia butuh tombol: menambah versi, mengesahkan,
     * menolak, dan menarik peredaran.
     */
    public static function getRelations(): array
    {
        return [
            VersionsRelationManager::class,
        ];
    }

    public static function bolehUnduh(): bool
    {
        return static::allows('download');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocuments::route('/'),
            'create' => CreateDocument::route('/create'),
            'view' => ViewDocument::route('/{record}'),
            'edit' => EditDocument::route('/{record}/edit'),
        ];
    }
}
