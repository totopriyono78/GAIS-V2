<?php

namespace App\Filament\Resources\DocumentTypes;

use App\Enums\Lifecycle;
use App\Enums\MetadataFieldType;
use App\Filament\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Models\DocumentType;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Layar tempat bentuk seluruh modul dokumen ditentukan.
 *
 * Satu baris di sini menentukan form unggah dokumen jenis itu akan menanyakan
 * apa saja, isiannya divalidasi bagaimana, dan daftar dokumennya bisa disaring
 * dengan apa. Karena itu menambah jenis dokumen baru tidak perlu menulis kode.
 *
 * Konsekuensinya juga perlu disadari: mengubah skema metadata sebuah jenis
 * mengubah arti kolom metadata pada seluruh dokumen yang sudah memakainya.
 * Menghapus field tidak menghapus isinya di dokumen lama, tetapi isinya tidak
 * lagi punya definisi, sehingga tidak lagi muncul di layar mana pun.
 */
class DocumentTypeResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = DocumentType::class;

    protected static string $moduleCode = 'document_types';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Documents';

    protected static ?string $navigationLabel = 'Document Types';

    protected static ?string $modelLabel = 'document type';

    protected static ?string $pluralModelLabel = 'document types';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Type')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode jenis')
                        ->required()
                        ->maxLength(40)
                        ->regex('/^[a-z][a-z0-9_]*$/')
                        ->unique(ignoreRecord: true)
                        // Kode tersimpan di tiap dokumen lewat foreign key, dan dipakai
                        // modul lain untuk menitipkan lampiran. Dikunci setelah dibuat.
                        ->disabled(fn (?DocumentType $record): bool => $record !== null)
                        ->dehydrated(fn (?DocumentType $record): bool => $record === null)
                        ->helperText('Huruf kecil dan garis bawah. Contoh: contract_vendor')
                        ->placeholder('contract_vendor'),
                    TextInput::make('name')
                        ->label('Nama jenis')
                        ->required()
                        ->maxLength(120),
                    Select::make('lifecycle')
                        ->label('Siklus hidup')
                        ->options(Lifecycle::opsi())
                        ->default(Lifecycle::Controlled->value)
                        ->required()
                        ->live()
                        ->helperText(fn (?string $state): string => Lifecycle::tryFrom((string) $state)?->keterangan()
                            ?? 'Menentukan cara dokumen jenis ini diperlakukan sepanjang hidupnya.'),
                    TextInput::make('number_prefix')
                        ->label('Awalan nomor dokumen')
                        ->maxLength(10)
                        ->regex('/^[A-Z][A-Z0-9]*$/')
                        ->placeholder('SOP')
                        // Lampiran tidak diberi nomor sendiri karena identitasnya
                        // mengikuti transaksi induknya.
                        ->disabled(fn ($get): bool => $get('lifecycle') === Lifecycle::Attachment->value)
                        ->dehydrateStateUsing(fn (?string $state, $get): ?string => $get('lifecycle') === Lifecycle::Attachment->value
                            ? null
                            : ($state ?: null))
                        ->helperText(fn ($get): string => $get('lifecycle') === Lifecycle::Attachment->value
                            ? 'Lampiran tidak diberi nomor sendiri, identitasnya ikut catatan induknya.'
                            : 'Huruf besar. Nomornya nanti berbentuk SOP/GA/'.date('Y').'/0001.'),
                    TextInput::make('retention_years')
                        ->label('Masa simpan (tahun)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(100)
                        ->placeholder('Permanen')
                        ->helperText('Dikosongkan berarti disimpan permanen, atau ikut umur catatan induknya untuk lampiran. Angkanya perlu dikonfirmasi bagian legal, sebagian jenis dokumen diatur peraturan.'),
                    TextInput::make('sort_order')
                        ->label('Urutan tampil')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),
                ]),

            Section::make('Behaviour')
                ->description('Tiga saklar ini menentukan layar mana yang muncul untuk dokumen jenis ini. Mengubahnya setelah ada dokumen yang memakai jenis ini hanya mempengaruhi yang berikutnya, bukan yang sudah tersimpan.')
                ->columns(3)
                ->schema([
                    Toggle::make('is_versioned')
                        ->label('Punya riwayat versi')
                        ->default(true)
                        ->helperText('Dimatikan berarti setiap perubahan diunggah sebagai dokumen baru, bukan versi baru.'),
                    Toggle::make('needs_approval')
                        ->label('Perlu pengesahan')
                        ->helperText('Versi baru belum berlaku sebelum disahkan. Alur pengesahannya sendiri dibangun di tahap berikutnya.'),
                    Toggle::make('has_validity')
                        ->label('Punya masa berlaku')
                        ->helperText('Menyalakan kolom mulai dan berakhir, dan nanti menjadi dasar pengingat jatuh tempo.'),
                    Toggle::make('is_active')
                        ->label('Jenis dipakai')
                        ->default(true)
                        ->helperText('Dimatikan berarti jenis ini tidak bisa dipilih lagi saat mengunggah dokumen baru, tetapi dokumen yang sudah memakainya tidak terganggu.')
                        ->columnSpanFull(),
                ]),

            Section::make('Metadata Schema')
                ->columnSpanFull()
                ->description('Field di bawah inilah yang akan ditanyakan form unggah untuk jenis dokumen ini, dan yang bisa dipakai menyaring daftarnya. Satu definisi dipakai untuk tiga hal sekaligus: membangun form, memvalidasi isian, dan merender filter.')
                ->schema([
                    Repeater::make('metadata_schema')
                        ->hiddenLabel()
                        ->addActionLabel('Add Field')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => filled($state['label'] ?? null)
                            ? $state['label'].' ('.(MetadataFieldType::tryFrom($state['type'] ?? '')?->label() ?? 'belum ditentukan').')'
                            : 'Field baru')
                        ->columns(2)
                        /*
                         * Skema disimpan sebagai peta nama field ke definisinya, sedangkan
                         * Repeater bekerja dengan daftar bernomor. Dua penerjemah di bawah
                         * yang menjembatani keduanya.
                         *
                         * Keduanya sengaja dipasang di kait yang tidak biasa, dan itu perlu
                         * dijelaskan karena pilihan yang wajar justru yang salah di sini.
                         *
                         * Pertama, afterStateHydrated milik Filament menyimpan SATU closure,
                         * bukan tumpukan. Repeater memakai kait itu untuk memanggil
                         * hydrateItems(), yang memberi setiap baris kunci acak. Memasang
                         * closure sendiri di situ menimpanya, barisnya kehilangan kunci, dan
                         * tombol hapus serta ubah urutan mulai mengenai baris yang salah.
                         * Karena itu hydrateItems() dipanggil ulang di akhir closure ini.
                         *
                         * Kedua, penyusunan kembali menjadi peta TIDAK boleh ditaruh di
                         * dehydrateStateUsing. Filament menjalankan mutateDehydratedState
                         * sebagai lintasan tersendiri sesudahnya, dan bawaan Repeater di
                         * lintasan itu menjalankan array_values, yang akan membuang seluruh
                         * nama field yang baru saja disusun. Menimpa mutateDehydratedStateUsing
                         * menggantikan perilaku itu, bukan menumpuk di atasnya.
                         */
                        ->afterStateHydrated(function (Repeater $component, mixed $state): void {
                            $daftar = [];

                            foreach ((array) $state as $nama => $definisi) {
                                if (! is_array($definisi)) {
                                    continue;
                                }

                                $daftar[] = [
                                    'nama' => is_string($nama) ? $nama : ($definisi['nama'] ?? ''),
                                    'label' => $definisi['label'] ?? '',
                                    'type' => $definisi['type'] ?? MetadataFieldType::Teks->value,
                                    'options' => $definisi['options'] ?? [],
                                    'reference' => $definisi['reference'] ?? null,
                                    'required' => (bool) ($definisi['required'] ?? false),
                                    'searchable' => (bool) ($definisi['searchable'] ?? false),
                                    'filterable' => (bool) ($definisi['filterable'] ?? false),
                                    'help' => $definisi['help'] ?? null,
                                ];
                            }

                            $component->rawState($daftar);
                            $component->hydrateItems();
                        })
                        ->mutateDehydratedStateUsing(function (mixed $state): array {
                            $peta = [];

                            foreach ((array) $state as $baris) {
                                if (! is_array($baris)) {
                                    continue;
                                }

                                $nama = trim((string) ($baris['nama'] ?? ''));

                                if ($nama === '') {
                                    continue;
                                }

                                $tipe = $baris['type'] ?? MetadataFieldType::Teks->value;

                                $definisi = [
                                    'type' => $tipe,
                                    'label' => (string) ($baris['label'] ?: $nama),
                                ];

                                foreach (['required', 'searchable', 'filterable'] as $saklar) {
                                    if (! empty($baris[$saklar])) {
                                        $definisi[$saklar] = true;
                                    }
                                }

                                if (filled($baris['help'] ?? null)) {
                                    $definisi['help'] = (string) $baris['help'];
                                }

                                // Opsi dan tabel rujukan hanya disimpan untuk tipe yang
                                // memakainya, supaya sisa isian dari tipe yang sempat
                                // dipilih lalu diganti tidak ikut tersimpan diam diam.
                                if ($tipe === MetadataFieldType::Pilihan->value) {
                                    $definisi['options'] = array_values(array_filter((array) ($baris['options'] ?? [])));
                                }

                                if ($tipe === MetadataFieldType::Relasi->value && filled($baris['reference'] ?? null)) {
                                    $definisi['reference'] = (string) $baris['reference'];
                                }

                                $peta[$nama] = $definisi;
                            }

                            return $peta;
                        })
                        ->schema([
                            TextInput::make('nama')
                                ->label('Nama field')
                                ->required()
                                ->maxLength(40)
                                ->regex('/^[a-z][a-z0-9_]*$/')
                                ->helperText('Huruf kecil dan garis bawah. Dipakai sebagai kunci penyimpanan, bukan yang dibaca orang.')
                                ->placeholder('nomor_kontrak'),
                            TextInput::make('label')
                                ->label('Label di layar')
                                ->required()
                                ->maxLength(80)
                                ->placeholder('Nomor kontrak'),
                            Select::make('type')
                                ->label('Tipe isian')
                                ->options(MetadataFieldType::opsi())
                                ->default(MetadataFieldType::Teks->value)
                                ->required()
                                ->live(),
                            Select::make('reference')
                                ->label('Merujuk tabel')
                                ->options(DocumentType::TABEL_RELASI)
                                ->required()
                                ->visible(fn ($get): bool => $get('type') === MetadataFieldType::Relasi->value)
                                ->helperText('Daftarnya tertutup. Tabel yang belum ada di sini perlu ditambahkan lebih dulu di kode.'),
                            TagsInput::make('options')
                                ->label('Daftar pilihan')
                                ->required()
                                ->visible(fn ($get): bool => $get('type') === MetadataFieldType::Pilihan->value)
                                ->helperText('Ketik satu pilihan lalu tekan Enter. Urutannya ikut yang Anda ketik.')
                                ->columnSpanFull(),
                            Toggle::make('required')
                                ->label('Wajib diisi'),
                            Toggle::make('searchable')
                                ->label('Ikut dicari')
                                ->helperText('Isinya ikut terbaca saat mencari dokumen.'),
                            Toggle::make('filterable')
                                ->label('Bisa jadi filter')
                                ->helperText('Muncul sebagai penyaring di daftar dokumen.'),
                            TextInput::make('help')
                                ->label('Teks bantuan')
                                ->maxLength(160)
                                ->placeholder('Opsional')
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Jenis dokumen')
                    ->description(fn (DocumentType $record): string => $record->sifatRingkas())
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lifecycle')
                    ->label('Siklus hidup')
                    ->badge()
                    ->color(fn (DocumentType $record): string => match ($record->lifecycle) {
                        Lifecycle::Controlled => 'primary',
                        Lifecycle::TermBased => 'warning',
                        Lifecycle::Attachment => 'gray',
                    })
                    ->formatStateUsing(fn (DocumentType $record): string => $record->lifecycle->label()),
                TextColumn::make('retention_years')
                    ->label('Masa simpan')
                    /*
                     * state(), bukan formatStateUsing().
                     *
                     * Masa simpan yang kosong berarti permanen, atau ikut umur
                     * catatan induknya, dan itu justru keterangan yang paling
                     * perlu terbaca. Filament melewati formatStateUsing ketika
                     * nilainya kosong dan langsung menggambar placeholder, jadi
                     * kolomnya tampil benar benar kosong untuk Kebijakan
                     * Perusahaan dan Legalitas Perusahaan, dua jenis yang memang
                     * disimpan selamanya. state() menggantikan pengambilan
                     * nilainya, jadi ia selalu dipanggil.
                     */
                    ->state(fn (DocumentType $record): string => $record->masaSimpanTerbaca())
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('documents_count')
                    ->label('Jumlah dokumen')
                    ->counts('documents')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('number_prefix')
                    ->label('Awalan nomor')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Tanpa nomor')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('lifecycle')
                    ->label('Siklus hidup')
                    ->options(Lifecycle::opsi())
                    ->placeholder('Semua'),
                TernaryFilter::make('is_active')
                    ->label('Jenis dipakai')
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
            ])
            /*
             * Tanpa tombol hapus, dan modulnya memang tidak menyediakan izin delete.
             *
             * Menghapus jenis dokumen yang sudah dipakai akan menghilangkan arti
             * kolom metadata seluruh dokumen lamanya, dan yang sebenarnya dibutuhkan
             * orang adalah menonaktifkannya supaya tidak bisa dipilih lagi. Itu sudah
             * tersedia lewat saklar Jenis dipakai di formulirnya.
             */
            ->emptyStateHeading('Belum ada jenis dokumen')
            ->emptyStateDescription('Jalankan php artisan db:seed --class=DocumentTypeSeeder untuk mengisi 20 jenis awal, lalu tinjau bersama orang yang tahu dokumen apa saja yang benar benar beredar di perusahaan ini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentTypes::route('/'),
        ];
    }
}
