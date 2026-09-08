<?php

namespace App\Filament\Resources\CleaningInspections;

use App\Filament\Resources\CleaningInspections\Pages\CreateCleaningInspection;
use App\Filament\Resources\CleaningInspections\Pages\ListCleaningInspections;
use App\Filament\Resources\CleaningInspections\Pages\ViewCleaningInspection;
use App\Models\CleaningInspection;
use App\Models\Location;
use App\Models\ServiceArea;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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
use UnitEnum;

/**
 * Putaran pemeriksaan kebersihan oleh pengawas GA.
 *
 * Daftar areanya lahir sendiri saat putaran dibuat, jadi tidak ada tombol menyusun daftar
 * seperti pada opname. Yang ada hanya tombol menyusun ulang, dan itu pun hanya berguna kalau
 * cakupannya diubah setelah putaran berjalan.
 */
class CleaningInspectionResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = CleaningInspection::class;

    protected static string $moduleCode = 'cleaning_inspections';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Facility Services';

    protected static ?string $navigationLabel = 'Cleaning Inspections';

    protected static ?string $modelLabel = 'cleaning inspection';

    protected static ?string $pluralModelLabel = 'cleaning inspections';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                // Keterangan kartu ini sengaja tidak mengulang subjudul halaman di atasnya.
                // Yang di atas menerangkan apa yang sedang dikerjakan, yang di sini
                // menerangkan bagaimana ketiga pembatas di bawah bekerja bersama.
                ->description('Ketiga pembatas di bawah berlaku bersamaan. Membiarkan semuanya kosong berarti seluruh area yang masih dipakai ikut diperiksa.')
                ->columns(2)
                ->schema([
                    DatePicker::make('inspection_date')
                        ->label('Tanggal pemeriksaan')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required()
                        ->helperText('Boleh diisi tanggal kemarin kalau lembar kertasnya baru sempat dimasukkan hari ini.'),

                    Select::make('scope_frequency')
                        ->label('Batasi jadwalnya')
                        ->options(ServiceArea::FREQUENCIES)
                        ->default('harian')
                        ->placeholder('Seluruh jadwal')
                        ->helperText('Putaran harian biasanya dibatasi pada area yang memang dijadwalkan setiap hari.'),

                    Select::make('scope_category')
                        ->label('Batasi jenis area')
                        ->options(ServiceArea::CATEGORIES)
                        ->placeholder('Seluruh jenis'),

                    Select::make('scope_location_id')
                        ->label('Batasi lokasi')
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
                        ->placeholder('Seluruh lokasi')
                        ->helperText('Hanya area yang ditautkan ke lokasi ini yang ikut. Area yang tidak punya lokasi tidak akan terbawa.'),

                    Textarea::make('notes')
                        ->label('Catatan putaran')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Misalnya alasan putaran ini dilakukan di luar jadwal biasa')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inspection')
                ->columns(2)
                ->schema([
                    TextEntry::make('code')
                        ->label('Nomor')
                        ->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (CleaningInspection $record): string => $record->statusLabel())
                        ->color(fn (CleaningInspection $record): string => $record->statusColor())
                        ->helperText(fn (CleaningInspection $record): string => $record->tahapLabel()),
                    TextEntry::make('inspection_date')
                        ->label('Tanggal pemeriksaan')
                        ->date('d M Y'),
                    TextEntry::make('cakupan')
                        ->label('Cakupan')
                        ->state(fn (CleaningInspection $record): string => $record->describeScope()),
                    TextEntry::make('kemajuan')
                        ->label('Kemajuan pemeriksaan')
                        ->state(fn (CleaningInspection $record): string => $record->kemajuanLabel()),
                    TextEntry::make('temuan')
                        ->label('Area yang bermasalah')
                        ->state(fn (CleaningInspection $record): string => $record->temuanLabel())
                        ->color(fn (CleaningInspection $record): ?string => $record->jumlahTemuan() > 0 ? 'danger' : null),
                    TextEntry::make('notes')
                        ->label('Catatan')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),
                ]),

            Section::make('Trail')
                ->columns(2)
                ->schema([
                    TextEntry::make('createdByUser.name')
                        ->label('Dibuat oleh')
                        ->placeholder('Tidak tercatat'),
                    TextEntry::make('finished_at')
                        ->label('Diselesaikan')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('Belum diselesaikan'),
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
                TextColumn::make('inspection_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (CleaningInspection $record): string => $record->describeScope())
                    ->sortable(),
                TextColumn::make('kemajuan')
                    ->label('Kemajuan')
                    ->state(fn (CleaningInspection $record): string => $record->kemajuanLabel())
                    ->alignEnd(),
                TextColumn::make('temuan')
                    ->label('Temuan')
                    ->badge()
                    ->state(fn (CleaningInspection $record): string => $record->sudahDiperiksa() === 0
                        ? 'Belum diperiksa'
                        : $record->jumlahTemuan().' area')
                    ->color(fn (CleaningInspection $record): string => match (true) {
                        $record->sudahDiperiksa() === 0 => 'gray',
                        $record->jumlahTemuan() > 0 => 'danger',
                        default => 'success',
                    })
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (CleaningInspection $record): string => $record->statusLabel())
                    ->color(fn (CleaningInspection $record): string => $record->statusColor())
                    ->description(fn (CleaningInspection $record): string => $record->tahapLabel())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('finished_at')
                    ->label('Diselesaikan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdByUser.name')
                    ->label('Dibuat oleh')
                    ->placeholder('Tidak tercatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('inspection_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['targetLocation', 'createdByUser']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(CleaningInspection::STATUSES),
                Filter::make('ada_temuan')
                    ->label('Hanya yang ada temuannya')
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'lines',
                        fn (Builder $baris) => $baris->whereIn('result', ['kurang', 'kotor'])
                    )),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                static::selesaikanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (CleaningInspection $record): bool => $record->isRunning()),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (CleaningInspection $record): bool => $record->isRunning()),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada putaran yang cocok'
                : 'Belum ada putaran pemeriksaan')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada putaran yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh putaran lagi.'
                : 'Buat putaran baru untuk hari ini, lalu berkeliling dan catat keadaan tiap area. Daftar areanya disusun sendiri dari data induk area layanan.');
    }

    // ---------------------------------------------------------------- tindakan

    /**
     * Menggambar ulang halaman lihat setelah tindakan yang mengubah keadaan.
     *
     * Sama seperti pada kedua opname: tombol yang ditekan dari halaman lihat mengubah status
     * yang juga dibaca infolist di halaman itu sendiri.
     */
    protected static function segarkan(mixed $livewire, CleaningInspection $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    public static function susunUlangAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('susun_ulang')
            ->label('Rebuild Area List')
            ->icon('heroicon-o-arrow-path')
            ->color('gray')
            ->visible(fn (CleaningInspection $record): bool => $record->isRunning() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (CleaningInspection $record): string => 'Rebuild Area List for '.$record->code)
            ->modalDescription(fn (CleaningInspection $record): string => 'Daftar disusun ulang dari cakupan putaran ini: '
                .lcfirst($record->describeScope()).'.'
                .($record->sudahDiperiksa() > 0
                    ? ' Perhatikan, '.$record->sudahDiperiksa().' area sudah terlanjur diperiksa, dan hasil beserta fotonya ikut terhapus.'
                    : ''))
            ->modalSubmitActionLabel('Rebuild List')
            ->action(function (CleaningInspection $record, $livewire): void {
                $jumlah = $record->generateLines();

                if ($jumlah === 0) {
                    Notification::make()
                        ->warning()
                        ->title('Tidak ada area yang masuk cakupan')
                        ->body('Cakupan putaran ini tidak menghasilkan satu pun area yang masih dipakai. Periksa lagi batasan jadwal, jenis, dan lokasinya.')
                        ->persistent()
                        ->send();
                } else {
                    Notification::make()
                        ->success()
                        ->title('Daftar tersusun ulang')
                        ->body($jumlah.' area siap diperiksa.')
                        ->send();
                }

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function selesaikanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('selesaikan')
            ->label('Finish Inspection')
            ->icon('heroicon-o-flag')
            ->color('primary')
            ->visible(fn (CleaningInspection $record): bool => $record->isRunning() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (CleaningInspection $record): string => 'Finish Inspection for '.$record->code)
            ->modalDescription(function (CleaningInspection $record): string {
                $alasan = $record->alasanBelumBisaDiselesaikan();

                if ($alasan !== null) {
                    return $alasan;
                }

                return $record->sudahDiperiksa().' dari '.$record->jumlahBaris().' area sudah diperiksa, dan '
                    .$record->jumlahTemuan().' di antaranya bermasalah.'
                    .($record->belumDiperiksa() > 0
                        ? ' '.$record->belumDiperiksa().' area tidak sempat diperiksa dan akan tercatat begitu apa adanya, karena tidak diperiksa berarti tidak diketahui, bukan berarti bersih.'
                        : '')
                    .' Setelah diselesaikan, hasilnya tidak bisa diubah lagi.';
            })
            ->modalSubmitActionLabel('Finish Inspection')
            ->action(function (CleaningInspection $record, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDiselesaikan();

                if ($alasan !== null) {
                    Notification::make()->warning()->title('Belum bisa diselesaikan')->body($alasan)->persistent()->send();

                    $action->halt();
                }

                $record->selesaikan();

                Notification::make()
                    ->success()
                    ->title($record->code.' selesai')
                    ->body($record->jumlahTemuan() > 0
                        ? $record->jumlahTemuan().' area tercatat bermasalah dan tetap terbaca di lembar ini.'
                        : 'Seluruh area yang diperiksa dalam keadaan bersih.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Cancel Inspection')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (CleaningInspection $record): bool => $record->isRunning() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (CleaningInspection $record): string => 'Cancel '.$record->code)
            ->modalDescription('Putaran ini tetap tersimpan beserta hasil yang sudah dicatat, sebagai catatan bahwa pemeriksaan pernah dimulai.')
            ->modalSubmitActionLabel('Cancel Inspection')
            ->action(function (CleaningInspection $record, $livewire): void {
                $record->batalkan();

                Notification::make()->success()->title($record->code.' dibatalkan')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
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
            'index' => ListCleaningInspections::route('/'),
            'create' => CreateCleaningInspection::route('/create'),
            'view' => ViewCleaningInspection::route('/{record}'),
        ];
    }
}
