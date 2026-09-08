<?php

namespace App\Filament\Resources\Letters;

use App\Filament\Resources\Letters\Pages\ListLetters;
use App\Models\Employee;
use App\Models\Letter;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
use UnitEnum;

/**
 * Buku agenda surat.
 *
 * Satu layar untuk dua arah. Pilihan arah ada di paling atas dan sisa formulirnya menyesuaikan
 * diri, dengan alasan yang sama seperti data induk petugas pada kiriman P: yang membedakan
 * keduanya hanya dua tiga kolom, dan memberi dua menu berbeda berarti memaksa orang memilih
 * menu sebelum ia sempat memikirkan suratnya.
 */
class LetterResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = Letter::class;

    protected static string $moduleCode = 'letters';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|UnitEnum|null $navigationGroup = 'Correspondence';

    protected static ?string $navigationLabel = 'Letters';

    protected static ?string $modelLabel = 'letter';

    protected static ?string $pluralModelLabel = 'letters';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Nomor agendanya dibuat sendiri oleh aplikasi. Nomor yang Anda ketik di bawah adalah nomor yang benar benar tertulis di kertas suratnya.')
                ->columns(2)
                ->schema([
                    Select::make('direction')
                        ->label('Arah surat')
                        ->options(Letter::DIRECTIONS)
                        ->default('masuk')
                        ->required()
                        ->live()
                        ->disabled(fn (?Letter $record): bool => $record !== null)
                        ->dehydrated()
                        ->helperText(fn (?Letter $record): string => $record !== null
                            ? 'Arah surat tidak bisa diubah setelah nomor agendanya terbit, karena nomor agenda masuk dan keluar berasal dari dua urutan yang terpisah.'
                            : 'Menentukan dari urutan mana nomor agendanya diambil, dan kolom mana saja yang perlu diisi.'),

                    Select::make('category')
                        ->label('Jenis surat')
                        ->options(Letter::CATEGORIES)
                        ->default('lainnya')
                        ->required(),

                    TextInput::make('counterparty')
                        ->label(fn ($get): string => $get('direction') === 'masuk' ? 'Pengirim' : 'Tujuan surat')
                        ->required()
                        ->maxLength(200)
                        ->placeholder(fn ($get): string => $get('direction') === 'masuk'
                            ? 'Nama orang atau lembaga yang mengirim'
                            : 'Nama orang atau lembaga yang dituju'),

                    TextInput::make('letter_number')
                        ->label(fn ($get): string => $get('direction') === 'masuk'
                            ? 'Nomor surat pengirim'
                            : 'Nomor surat perusahaan')
                        ->maxLength(100)
                        ->required(fn ($get): bool => $get('direction') === 'keluar')
                        ->placeholder(fn ($get): string => $get('direction') === 'masuk'
                            ? 'Kosongkan kalau suratnya tidak bernomor'
                            : 'Ketik mengikuti format perusahaan')
                        /*
                         * Keunikan hanya berlaku pada surat keluar. Dua surat perusahaan
                         * bernomor sama adalah kesalahan arsip, sedangkan dua pengirim yang
                         * berbeda bebas memakai nomor yang kebetulan sama.
                         */
                        ->rule(fn (?Letter $record, $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record, $get): void {
                            if ($get('direction') !== 'keluar' || blank($value)) {
                                return;
                            }

                            $kembar = Letter::query()
                                ->where('direction', 'keluar')
                                ->where('letter_number', $value)
                                ->when($record, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
                                ->first();

                            if ($kembar !== null) {
                                $fail('Nomor surat ini sudah dipakai pada agenda '.$kembar->code
                                    .', perihal '.$kembar->subject.'.');
                            }
                        })
                        ->helperText(fn ($get): ?string => $get('direction') === 'keluar'
                            ? 'Format nomornya milik perusahaan, jadi aplikasi ini tidak mengubahnya. Yang dijaga hanya supaya tidak ada dua surat keluar bernomor sama.'
                            : null),

                    DatePicker::make('letter_date')
                        ->label('Tanggal surat')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->required()
                        ->helperText('Tanggal yang tertulis di kertas suratnya.'),

                    DatePicker::make('logged_date')
                        ->label(fn ($get): string => $get('direction') === 'masuk' ? 'Tanggal diterima' : 'Tanggal dikirim')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required()
                        ->helperText('Saat surat ini melewati meja GA, yang sering berbeda dari tanggal suratnya.'),

                    TextInput::make('subject')
                        ->label('Perihal')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('assigned_employee_id')
                        ->label('Ditujukan kepada')
                        ->options(fn (): array => static::pilihanKaryawan())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get): bool => $get('direction') === 'masuk')
                        ->placeholder('Belum ditentukan')
                        ->helperText('Boleh dikosongkan dulu kalau tujuannya belum jelas, dan diisi setelah suratnya dibaca.'),

                    Select::make('signer_employee_id')
                        ->label('Ditandatangani')
                        ->options(fn (): array => static::pilihanKaryawan())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get): bool => $get('direction') === 'keluar')
                        ->placeholder('Tidak dicatat'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(1000)
                        ->placeholder('Ringkasan isi, atau hal yang perlu diingat saat surat ini dicari lagi')
                        ->columnSpanFull(),

                    FileUpload::make('file_path')
                        ->label('Pindaian surat')
                        ->disk('public')
                        ->directory('surat')
                        ->visibility('public')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(10240)
                        ->openable()
                        ->downloadable()
                        ->storeFileNamesIn('original_name')
                        ->helperText('PDF atau foto, maksimum 10 MB. Surat yang sudah dipindai tidak perlu dicari lagi di lemari arsip.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected static function pilihanKaryawan(): array
    {
        return Employee::query()
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get()
            ->mapWithKeys(fn (Employee $karyawan) => [
                $karyawan->id => $karyawan->full_name.' ('.$karyawan->nip.')',
            ])
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor agenda')
                    ->fontFamily('mono')
                    ->description(fn (Letter $record): ?string => $record->letter_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('direction')
                    ->label('Arah')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => Letter::DIRECTIONS[$state] ?? '-')
                    ->sortable(),
                TextColumn::make('subject')
                    ->label('Perihal')
                    ->description(fn (Letter $record): string => $record->counterpartyLabel())
                    ->searchable()
                    ->wrap()
                    ->limit(70),
                TextColumn::make('logged_date')
                    ->label('Diterima atau dikirim')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('keadaan')
                    ->label('Keadaan')
                    ->badge()
                    ->state(fn (Letter $record): string => $record->keadaanLabel())
                    ->color(fn (Letter $record): string => $record->keadaanColor()),
                TextColumn::make('tujuan')
                    ->label('Untuk siapa')
                    ->state(fn (Letter $record): ?string => $record->isMasuk()
                        ? $record->assignedEmployee?->full_name
                        : $record->signer?->full_name)
                    /*
                     * Hasil serah terimanya ikut terbaca di sini.
                     *
                     * Sebelumnya kalimat itu hanya muncul sekali di pemberitahuan setelah
                     * tombol Record Handover ditekan, lalu hilang bersama pemberitahuannya.
                     * Karena surat ini tidak punya halaman Lihat, siapa yang benar benar
                     * menerima surat tersimpan di basis data tetapi tidak bisa dibaca siapa
                     * pun lagi, padahal itu justru keterangan yang dicari saat sebuah surat
                     * dinyatakan tidak pernah sampai. Ketahuan saat kiriman T diverifikasi.
                     */
                    ->description(function (Letter $record): string {
                        if ($record->isKeluar()) {
                            return 'Penanda tangan';
                        }

                        return $record->sudahDiserahkan()
                            ? $record->serahTerimaKalimat()
                            : 'Tujuan surat';
                    })
                    ->wrap()
                    ->placeholder('Belum ditentukan'),
                TextColumn::make('category')
                    ->label('Jenis')
                    ->formatStateUsing(fn (?string $state): string => Letter::CATEGORIES[$state] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('letter_date')
                    ->label('Tanggal surat')
                    ->date('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dicatat oleh')
                    ->placeholder('Tidak tercatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('logged_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'assignedEmployee', 'handedOverTo', 'signer', 'createdByUser',
            ]))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('direction')
                    ->label('Arah surat')
                    ->options(Letter::DIRECTIONS),
                SelectFilter::make('category')
                    ->label('Jenis surat')
                    ->options(Letter::CATEGORIES),
                Filter::make('menunggu_diserahkan')
                    ->label('Surat masuk yang belum sampai')
                    ->query(fn (Builder $query): Builder => $query->menungguDiserahkan()),
            ])
            ->recordActions([
                static::serahkanAction(),
                static::bukaPindaianAction(),
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->modalDescription('Nomor agendanya ikut hilang dan tidak dipakai ulang, jadi urutan agenda akan berlubang di nomor itu.'),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada surat yang cocok'
                : 'Buku agenda masih kosong')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada surat yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh surat lagi.'
                : 'Catat surat masuk begitu diterima dan surat keluar begitu dikirim. Nomor agendanya dibuat sendiri, dan pindaiannya membuat surat lama tidak perlu dicari di lemari arsip.');
    }

    /**
     * Mencatat serah terima surat masuk.
     *
     * Yang dicatat adalah siapa yang benar benar menerima, bukan siapa yang dituju. Surat
     * yang dititipkan ke rekan semeja adalah kejadian sehari hari, dan justru keterangan itu
     * yang paling dicari saat sebuah surat dinyatakan tidak pernah sampai.
     */
    public static function serahkanAction(): Action
    {
        return Action::make('serahkan')
            ->label('Record Handover')
            ->icon('heroicon-o-hand-raised')
            ->iconButton()
            ->color('primary')
            ->visible(fn (Letter $record): bool => $record->isMasuk()
                && ! $record->sudahDiserahkan()
                && static::allows('update'))
            ->modalHeading(fn (Letter $record): string => 'Handover of '.$record->code)
            ->modalDescription(fn (Letter $record): string => $record->assigned_employee_id === null
                ? 'Surat ini belum ditentukan untuk siapa. Isi dulu kolom Ditujukan kepada lewat Ubah, supaya jelas siapa yang seharusnya menerimanya.'
                : 'Ditujukan kepada '.($record->assignedEmployee?->full_name ?? 'orang yang dituju')
                    .'. Catat siapa yang benar benar menerimanya, karena keduanya tidak selalu orang yang sama.')
            ->modalSubmitActionLabel('Save Handover')
            ->fillForm(fn (Letter $record): array => [
                'handed_over_to_employee_id' => $record->assigned_employee_id,
                'handed_over_at' => now(),
            ])
            ->schema([
                Select::make('handed_over_to_employee_id')
                    ->label('Diterima oleh')
                    ->options(fn (): array => static::pilihanKaryawan())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Sudah terisi nama orang yang dituju. Ganti kalau yang menerima ternyata orang lain.'),
                DateTimePicker::make('handed_over_at')
                    ->label('Waktu diterima')
                    ->native(false)
                    ->seconds(false)
                    ->displayFormat('d M Y, H:i')
                    ->required()
                    ->maxDate(now()),
                Textarea::make('handover_note')
                    ->label('Catatan serah terima')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Misalnya dititipkan karena yang dituju sedang keluar kota'),
            ])
            ->action(function (Letter $record, array $data, Action $action): void {
                if ($record->assigned_employee_id === null) {
                    Notification::make()
                        ->warning()
                        ->title('Belum bisa diserahkan')
                        ->body('Surat ini belum ditentukan untuk siapa, jadi tidak ada yang bisa dibandingkan dengan penerimanya.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                $record->forceFill([
                    'handed_over_to_employee_id' => $data['handed_over_to_employee_id'],
                    'handed_over_at' => $data['handed_over_at'],
                    'handover_note' => $data['handover_note'] ?? null,
                ])->save();

                // Relasi penerimanya dimuat ulang sebelum kalimatnya disusun, karena
                // menyimpan kolomnya tidak membuat Eloquent membaca ulang relasinya.
                // Cacat yang sama pernah muncul pada pencatatan kehadiran di kiriman Q.
                $record->load(['assignedEmployee', 'handedOverTo']);

                Notification::make()
                    ->success()
                    ->title('Serah terima tercatat')
                    ->body($record->serahTerimaKalimat())
                    ->send();
            });
    }

    public static function bukaPindaianAction(): Action
    {
        return Action::make('buka_pindaian')
            ->label('Open Scan')
            ->icon('heroicon-o-document-magnifying-glass')
            ->iconButton()
            ->color('gray')
            ->visible(fn (Letter $record): bool => filled($record->file_path))
            ->url(fn (Letter $record): ?string => $record->pindaianUrl())
            ->openUrlInNewTab();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLetters::route('/'),
        ];
    }
}
