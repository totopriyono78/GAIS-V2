<?php

namespace App\Filament\Resources\SupplyOpnames;

use App\Filament\Resources\SupplyOpnames\Pages\CreateSupplyOpname;
use App\Filament\Resources\SupplyOpnames\Pages\ListSupplyOpnames;
use App\Filament\Resources\SupplyOpnames\Pages\ViewSupplyOpname;
use App\Models\Location;
use App\Models\SupplyItem;
use App\Models\SupplyOpname;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
use UnitEnum;

/**
 * Penghitungan fisik barang habis pakai.
 *
 * Ini kiriman yang menutup siklus ATK. Sejak kiriman M barang keluar lewat permintaan, sejak
 * kiriman N barang masuk lewat penerimaan, dan sekarang selisih antara buku dan rak
 * diselesaikan lewat dokumen yang punya nomor, tanggal, dan orang yang bertanggung jawab.
 *
 * Alurnya sengaja menyalin opname aset dari kiriman B sampai ke nama tombolnya, karena orang
 * yang menghitung pulpen di lemari adalah orang yang sama yang menghitung kursi di ruang
 * rapat, dan tidak ada gunanya ia mempelajari dua alur untuk pekerjaan yang sama.
 *
 * Satu hal yang berbeda dan penting: penyesuaian di sini melahirkan mutasi koreksi, bukan
 * mengubah kolom. Karena itu hasil opname bisa ditelusuri di buku stok seperti mutasi lain,
 * dan tidak ada angka yang berubah tanpa meninggalkan barisnya sendiri.
 */
class SupplyOpnameResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = SupplyOpname::class;

    protected static string $moduleCode = 'supply_opnames';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|UnitEnum|null $navigationGroup = 'Office Supplies';

    protected static ?string $navigationLabel = 'Supply Opname';

    protected static ?string $modelLabel = 'supply opname';

    protected static ?string $pluralModelLabel = 'supply opnames';

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Lencana hanya menghitung sesi yang sudah selesai dihitung tetapi stoknya belum
     * disesuaikan, dan hanya untuk yang boleh menyesuaikan. Sesi yang masih dihitung tidak
     * menunggu siapa pun selain yang sedang memegang lembar hitungannya.
     */
    public static function getNavigationBadge(): ?string
    {
        if (! static::allows('adjust')) {
            return null;
        }

        $jumlah = static::getEloquentQuery()->menungguPenyesuaian()->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Opname Session')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama sesi')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull()
                        ->placeholder('Contoh: opname ATK akhir September 2026')
                        ->helperText('Nama yang dipakai orang saat menyebut sesi ini di rapat. Nomornya dibuat sendiri oleh aplikasi.'),

                    Select::make('scope_category')
                        ->label('Batasi kategori')
                        ->options(SupplyItem::CATEGORIES)
                        ->placeholder('Seluruh kategori')
                        ->helperText('Kosongkan untuk menghitung semuanya. Membatasi kategori berguna kalau pantry dan alat tulis dihitung di hari yang berbeda.'),

                    Select::make('scope_location_id')
                        ->label('Batasi tempat simpan')
                        ->options(fn (): array => Location::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->placeholder('Seluruh tempat simpan')
                        ->helperText('Kosongkan untuk menghitung semuanya. Hanya barang yang tempat simpannya diisi yang akan tersaring.'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: dihitung bersama Rina dan Andi, gudang ditutup selama penghitungan.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Opname Session')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (SupplyOpname $record): string => $record->statusLabel())
                        ->color(fn (SupplyOpname $record): string => $record->statusColor())
                        ->helperText(fn (SupplyOpname $record): string => $record->tahapLabel()),
                    TextEntry::make('kemajuan')
                        ->label('Kemajuan hitungan')
                        ->state(fn (SupplyOpname $record): string => $record->jumlahBaris() === 0
                            ? 'Daftar belum disusun'
                            : $record->sudahDihitung().' dari '.$record->jumlahBaris().' barang'),
                    TextEntry::make('name')->label('Nama sesi')->columnSpanFull(),
                    TextEntry::make('cakupan')
                        ->label('Cakupan')
                        ->columnSpan(2)
                        ->state(fn (SupplyOpname $record): string => $record->describeScope()),
                    TextEntry::make('selisih')
                        ->label('Barang yang selisih')
                        ->state(fn (SupplyOpname $record): string => $record->sudahDihitung() === 0
                            ? 'Belum ada yang dihitung'
                            : $record->jumlahSelisih().' dari '.$record->sudahDihitung().' yang sudah dihitung')
                        ->color(fn (SupplyOpname $record): ?string => $record->sudahDihitung() > 0
                            && $record->jumlahSelisih() > 0 ? 'warning' : null),
                    TextEntry::make('notes')->label('Catatan')->placeholder('Tidak ada')->columnSpanFull(),
                ]),

            Section::make('Counting & Adjustment')
                ->columns(2)
                ->schema([
                    TextEntry::make('penghitungan')
                        ->label('Penghitungan')
                        ->state(function (SupplyOpname $record): string {
                            if ($record->status === 'dibatalkan') {
                                return 'Sesi dibatalkan sebelum selesai. Stok tidak tersentuh.';
                            }

                            if ($record->isDraft()) {
                                return $record->jumlahBaris() === 0
                                    ? 'Daftar targetnya belum disusun. Susun daftar lebih dulu lewat tombol di atas.'
                                    : 'Daftar berisi '.$record->jumlahBaris().' barang dan siap dihitung. Menyusun ulang daftar akan menghapus hitungan yang sudah terlanjur dicatat.';
                            }

                            if ($record->isRunning()) {
                                return 'Sedang dihitung sejak '.($record->started_at?->translatedFormat('d F Y, H:i') ?? 'entah kapan')
                                    .'. '.$record->belumDihitung().' barang belum dicatat hitungan fisiknya.';
                            }

                            return 'Selesai dihitung '.($record->finished_at?->translatedFormat('d F Y, H:i') ?? 'tanpa dicatat kapan')
                                .', '.$record->sudahDihitung().' dari '.$record->jumlahBaris().' barang tercatat.';
                        }),
                    TextEntry::make('penyesuaian')
                        ->label('Penyesuaian stok')
                        ->state(function (SupplyOpname $record): string {
                            if ($record->isAdjusted()) {
                                return 'Sudah diterapkan '.($record->adjustedByUser?->name ?? 'pengguna yang sudah dihapus')
                                    .' pada '.$record->adjusted_at->translatedFormat('d F Y, H:i')
                                    .'. Tiap selisih melahirkan satu mutasi koreksi yang bisa dibuka dari daftar di bawah.';
                            }

                            if (! $record->isFinished()) {
                                return 'Belum bisa diterapkan. Sesi perlu diselesaikan lebih dulu.';
                            }

                            return 'Belum diterapkan. Stok masih memakai angka lama, dan '
                                .$record->jumlahSelisih().' barang akan berubah begitu penyesuaian dijalankan.';
                        }),
                    /*
                     * Peringatan pergerakan stok hanya muncul kalau memang ada, dan sengaja
                     * ditaruh selebar halaman supaya tidak terlewat. Hitungan fisik yang
                     * diambil sebelum barang bergerak adalah alasan paling umum kenapa hasil
                     * opname dipertanyakan orang seminggu kemudian.
                     */
                    TextEntry::make('pergerakan')
                        ->label('Stok bergerak selama penghitungan')
                        ->columnSpanFull()
                        /*
                         * Tidak ada syarat status di sini, karena syarat itu dipegang satu tempat
                         * saja, di SupplyOpname::pergerakanPerluDiwaspadai() yang dibaca lewat
                         * SupplyOpnameLine::stokBergerak(). Sesi yang sudah disesuaikan dan sesi
                         * yang dibatalkan sama sama membuat peringatan ini diam, dan alasan
                         * keduanya ditulis di sana. Ditulis ulang di sini, keduanya akan berbeda
                         * pendapat pada suatu hari.
                         */
                        ->visible(fn (SupplyOpname $record): bool => ! $record->isDraft()
                            && $record->barisStokBergerak() > 0)
                        ->color('warning')
                        ->state(fn (SupplyOpname $record): string => $record->barisStokBergerak()
                            .' barang stoknya berubah setelah daftar ini disusun, karena ada penerimaan atau penyerahan di sela sela penghitungan. '
                            .'Selisihnya nanti dihitung terhadap stok terbaru, bukan terhadap angka saat daftar disusun, jadi hasilnya tetap benar. '
                            .'Yang perlu Anda pastikan hanya satu: hitungan fisiknya diambil setelah barang itu bergerak, bukan sebelumnya.'),
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
                TextColumn::make('name')
                    ->label('Nama sesi')
                    ->description(fn (SupplyOpname $record): string => $record->describeScope())
                    ->searchable()
                    ->wrap(),
                TextColumn::make('kemajuan')
                    ->label('Kemajuan')
                    ->state(fn (SupplyOpname $record): string => $record->jumlahBaris() === 0
                        ? 'Belum disusun'
                        : $record->sudahDihitung().' dari '.$record->jumlahBaris()),
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->state(fn (SupplyOpname $record): string => $record->sudahDihitung() === 0
                        ? 'Belum dihitung'
                        : $record->jumlahSelisih().' barang')
                    ->color(fn (SupplyOpname $record): ?string => $record->sudahDihitung() > 0
                        && $record->jumlahSelisih() > 0 ? 'warning' : null),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (SupplyOpname $record): string => $record->statusLabel())
                    ->color(fn (SupplyOpname $record): string => $record->statusColor())
                    ->description(fn (SupplyOpname $record): string => $record->tahapLabel())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Mulai dihitung')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum dimulai')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adjusted_at')
                    ->label('Stok disesuaikan')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Belum disesuaikan')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('adjustedByUser.name')
                    ->label('Disesuaikan oleh')
                    ->placeholder('Belum disesuaikan')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['targetLocation', 'adjustedByUser'])
                ->orderByRaw("case when adjusted_at is null and status <> 'dibatalkan' then 0 else 1 end"))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_penyesuaian')
                    ->label('Menunggu penyesuaian stok')
                    ->query(fn (Builder $query): Builder => $query->menungguPenyesuaian()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(SupplyOpname::STATUSES)
                    ->multiple(),
                SelectFilter::make('scope_category')
                    ->label('Kategori')
                    ->options(SupplyItem::CATEGORIES),
            ])
            ->recordActions([
                static::susunAction(),
                static::mulaiAction(),
                static::selesaikanAction(),
                static::terapkanAction(),
                static::bukaLagiAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyOpname $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyOpname $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada sesi opname ATK')
            ->emptyStateDescription('Opname mencocokkan buku stok dengan isi rak yang sebenarnya. Susun daftar barang yang akan dihitung, catat hitungan fisiknya satu per satu, lalu terapkan penyesuaian. Tiap selisih melahirkan satu mutasi koreksi, jadi perubahannya tetap bisa ditelusuri seperti mutasi lain.');
    }

    // ------------------------------------------------------------------ tindakan

    protected static function segarkan(mixed $livewire, SupplyOpname $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    public static function susunAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('susun')
            ->label('Build Target List')
            ->icon('heroicon-o-list-bullet')
            ->color('primary')
            ->visible(fn (SupplyOpname $record): bool => $record->daftarBisaDisusun() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyOpname $record): string => 'Build Target List for '.$record->code)
            ->modalDescription(fn (SupplyOpname $record): string => 'Daftar disusun dari cakupan sesi ini: '
                .lcfirst($record->describeScope())
                .'. Stok menurut catatan ikut dibekukan sekarang sebagai pembanding.'
                .($record->jumlahBaris() > 0
                    ? ' Daftar yang sudah ada berisi '.$record->jumlahBaris().' barang dan akan diganti seluruhnya.'
                    : ''))
            ->modalSubmitActionLabel('Build List')
            ->action(function (SupplyOpname $record, $livewire): void {
                $jumlah = $record->generateLines();

                if ($jumlah === 0) {
                    Notification::make()
                        ->warning()
                        ->title('Tidak ada barang yang masuk cakupan')
                        ->body('Cakupan sesi ini tidak menghasilkan satu pun barang aktif. Periksa lagi batasan kategori dan tempat simpannya.')
                        ->persistent()
                        ->send();
                } else {
                    Notification::make()
                        ->success()
                        ->title('Daftar tersusun')
                        ->body($jumlah.' barang siap dihitung.')
                        ->send();
                }

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function mulaiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('mulai')
            ->label('Start Counting')
            ->icon('heroicon-o-play')
            ->color('primary')
            ->visible(fn (SupplyOpname $record): bool => $record->isDraft() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyOpname $record): string => 'Start Counting for '.$record->code)
            ->modalDescription(function (SupplyOpname $record): string {
                $alasan = $record->alasanBelumBisaDimulai();

                return $alasan ?? 'Setelah dimulai, hitungan fisik bisa dicatat per barang, dan daftar targetnya tidak bisa disusun ulang lagi. Stok belum berubah sama sekali sampai penyesuaian diterapkan di akhir.';
            })
            ->modalSubmitActionLabel('Start Counting')
            ->action(function (SupplyOpname $record, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDimulai();

                if ($alasan !== null) {
                    Notification::make()->warning()->title('Belum bisa dimulai')->body($alasan)->persistent()->send();

                    $action->halt();
                }

                $record->mulai();

                Notification::make()->success()->title($record->code.' dimulai')
                    ->body('Hitungan fisik sekarang bisa dicatat di daftar barang.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function selesaikanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('selesaikan')
            ->label('Finish Counting')
            ->icon('heroicon-o-flag')
            ->color('primary')
            ->visible(fn (SupplyOpname $record): bool => $record->isRunning() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyOpname $record): string => 'Finish Counting for '.$record->code)
            ->modalDescription(fn (SupplyOpname $record): string => $record->sudahDihitung().' dari '
                .$record->jumlahBaris().' barang sudah dicatat, dan '.$record->jumlahSelisih().' di antaranya selisih.'
                .($record->belumDihitung() > 0
                    ? ' '.$record->belumDihitung().' barang belum dihitung dan stoknya tidak akan diubah sama sekali, karena tidak dihitung berarti tidak diketahui, bukan berarti nol.'
                    : '')
                .' Stok belum berubah. Perubahannya baru terjadi saat penyesuaian diterapkan.')
            ->modalSubmitActionLabel('Finish Counting')
            ->action(function (SupplyOpname $record, $livewire): void {
                if (! $record->selesaikan()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' selesai dihitung')
                    ->body('Menunggu penyesuaian stok diterapkan.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Menerapkan hasil hitungan ke buku stok. Satu satunya tindakan di modul ini yang
     * mengubah stok, dan satu satunya yang butuh izin `adjust`.
     */
    public static function terapkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('terapkan')
            ->label('Apply Stock Adjustment')
            ->icon('heroicon-o-scale')
            ->color('success')
            ->visible(fn (SupplyOpname $record): bool => $record->isFinished()
                && ! $record->isAdjusted()
                && static::allows('adjust'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyOpname $record): string => 'Apply Stock Adjustment for '.$record->code)
            ->modalDescription(function (SupplyOpname $record): string {
                $selisih = $record->jumlahSelisih();

                if ($selisih === 0) {
                    return 'Tidak ada satu pun barang yang selisih, jadi tidak ada mutasi koreksi yang akan lahir. Sesi ini hanya akan ditandai sudah disesuaikan, sebagai catatan bahwa gudang memang sudah dicocokkan.';
                }

                $bergerak = $record->barisStokBergerak();

                return $selisih.' barang akan dikoreksi supaya stoknya sama persis dengan hitungan fisik, dan tiap koreksi lahir sebagai satu mutasi di buku stok. '
                    .($record->belumDihitung() > 0
                        ? $record->belumDihitung().' barang yang belum dihitung tidak disentuh sama sekali. '
                        : '')
                    .($bergerak > 0
                        ? 'Perlu Anda ketahui, '.$bergerak.' barang stoknya sempat bergerak setelah daftar disusun, dan koreksinya dihitung terhadap stok terbaru. '
                        : '')
                    .'Setelah diterapkan, sesi ini tidak bisa dibuka lagi.';
            })
            ->modalSubmitActionLabel('Apply and Correct Stock')
            ->action(function (SupplyOpname $record, $livewire): void {
                if ($record->isAdjusted() || ! $record->isFinished()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                $hasil = $record->terapkanPenyesuaian();

                Notification::make()
                    ->success()
                    ->title($record->code.' sudah disesuaikan')
                    ->body($hasil['koreksi'] === 0
                        ? 'Tidak ada selisih, jadi tidak ada mutasi koreksi yang lahir. Stok tidak berubah.'
                        : $hasil['koreksi'].' mutasi koreksi lahir, '.$hasil['tambah'].' menambah dan '
                            .$hasil['kurang'].' mengurangi stok.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function bukaLagiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('buka_lagi')
            ->label('Reopen Counting')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->visible(fn (SupplyOpname $record): bool => $record->isFinished()
                && ! $record->isAdjusted()
                && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyOpname $record): string => 'Reopen '.$record->code)
            ->modalDescription('Hitungan yang sudah dicatat tetap tersimpan, dan barang yang terlewat bisa dihitung lagi. Hanya bisa dilakukan selama penyesuaian stok belum diterapkan.')
            ->modalSubmitActionLabel('Reopen Counting')
            ->action(function (SupplyOpname $record, $livewire): void {
                if (! $record->kembalikanKeHitung()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' dibuka lagi')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Cancel Session')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (SupplyOpname $record): bool => ! $record->isAdjusted()
                && $record->status !== 'dibatalkan'
                && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyOpname $record): string => 'Cancel '.$record->code)
            ->modalDescription('Sesi ini tetap tersimpan beserta hitungan yang sudah dicatat, sebagai catatan bahwa penghitungan pernah dimulai. Stok tidak tersentuh sama sekali.')
            ->modalSubmitActionLabel('Cancel Session')
            ->action(function (SupplyOpname $record, $livewire): void {
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
            ->body('Sesi ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->send();
    }

    // ------------------------------------------------------------------ perizinan

    /** Kepala sesi hanya bisa diubah selama masih draf. */
    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record) && $record->isDraft();
    }

    /**
     * Sesi yang stoknya sudah disesuaikan tidak boleh dihapus. Mutasi koreksinya sudah lahir,
     * dan menghapus sesinya akan memutus tautan itu sehingga koreksi tetap ada di buku stok
     * tetapi tidak lagi bisa dijelaskan datangnya dari hitungan yang mana.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && ! $record->isAdjusted();
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
            'index' => ListSupplyOpnames::route('/'),
            'create' => CreateSupplyOpname::route('/create'),
            'view' => ViewSupplyOpname::route('/{record}'),
        ];
    }
}
