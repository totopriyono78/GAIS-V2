<?php

namespace App\Filament\Resources\ServiceStaff;

use App\Filament\Resources\ServiceStaff\Pages\ListServiceStaff;
use App\Models\Employee;
use App\Models\ServiceStaff;
use App\Models\Vendor;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Data induk petugas kebersihan dan keamanan.
 *
 * Satu formulir untuk dua jenis asal orang. Yang membedakannya hanya satu pilihan di atas,
 * dan sisa formulirnya menyesuaikan diri. Dibuat begitu supaya tim GA tidak perlu memilih
 * menu yang berbeda hanya karena orangnya digaji pihak yang berbeda.
 */
class ServiceStaffResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = ServiceStaff::class;

    protected static string $moduleCode = 'service_staff';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Service Staff';

    protected static ?string $modelLabel = 'service staff';

    protected static ?string $pluralModelLabel = 'service staff';

    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Petugas kebersihan dan keamanan, baik karyawan perusahaan maupun tenaga dari rekanan penyedia.')
                ->columns(2)
                ->schema([
                    /*
                     * Asal orangnya ditanyakan lebih dulu, bukan disimpulkan dari kolom mana
                     * yang kebetulan terisi. Kalau disimpulkan, orang yang salah mengisi baru
                     * tahu setelah menyimpan, dan yang tersimpan adalah baris yang separuh
                     * benar. Pilihan ini sendiri tidak disimpan: yang disimpan akibatnya,
                     * yaitu employee_id terisi atau kosong.
                     */
                    Select::make('asal')
                        ->label('Asal petugas')
                        ->options([
                            'karyawan' => 'Karyawan perusahaan',
                            'rekanan' => 'Tenaga dari rekanan',
                        ])
                        ->default('rekanan')
                        // formatStateUsing, bukan hanya default. default hanya berlaku saat
                        // membuat baris baru, sedangkan saat mengubah baris yang sudah ada
                        // formulir diisi dari recordnya, dan record tidak punya kolom asal.
                        ->formatStateUsing(fn (?ServiceStaff $record): string => $record?->isKaryawan() ? 'karyawan' : 'rekanan')
                        ->dehydrated(false)
                        ->live()
                        /*
                         * Sisi yang tidak dipakai dikosongkan saat pilihan ini berubah, bukan
                         * dibiarkan tertinggal di balik kolom yang menghilang.
                         *
                         * Tanpa ini, orang yang mengubah petugas dari karyawan menjadi tenaga
                         * rekanan mengetik nama baru, menekan Simpan, dan barisnya tidak
                         * berubah sama sekali. Sebabnya employee_id lama tetap tersimpan,
                         * lalu penjaga di model mengosongkan nama ketikan yang baru saja
                         * diisi. Perubahannya tersimpan sebagai bukan apa apa, tanpa satu pun
                         * pesan galat. Ketahuan saat pemeriksaan kiriman P.
                         */
                        ->afterStateUpdated(function (?string $state, callable $set): void {
                            if ($state === 'karyawan') {
                                $set('name', null);
                                $set('vendor_id', null);

                                return;
                            }

                            $set('employee_id', null);
                        })
                        ->required()
                        ->helperText('Menentukan dari mana namanya diambil. Nama karyawan dibaca dari kartu karyawan, nama tenaga rekanan diketik di sini.'),

                    Select::make('kind')
                        ->label('Bertugas di')
                        ->options(ServiceStaff::KINDS)
                        ->default('kebersihan')
                        ->required(),

                    Select::make('employee_id')
                        ->label('Karyawan')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->get()
                            ->mapWithKeys(fn (Employee $karyawan) => [
                                $karyawan->id => $karyawan->full_name.' ('.$karyawan->nip.')',
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get): bool => $get('asal') === 'karyawan')
                        ->required(fn ($get): bool => $get('asal') === 'karyawan')
                        /*
                         * Tanpa ->dehydrated(). Sudah dicoba, dan pada versi Filament ini
                         * kolom yang disembunyikan tetap tidak ikut terkirim meskipun
                         * ditandai begitu. Yang menjaga bentuk barisnya adalah penjaga di
                         * ServiceStaff::booted(), dan uraiannya ada di sana.
                         */
                        ->helperText('Namanya ikut berubah sendiri kalau kartu karyawannya kelak diperbarui.'),

                    TextInput::make('name')
                        ->label('Nama petugas')
                        ->maxLength(150)
                        ->visible(fn ($get): bool => $get('asal') === 'rekanan')
                        ->required(fn ($get): bool => $get('asal') === 'rekanan'),

                    Select::make('vendor_id')
                        ->label('Rekanan penyedia')
                        ->options(fn (): array => Vendor::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get): bool => $get('asal') === 'rekanan')
                        ->placeholder('Belum terdaftar sebagai rekanan')
                        ->helperText('Boleh dikosongkan, tetapi mengisinya membuat keluhan tentang petugas ini tahu harus disampaikan ke siapa.'),

                    TextInput::make('phone')
                        ->label('Nomor telepon')
                        ->tel()
                        ->maxLength(30)
                        ->placeholder('Boleh dikosongkan'),

                    DatePicker::make('start_date')
                        ->label('Mulai bertugas')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->placeholder('Boleh dikosongkan'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Misalnya shift yang biasa dipegang, atau area yang jadi tanggung jawabnya')
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Masih bertugas')
                        ->default(true)
                        ->helperText('Dimatikan kalau orangnya sudah tidak bertugas lagi. Riwayat pemeriksaan yang sudah menyebut namanya tetap utuh.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->state(fn (ServiceStaff $record): string => $record->namaLengkap())
                    ->description(fn (ServiceStaff $record): string => $record->asalLabel())
                    /*
                     * Pencariannya menyisir dua kolom di dua tabel, karena nama petugas
                     * memang tersimpan di dua tempat: kolom name untuk tenaga rekanan, dan
                     * kartu karyawan untuk karyawan perusahaan. Keduanya dibungkus satu
                     * kelompok where supaya penyaring lain di sebelahnya tidak ikut terbuka
                     * oleh orWhere di dalamnya.
                     */
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where(fn (Builder $q) => $q
                            ->where('name', 'ilike', '%'.$search.'%')
                            ->orWhereHas('employee', fn (Builder $karyawan) => $karyawan
                                ->where('full_name', 'ilike', '%'.$search.'%')))),
                TextColumn::make('kind')
                    ->label('Bertugas di')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => ServiceStaff::KINDS[$state] ?? '-'),
                TextColumn::make('areas_count')
                    ->label('Area')
                    ->counts('areas')
                    // Tanpa placeholder. Hasil hitungan tidak pernah null, jadi baris yang
                    // belum memegang area mana pun berbunyi nol, dan nol memang jawaban yang
                    // benar untuk pertanyaan berapa.
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('telepon')
                    ->label('Telepon')
                    ->state(fn (ServiceStaff $record): ?string => $record->teleponLabel())
                    ->placeholder('Tidak dicatat'),
                TextColumn::make('start_date')
                    ->label('Mulai bertugas')
                    ->date('d M Y')
                    ->placeholder('Tidak dicatat')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Bertugas')
                    ->boolean(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('kind')
                    ->label('Bertugas di')
                    ->options(ServiceStaff::KINDS),
                SelectFilter::make('is_active')
                    ->label('Keadaan')
                    ->options([
                        '1' => 'Masih bertugas',
                        '0' => 'Sudah tidak bertugas',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->modalDescription('Petugas yang namanya sudah tercatat di lembar pemeriksaan sebaiknya dimatikan saja lewat Masih bertugas, bukan dihapus, supaya riwayatnya tetap bisa dibaca.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada petugas yang cocok'
                : 'Belum ada petugas terdaftar')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada petugas yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh petugas lagi.'
                : 'Daftarkan petugas kebersihan dan satpam di sini lebih dulu, karena area layanan menunjuk ke salah satu dari mereka sebagai penanggung jawab.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceStaff::route('/'),
        ];
    }
}
