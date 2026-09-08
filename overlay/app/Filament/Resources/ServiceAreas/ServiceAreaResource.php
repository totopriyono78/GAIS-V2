<?php

namespace App\Filament\Resources\ServiceAreas;

use App\Filament\Resources\ServiceAreas\Pages\ListServiceAreas;
use App\Models\Location;
use App\Models\ServiceArea;
use App\Models\ServiceStaff;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Data induk area layanan kebersihan.
 *
 * Satu baris di sini menjadi satu baris di lembar pemeriksaan, jadi ukurannya sebaiknya
 * sebesar yang sanggup dinilai sekali lihat oleh pengawas yang sedang berjalan. Itu sebabnya
 * satuannya area, bukan ruangan: "toilet pria lantai 2" bisa dinilai sekali lihat, sedangkan
 * "lantai 2" tidak.
 */
class ServiceAreaResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = ServiceArea::class;

    protected static string $moduleCode = 'service_areas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Service Areas';

    protected static ?string $modelLabel = 'service area';

    protected static ?string $pluralModelLabel = 'service areas';

    protected static ?int $navigationSort = 80;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Area yang dibersihkan dan diperiksa. Seberapa sering dan siapa penanggung jawabnya ditentukan di sini, dan keduanya ikut terbawa saat lembar pemeriksaan disusun.')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode area')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true)
                        ->helperText('Kode singkat yang dipakai mengurutkan lembar pemeriksaan, contoh: L2-TOILET-PRIA'),

                    TextInput::make('name')
                        ->label('Nama area')
                        ->required()
                        ->maxLength(150)
                        ->placeholder('Contoh: Toilet pria lantai 2'),

                    Select::make('category')
                        ->label('Jenis area')
                        ->options(ServiceArea::CATEGORIES)
                        ->default('lainnya')
                        ->required()
                        ->helperText('Dipakai menyaring saat menyusun lembar pemeriksaan, misalnya putaran yang khusus memeriksa seluruh toilet.'),

                    Select::make('frequency')
                        ->label('Dibersihkan')
                        ->options(ServiceArea::FREQUENCIES)
                        ->default('harian')
                        ->required()
                        ->helperText('Menentukan apakah area ini ikut terbawa pada putaran pemeriksaan harian atau hanya pada putaran yang lebih jarang.'),

                    Select::make('location_id')
                        ->label('Letaknya')
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
                        ->placeholder('Tidak ditautkan ke lokasi mana pun')
                        ->helperText('Boleh dikosongkan. Mengisinya membuat area ini bisa dipanggil sekaligus per lantai saat memeriksa.'),

                    Select::make('service_staff_id')
                        ->label('Penanggung jawab')
                        ->options(fn (): array => ServiceStaff::query()
                            ->kebersihan()
                            ->where('is_active', true)
                            ->with('employee')
                            ->get()
                            ->mapWithKeys(fn (ServiceStaff $petugas) => [
                                $petugas->id => $petugas->namaLengkap().' ('.$petugas->asalLabel().')',
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->placeholder('Belum ditentukan')
                        ->helperText('Hanya petugas kebersihan yang masih bertugas yang muncul di sini. Namanya ikut disalin ke lembar pemeriksaan saat daftarnya disusun.'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Misalnya bagian yang sering terlewat, atau alat khusus yang dibutuhkan')
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Area masih dipakai')
                        ->default(true)
                        ->helperText('Area yang dimatikan tidak lagi ikut terbawa saat lembar pemeriksaan baru disusun, tetapi lembar lama tetap menyebutnya.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Area')
                    ->description(fn (ServiceArea $record): ?string => $record->letakLabel())
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => ServiceArea::CATEGORIES[$state] ?? '-'),
                TextColumn::make('frequency')
                    ->label('Dibersihkan')
                    ->formatStateUsing(fn (?string $state): string => ServiceArea::FREQUENCIES[$state] ?? '-'),
                TextColumn::make('penanggung_jawab')
                    ->label('Penanggung jawab')
                    ->state(fn (ServiceArea $record): string => $record->penanggungJawabLabel())
                    ->color(fn (ServiceArea $record): ?string => $record->service_staff_id === null ? 'warning' : null),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('code')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['location', 'staff.employee']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('category')
                    ->label('Jenis area')
                    ->options(ServiceArea::CATEGORIES),
                SelectFilter::make('frequency')
                    ->label('Seberapa sering')
                    ->options(ServiceArea::FREQUENCIES),
                Filter::make('tanpa_penanggung_jawab')
                    ->label('Belum ada penanggung jawab')
                    ->query(fn (Builder $query): Builder => $query->whereNull('service_staff_id')),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->modalDescription('Area yang sudah pernah masuk lembar pemeriksaan sebaiknya dimatikan lewat Area masih dipakai, bukan dihapus, supaya lembar lama tetap bisa dibaca utuh.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada area yang cocok'
                : 'Belum ada area layanan')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada area yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh area lagi.'
                : 'Daftarkan area yang dibersihkan setiap hari lebih dulu, misalnya toilet tiap lantai, lobi, dan pantry. Lembar pemeriksaan lahir dari daftar ini, jadi selama kosong tidak ada yang bisa diperiksa.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceAreas::route('/'),
        ];
    }
}
