<?php

namespace App\Filament\Resources\SupplyPurchases;

use App\Filament\Resources\SupplyPurchases\Pages\CreateSupplyPurchase;
use App\Filament\Resources\SupplyPurchases\Pages\ListSupplyPurchases;
use App\Filament\Resources\SupplyPurchases\Pages\ViewSupplyPurchase;
use App\Models\SupplyPurchase;
use App\Models\SupplyPurchaseLine;
use App\Models\Vendor;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
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
 * Pembelian ATK, pesanan resmi maupun pembelian langsung.
 *
 * Modul ini menutup celah yang paling lama menganga di persediaan GAIS. Sejak kiriman C, stok
 * bertambah karena seseorang mengetik baris barang masuk, dan tidak ada apa pun yang
 * menghubungkan angka itu dengan pesanan ke pemasok maupun dengan faktur yang nanti dibayar.
 * Tiga angka yang seharusnya sama hidup di tiga tempat tanpa saling memeriksa.
 *
 * Sekarang barang masuk lahir dari penerimaan, penerimaan menunjuk baris pesanan, dan tagihan
 * boleh menunjuk pesanan yang sama. Rantainya bisa dibaca dari ujung ke ujung, dan layar
 * tagihan menyebutkan sendiri kalau nilainya tidak cocok dengan barang yang sudah datang.
 *
 * Dua bentuk pembelian memakai layar ini bersama, dan yang membedakan hanya jalur
 * persetujuannya. Alasannya ditulis panjang di model, dan ringkasnya: pesanan resmi disetujui
 * saat uangnya belum keluar, pembelian langsung dicatat saat uangnya sudah keluar.
 */
class SupplyPurchaseResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = SupplyPurchase::class;

    protected static string $moduleCode = 'supply_purchases';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|UnitEnum|null $navigationGroup = 'Office Supplies';

    protected static ?string $navigationLabel = 'Supply Purchases';

    protected static ?string $modelLabel = 'supply purchase';

    protected static ?string $pluralModelLabel = 'supply purchases';

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Lencana menghitung yang menunggu tindakan orang yang sedang masuk: antrean tanda
     * tangan untuk yang boleh menyetujui, antrean barang datang untuk yang boleh menerima.
     */
    public static function getNavigationBadge(): ?string
    {
        $query = static::getEloquentQuery();

        $bisaSetujui = static::allows('approve');
        $bisaTerima = static::allows('receive');

        if (! $bisaSetujui && ! $bisaTerima) {
            return null;
        }

        $jumlah = $query->where(function (Builder $sub) use ($bisaSetujui, $bisaTerima): void {
            if ($bisaSetujui) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPersetujuan());
            }

            if ($bisaTerima) {
                $sub->orWhere(fn (Builder $q) => $q->menungguBarang());
            }
        })->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Purchase')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    /*
                     * Jenis pembelian dipilih lebih dulu dan tidak bisa diubah setelah
                     * tersimpan, karena jenisnya menentukan seluruh sisa alurnya. Pesanan
                     * yang berubah menjadi pembelian langsung di tengah jalan akan
                     * meninggalkan tanda tangan manajer yang menyetujui sesuatu yang sudah
                     * tidak ada lagi bentuknya.
                     */
                    Radio::make('kind')
                        ->label('Cara membeli')
                        ->options(SupplyPurchase::KINDS)
                        ->default('pesanan')
                        ->required()
                        ->live()
                        ->columnSpanFull()
                        ->disabled(fn (?Model $record): bool => $record !== null)
                        ->dehydrated()
                        ->descriptions([
                            'pesanan' => 'Dikirim ke pemasok setelah disetujui manajer GA. Barangnya datang belakangan, boleh bertahap.',
                            'langsung' => 'Barangnya sudah dibeli dan sudah di tangan. Dicatat lalu langsung masuk gudang, tanpa persetujuan, karena uangnya memang sudah keluar.',
                        ])
                        ->helperText(fn (?Model $record): ?string => $record !== null
                            ? 'Tidak bisa diubah setelah tersimpan, karena jenisnya menentukan seluruh sisa alurnya.'
                            : null),

                    TextInput::make('description')
                        ->label('Untuk keperluan apa')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->placeholder('Contoh: belanja ATK rutin bulan September')
                        ->helperText('Satu kalimat yang menjelaskan seluruh barang di dalamnya. Ini yang dibaca manajer lebih dulu.'),

                    Select::make('vendor_id')
                        ->label('Rekanan')
                        ->options(fn (): array => Vendor::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required(fn ($get): bool => $get('kind') === 'pesanan')
                        ->visible(fn ($get): bool => $get('kind') === 'pesanan')
                        ->helperText('Pesanan resmi selalu menunjuk rekanan terdaftar, karena nomornya ikut tercetak di pesanan yang dikirim.'),

                    /*
                     * Nama penjual bebas diketik untuk pembelian langsung. Keputusan pemilik
                     * proyek pada 8 September 2026: tidak ada yang akan mendaftarkan sebuah
                     * marketplace sebagai rekanan hanya untuk membeli satu box pulpen, dan
                     * memaksanya hanya melahirkan data induk berisi nama toko sekali pakai.
                     */
                    TextInput::make('supplier_name')
                        ->label('Beli di mana')
                        ->maxLength(150)
                        ->visible(fn ($get): bool => $get('kind') === 'langsung')
                        ->placeholder('Contoh: Toko Sinar Jaya, Jalan Kaliurang')
                        ->helperText('Boleh ditulis bebas. Kalau penjualnya kebetulan rekanan terdaftar, tulis saja namanya sama persis supaya mudah dicari.'),

                    DatePicker::make('order_date')
                        ->label(fn ($get): string => $get('kind') === 'langsung' ? 'Tanggal beli' : 'Tanggal pesanan')
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->maxDate(now())
                        ->required(),

                    DatePicker::make('expected_date')
                        ->label('Barang dijanjikan datang')
                        ->displayFormat('d M Y')
                        ->visible(fn ($get): bool => $get('kind') === 'pesanan')
                        ->placeholder('Tidak menyebut tanggal')
                        ->helperText('Boleh dikosongkan. Kalau diisi, pesanan yang lewat janji ikut disebut di daftar.'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: minta dikirim sebelum tanggal 20, gudang tutup akhir bulan.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Purchase')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (SupplyPurchase $record): string => $record->statusLabel())
                        ->color(fn (SupplyPurchase $record): string => $record->statusColor())
                        ->helperText(fn (SupplyPurchase $record): string => $record->tahapLabel()),
                    TextEntry::make('nilai')
                        ->label('Nilai pesanan')
                        ->state(fn (SupplyPurchase $record): string => $record->totalLabel())
                        ->helperText(fn (SupplyPurchase $record): string => 'Dijumlahkan dari '
                            .$record->jenisLabel().' di bawah, tidak pernah diketik.'),
                    TextEntry::make('description')->label('Untuk keperluan apa')->columnSpanFull(),
                    TextEntry::make('kind')
                        ->label('Cara membeli')
                        ->state(fn (SupplyPurchase $record): string => $record->kindLabel()),
                    TextEntry::make('pemasok')
                        ->label(fn (SupplyPurchase $record): string => $record->kind === 'pesanan' ? 'Rekanan' : 'Beli di mana')
                        ->state(fn (SupplyPurchase $record): string => $record->pemasokLabel()),
                    TextEntry::make('order_date')
                        ->label('Tanggal')
                        ->date('d M Y')
                        ->helperText(fn (SupplyPurchase $record): ?string => $record->kind === 'pesanan'
                            ? $record->janjiLabel()
                            : null),
                    TextEntry::make('notes')->label('Catatan')->placeholder('Tidak ada')->columnSpanFull(),
                ]),

            Section::make('Approval & Delivery')
                ->columns(2)
                ->schema([
                    TextEntry::make('persetujuan')
                        ->label('Persetujuan manajer')
                        ->state(function (SupplyPurchase $record): string {
                            if (filled($record->approval_skipped_reason)) {
                                return 'Lewat persetujuan. '.$record->approval_skipped_reason.'.';
                            }

                            if ($record->status === 'draft') {
                                return 'Belum diajukan. Daftar barangnya masih bisa diubah.';
                            }

                            if ($record->status === 'diajukan') {
                                return 'Menunggu manajer GA menyetujui sebelum pesanan dikirim ke rekanan.';
                            }

                            if (blank($record->approved_at)) {
                                return 'Belum disetujui.';
                            }

                            return 'Disetujui '.($record->approvedByUser?->name ?? 'pengguna yang sudah dihapus')
                                .' pada '.$record->approved_at->translatedFormat('d F Y, H:i')
                                .(filled($record->approval_note) ? '. Catatan: '.$record->approval_note : '.');
                        }),
                    TextEntry::make('penerimaan')
                        ->label('Kedatangan barang')
                        ->state(function (SupplyPurchase $record): string {
                            if (in_array($record->status, ['draft', 'diajukan', 'ditolak', 'dibatalkan'], true)) {
                                return 'Belum ada barang yang diterima.';
                            }

                            $jumlahPenerimaan = $record->receipts()->count();
                            $belum = $record->barisBelumLengkap();

                            /*
                             * Pesanan yang sudah ditutup tidak lagi menunggu apa pun, jadi ia
                             * diperiksa lebih dulu. Tanpa cabang ini, pesanan yang ditutup
                             * tanpa pernah menerima barang berbunyi "seluruh pesanan masih
                             * ditunggu", bertentangan dengan subjudul halamannya sendiri yang
                             * sudah menyatakan pesanan itu ditutup.
                             */
                            if (filled($record->closing_reason)) {
                                return $jumlahPenerimaan === 0
                                    ? 'Ditutup tanpa satu pun barang datang. Sisanya tercatat tidak jadi dikirim.'
                                    : $jumlahPenerimaan.' kali penerimaan tercatat, senilai '
                                        .Rupiah::penuh($record->totalDiterima())
                                        .', lalu pesanan ditutup dengan '.$belum.' jenis barang yang tidak jadi dikirim.';
                            }

                            if ($jumlahPenerimaan === 0) {
                                return 'Belum ada barang yang datang. Seluruh pesanan masih ditunggu.';
                            }

                            return $jumlahPenerimaan.' kali penerimaan tercatat, senilai '
                                .Rupiah::penuh($record->totalDiterima()).'. '
                                .($belum === 0
                                    ? 'Seluruh barang sudah datang.'
                                    : $belum.' jenis barang belum datang seluruhnya.');
                        }),
                    TextEntry::make('closing_reason')
                        ->label('Alasan ditutup sebelum lengkap')
                        ->columnSpanFull()
                        ->visible(fn (SupplyPurchase $record): bool => filled($record->closing_reason)),
                    TextEntry::make('rejection_reason')
                        ->label('Alasan ditolak')
                        ->columnSpanFull()
                        ->visible(fn (SupplyPurchase $record): bool => filled($record->rejection_reason)),
                    TextEntry::make('tagihan')
                        ->label('Tagihan yang menunjuk pembelian ini')
                        ->columnSpanFull()
                        ->state(function (SupplyPurchase $record): string {
                            $tagihan = $record->bills()->get();

                            if ($tagihan->isEmpty()) {
                                return 'Belum ada. Tagihan rekanan bisa menunjuk pembelian ini lewat kolom Pesanan pembelian di layar tagihan, dan layar itu lalu menyebutkan sendiri selisihnya terhadap barang yang sudah datang.';
                            }

                            return $tagihan
                                ->map(fn ($t): string => $t->code.' senilai '.$t->totalLabel().' ('.$t->statusLabel().')')
                                ->implode('. ').'.';
                        }),
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
                TextColumn::make('pemasok')
                    ->label('Pemasok')
                    ->state(fn (SupplyPurchase $record): string => $record->pemasokLabel())
                    ->description(fn (SupplyPurchase $record): string => $record->kindLabel())
                    ->wrap(),
                TextColumn::make('description')
                    ->label('Untuk keperluan apa')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->state(fn (SupplyPurchase $record): string => $record->totalLabel())
                    ->description(fn (SupplyPurchase $record): string => $record->jenisLabel())
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (SupplyPurchase $record): string => $record->statusLabel())
                    ->color(fn (SupplyPurchase $record): string => $record->statusColor())
                    ->description(fn (SupplyPurchase $record): string => $record->tahapLabel())
                    ->sortable(),
                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (SupplyPurchase $record): string => $record->kind === 'pesanan'
                        ? $record->janjiLabel()
                        : 'Pembelian langsung')
                    ->sortable(),
                TextColumn::make('diterima')
                    ->label('Sudah diterima')
                    ->state(fn (SupplyPurchase $record): string => Rupiah::penuh($record->totalDiterima()))
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vendor.name')
                    ->label('Rekanan terdaftar')
                    ->placeholder('Bukan rekanan terdaftar')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['vendor', 'lines.item', 'lines.receiptLines'])
                ->orderByRaw("case when status in ('draft','diajukan','disetujui','diterima_sebagian') then 0 else 1 end"))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_persetujuan')
                    ->label('Menunggu persetujuan')
                    ->query(fn (Builder $query): Builder => $query->menungguPersetujuan()),
                Filter::make('menunggu_barang')
                    ->label('Menunggu barang datang')
                    ->query(fn (Builder $query): Builder => $query->menungguBarang()),
                SelectFilter::make('kind')
                    ->label('Cara membeli')
                    ->options(SupplyPurchase::KINDS),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(SupplyPurchase::STATUSES)
                    ->multiple(),
                SelectFilter::make('vendor_id')
                    ->label('Rekanan')
                    ->relationship('vendor', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                static::ajukanAction(),
                static::catatLangsungAction(),
                static::setujuiAction(),
                static::terimaAction(),
                static::tutupAction(),
                static::tolakAction(),
                static::perbaikiAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyPurchase $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (SupplyPurchase $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada pembelian ATK yang tercatat')
            ->emptyStateDescription('Dua cara dicatat di sini. Pesanan resmi disusun, disetujui manajer, lalu barangnya datang bertahap. Pembelian langsung dicatat setelah barangnya sudah di tangan. Keduanya menambah stok lewat penerimaan, jadi tidak ada lagi barang masuk yang diketik tanpa asal usul.');
    }

    // ------------------------------------------------------------------ tindakan

    protected static function segarkan(mixed $livewire, SupplyPurchase $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    public static function ajukanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('ajukan')
            ->label('Submit for Approval')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn (SupplyPurchase $record): bool => $record->kind === 'pesanan'
                && $record->status === 'draft'
                && static::allows('update'))
            ->modalHeading(fn (SupplyPurchase $record): string => 'Submit '.$record->code)
            ->modalDescription(function (SupplyPurchase $record): string {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    return $alasan;
                }

                return $record->totalLabel().' untuk '.$record->jenisLabel().' dari '
                    .$record->pemasokLabel()
                    .'. Setelah disetujui manajer, pesanan boleh dikirim ke rekanan dan barangnya ditunggu. Stok belum bertambah sampai barangnya benar benar datang.';
            })
            ->modalSubmitActionLabel('Submit')
            ->action(function (SupplyPurchase $record, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    Notification::make()->warning()->title('Belum bisa diajukan')->body($alasan)->persistent()->send();

                    $action->halt();
                }

                $record->ajukan();
                $record->refresh();

                Notification::make()->success()->title($record->code.' diajukan')
                    ->body('Menunggu manajer GA menyetujui.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Mencatat pembelian langsung sekaligus memasukkan barangnya ke gudang.
     *
     * Satu tombol, bukan tiga, karena ketiga langkah itu sudah terjadi semua di dunia nyata
     * sebelum orang membuka layar ini.
     */
    public static function catatLangsungAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('catat_langsung')
            ->label('Record and Receive')
            ->icon('heroicon-o-inbox-arrow-down')
            ->color('success')
            ->visible(fn (SupplyPurchase $record): bool => $record->kind === 'langsung'
                && $record->status === 'draft'
                && static::allows('receive'))
            ->modalHeading(fn (SupplyPurchase $record): string => 'Record and Receive '.$record->code)
            ->modalDescription(function (SupplyPurchase $record): string {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    return $alasan;
                }

                return $record->totalLabel().' untuk '.$record->jenisLabel().' dari '
                    .$record->pemasokLabel()
                    .'. Seluruh barangnya langsung masuk gudang dengan jumlah yang tertulis di daftar, stok bertambah, dan harga pembelian terakhir tiap barang ikut diperbarui. Pembelian langsung tidak melewati persetujuan karena uangnya memang sudah keluar.';
            })
            ->modalSubmitActionLabel('Record and Add to Stock')
            ->schema([
                TextInput::make('delivery_note_number')
                    ->label('Nomor nota')
                    ->maxLength(80)
                    ->placeholder('Tidak dicatat')
                    ->helperText('Nomor nota atau struk toko. Ini yang dicari saat angkanya dicocokkan nanti.'),
                Textarea::make('notes')
                    ->label('Catatan penerimaan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan.'),
            ])
            ->action(function (SupplyPurchase $record, array $data, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    Notification::make()->warning()->title('Belum bisa dicatat')->body($alasan)->persistent()->send();

                    $action->halt();
                }

                $record->load('lines');

                if (! $record->catatDanTerima(
                    $record->order_date->toDateString(),
                    $data['delivery_note_number'] ?? null,
                    $data['notes'] ?? null,
                )) {
                    static::peringatanStatusBerubah();

                    return;
                }

                $record->refresh();

                Notification::make()->success()->title($record->code.' dicatat dan masuk gudang')
                    ->body('Stok sudah bertambah, dan harga pembelian terakhir tiap barang ikut diperbarui.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function setujuiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('setujui')
            ->label('Approve Purchase Order')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (SupplyPurchase $record): bool => $record->status === 'diajukan' && static::allows('approve'))
            ->modalHeading(fn (SupplyPurchase $record): string => 'Approve '.$record->code)
            ->modalDescription(fn (SupplyPurchase $record): string => $record->totalLabel().' untuk '
                .$record->jenisLabel().' dari '.$record->pemasokLabel()
                .'. Yang Anda setujui adalah bahwa pesanan ini boleh dikirim ke rekanan. Stok belum bertambah dan belum ada uang yang keluar sampai barangnya datang dan fakturnya dibayar.')
            ->modalSubmitActionLabel('Approve Purchase Order')
            ->schema([
                Textarea::make('approval_note')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Boleh dikosongkan.'),
            ])
            ->action(function (SupplyPurchase $record, array $data, $livewire): void {
                if (! $record->setujui($data['approval_note'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' disetujui')
                    ->body('Pesanan boleh dikirim ke rekanan, dan barangnya sekarang ditunggu.')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Menerima barang datang, seluruhnya atau sebagian.
     *
     * Kotak dialognya berisi satu baris per barang yang belum lengkap, dengan jumlah yang
     * sudah terisi sisa yang belum datang. Yang paling sering terjadi adalah barangnya datang
     * lengkap, dan untuk keadaan itu orang cukup menekan tombol tanpa mengetik apa pun.
     */
    public static function terimaAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('terima')
            ->label('Receive Goods')
            ->icon('heroicon-o-truck')
            ->color('success')
            ->visible(fn (SupplyPurchase $record): bool => $record->kind === 'pesanan'
                && in_array($record->status, SupplyPurchase::RECEIVABLE_STATUSES, true)
                && static::allows('receive'))
            ->modalHeading(fn (SupplyPurchase $record): string => 'Receive Goods for '.$record->code)
            ->modalDescription(fn (SupplyPurchase $record): string => 'Isi jumlah yang benar benar datang hari ini. Bawaannya sisa yang belum datang, jadi kalau kirimannya lengkap tidak ada yang perlu diubah. Barang yang belum datang sama sekali diisi nol, dan barisnya tetap menunggu kiriman berikutnya. Setelah disimpan, stok bertambah dan harga pembelian terakhir tiap barang ikut diperbarui.')
            ->modalSubmitActionLabel('Receive and Add to Stock')
            ->fillForm(fn (SupplyPurchase $record): array => [
                'receipt_date' => now()->toDateString(),
                'lines' => $record->lines()->with('item', 'receiptLines')->get()
                    ->filter(fn (SupplyPurchaseLine $b): bool => $b->sisa() > 0)
                    ->map(fn (SupplyPurchaseLine $b): array => [
                        'line_id' => $b->getKey(),
                        'nama' => ($b->item?->name ?? 'Barang yang sudah dihapus')
                            .', sisa '.$b->formatJumlah($b->sisa()).' dari '.$b->dipesanLabel(),
                        'quantity' => $b->sisa(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->schema([
                DatePicker::make('receipt_date')
                    ->label('Tanggal barang datang')
                    ->displayFormat('d M Y')
                    ->maxDate(now())
                    ->required()
                    ->helperText('Tanggal inilah yang tercatat di mutasi stoknya, bukan tanggal Anda mengisi layar ini.'),
                TextInput::make('delivery_note_number')
                    ->label('Nomor surat jalan')
                    ->maxLength(80)
                    ->placeholder('Tidak dicatat')
                    ->helperText('Boleh dikosongkan. Pemasok kecil kadang memang tidak memberi nomor.'),
                Repeater::make('lines')
                    ->label('Barang yang datang')
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columns(2)
                    ->schema([
                        // Hidden, bukan TextInput yang disembunyikan, karena id baris memang
                        // bukan sesuatu yang boleh diketik orang dan hanya perlu ikut
                        // terkirim supaya jumlahnya jatuh ke baris pesanan yang benar.
                        Hidden::make('line_id'),
                        Hidden::make('nama'),
                        /*
                         * Nama penampungnya sengaja berbeda dari kunci datanya.
                         *
                         * Versi pertama memberi Placeholder ini nama `nama` dan membaca
                         * `$get('nama')` di dalamnya, dan itu membuat penampung membaca
                         * dirinya sendiri: mengambil isinya memanggil closure content, yang
                         * memanggil pengambilan isinya lagi, terus menerus sampai memori PHP
                         * habis dan seluruh permintaan mati dengan galat 500. Kuncinya
                         * sekarang dipegang Hidden di atas, dan penampung ini hanya membacanya.
                         */
                        Placeholder::make('barang')
                            ->label('Barang')
                            ->content(fn ($get): string => (string) $get('nama')),
                        TextInput::make('quantity')
                            ->label('Jumlah datang')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required(),
                    ]),
                Textarea::make('notes')
                    ->label('Catatan penerimaan')
                    ->rows(2)
                    ->maxLength(500)
                    ->columnSpanFull()
                    ->placeholder('Contoh: satu box kertas basah kena hujan, ditukar minggu depan.'),
            ])
            ->action(function (SupplyPurchase $record, array $data, Action $action, $livewire): void {
                $jumlah = [];

                foreach ($data['lines'] ?? [] as $baris) {
                    $jumlah[(int) $baris['line_id']] = (int) $baris['quantity'];
                }

                $masalah = $record->masalahPenerimaan($jumlah);

                if ($masalah !== []) {
                    Notification::make()
                        ->warning()
                        ->title('Jumlahnya melebihi sisa pesanan')
                        ->body(implode('. ', $masalah).'.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                if (array_sum($jumlah) <= 0) {
                    Notification::make()
                        ->warning()
                        ->title('Tidak ada barang yang datang')
                        ->body('Seluruh barisnya berisi nol. Isi minimal satu barang supaya ada yang bisa dicatat masuk gudang.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                $penerimaan = $record->terima(
                    $data['receipt_date'],
                    $jumlah,
                    $data['delivery_note_number'] ?? null,
                    $data['notes'] ?? null,
                );

                if ($penerimaan === null) {
                    static::peringatanStatusBerubah();

                    return;
                }

                $record->refresh()->load('lines.receiptLines');

                Notification::make()
                    ->success()
                    ->title($penerimaan->code.' tercatat')
                    ->body('Stok sudah bertambah. '
                        .($record->barisBelumLengkap() === 0
                            ? 'Seluruh barang pesanan ini sudah datang.'
                            : $record->barisBelumLengkap().' jenis barang masih ditunggu.'))
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function tutupAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('tutup')
            ->label('Close Order')
            ->icon('heroicon-o-archive-box-arrow-down')
            ->color('gray')
            ->visible(fn (SupplyPurchase $record): bool => in_array($record->status, ['disetujui', 'diterima_sebagian'], true)
                && $record->barisBelumLengkap() > 0
                && static::allows('receive'))
            ->modalHeading(fn (SupplyPurchase $record): string => 'Close '.$record->code)
            ->modalDescription(fn (SupplyPurchase $record): string => $record->barisBelumLengkap()
                .' jenis barang belum datang seluruhnya. Menutup pesanan berarti sisanya memang tidak akan datang lagi. Sisa yang batal itu tetap terbaca di daftar barang, jadi tidak ada catatan yang hilang, dan stok tidak tersentuh.')
            ->modalSubmitActionLabel('Close Order')
            ->schema([
                Textarea::make('closing_reason')
                    ->label('Alasan ditutup')
                    ->rows(2)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: sisa dua box dibatalkan rekanan karena stoknya kosong sampai akhir tahun.'),
            ])
            ->action(function (SupplyPurchase $record, array $data, $livewire): void {
                if (! $record->tutup($data['closing_reason'])) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' ditutup')
                    ->body('Sisanya tercatat tidak jadi datang, dan pesanan ini keluar dari daftar yang masih ditunggu.')->send();

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
            ->visible(fn (SupplyPurchase $record): bool => $record->status === 'diajukan' && static::allows('approve'))
            ->modalHeading(fn (SupplyPurchase $record): string => 'Reject '.$record->code)
            ->modalDescription('Pesanan yang ditolak tetap tersimpan beserta alasannya, dan tim GA bisa mengembalikannya ke draf untuk memperbaiki daftarnya. Tidak ada yang dikirim ke rekanan.')
            ->modalSubmitActionLabel('Reject Purchase Order')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan ditolak')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: harga kertas di atas pagu, minta penawaran dari rekanan lain dulu.'),
            ])
            ->action(function (SupplyPurchase $record, array $data, $livewire): void {
                if (! $record->tolak($data['rejection_reason'])) {
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
            ->visible(fn (SupplyPurchase $record): bool => $record->status === 'ditolak' && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyPurchase $record): string => 'Return '.$record->code.' to Draft')
            ->modalDescription('Daftar barangnya bisa diubah lagi, lalu diajukan ulang dari awal. Alasan penolakannya sengaja tetap tersimpan supaya bisa dibaca sambil memperbaiki.')
            ->modalSubmitActionLabel('Return to Draft')
            ->action(function (SupplyPurchase $record, $livewire): void {
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
            ->visible(fn (SupplyPurchase $record): bool => $record->isOpen()
                && ! $record->adaYangSudahDiterima()
                && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (SupplyPurchase $record): string => 'Cancel '.$record->code)
            ->modalDescription('Pembelian ini tetap tersimpan sebagai catatan. Stok tidak tersentuh, karena belum ada satu pun barangnya yang masuk gudang.')
            ->modalSubmitActionLabel('Cancel Purchase')
            ->action(function (SupplyPurchase $record, $livewire): void {
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
            ->body('Pembelian ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->send();
    }

    // ------------------------------------------------------------------ perizinan

    /**
     * Isi pembelian hanya bisa diubah selama masih draf. Setelah diajukan, mengubahnya
     * berarti mengubah angka yang sedang atau sudah ditandatangani orang lain tanpa ia tahu,
     * dan setelah barangnya datang berarti mengubah dasar perhitungan stok yang sudah jadi.
     */
    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record) && $record->status === 'draft';
    }

    /**
     * Pembelian yang barangnya sudah pernah masuk gudang tidak boleh dihapus, karena mutasi
     * stoknya sudah lahir dan menghapusnya memutus tautan itu: barang masuk tetap ada di buku
     * stok tetapi tidak lagi bisa dijelaskan datangnya dari mana.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record)
            && in_array($record->status, ['draft', 'ditolak', 'dibatalkan'], true)
            && ! $record->adaYangSudahDiterima();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LinesRelationManager::class,
            RelationManagers\ReceiptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSupplyPurchases::route('/'),
            'create' => CreateSupplyPurchase::route('/create'),
            'view' => ViewSupplyPurchase::route('/{record}'),
        ];
    }
}
