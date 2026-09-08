<?php

namespace App\Filament\Resources\VendorBills;

use App\Filament\Resources\SupplyPurchases\SupplyPurchaseResource;
use App\Filament\Resources\VendorBills\Pages\CreateVendorBill;
use App\Filament\Resources\VendorBills\Pages\ListVendorBills;
use App\Filament\Resources\VendorBills\Pages\ViewVendorBill;
use App\Models\SupplyPurchase;
use App\Models\Vendor;
use App\Models\VendorBill;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Support\Carbon;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/**
 * Tagihan rekanan.
 *
 * Layar ini punya dua pembaca yang berbeda kepentingan. Staf GA membacanya sebagai
 * tumpukan faktur yang harus dibereskan sebelum jatuh tempo, dan yang ia butuhkan adalah
 * urutan menurut jatuh tempo beserta peringatan mana yang sudah lewat. Manajer membacanya
 * sebagai antrean tanda tangan, dan yang ia butuhkan adalah nilai dan pembebanannya
 * dalam satu tarikan mata.
 *
 * Karena itu nilai tagihan tidak pernah tampil sendirian tanpa rinciannya: kolom Nilai
 * selalu membawa keterangan dibebankan ke mana. Tagihan yang belum punya rincian
 * mengatakannya apa adanya dan tidak bisa diajukan, karena tanda tangan untuk nol rupiah
 * yang berubah sendiri setelahnya adalah cacat yang tidak bisa diperbaiki belakangan.
 */
class VendorBillResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = VendorBill::class;

    protected static string $moduleCode = 'vendor_bills';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static string|UnitEnum|null $navigationGroup = 'Budget & Expenses';

    protected static ?string $navigationLabel = 'Vendor Bills';

    protected static ?string $modelLabel = 'vendor bill';

    protected static ?string $pluralModelLabel = 'vendor bills';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'code';

    /**
     * Lencana menghitung yang menunggu tindakan orang yang sedang masuk: antrean tanda
     * tangan untuk yang boleh menyetujui, antrean pembayaran untuk yang boleh menandai
     * lunas. Yang tidak boleh keduanya tidak diberi angka, karena angka yang tidak bisa
     * ditindaklanjuti hanya menjadi noda merah permanen di menu.
     */
    public static function getNavigationBadge(): ?string
    {
        $jumlah = static::antrianSaya()->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return VendorBill::query()->terlambat()->exists() ? 'danger' : 'warning';
    }

    protected static function antrianSaya(): Builder
    {
        $query = VendorBill::query();

        $bisaSetujui = static::allows('approve');
        $bisaBayar = static::allows('pay');

        if (! $bisaSetujui && ! $bisaBayar) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $sub) use ($bisaSetujui, $bisaBayar): void {
            if ($bisaSetujui) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPersetujuan());
            }

            if ($bisaBayar) {
                $sub->orWhere(fn (Builder $q) => $q->menungguPembayaran());
            }
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Select::make('vendor_id')
                        ->label('Rekanan')
                        ->options(fn (): array => Vendor::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (Vendor $v) => [$v->id => $v->pickerLabel()])
                            ->all())
                        ->searchable()
                        ->required()
                        ->live(),

                    /*
                     * Pesanan pembelian yang ditagih faktur ini. Boleh dikosongkan, dan
                     * sebagian besar tagihan GA memang kosong: listrik, sewa gedung, dan jasa
                     * kebersihan datang tanpa didahului pesanan barang.
                     *
                     * Daftarnya disempitkan ke pesanan milik rekanan yang dipilih, karena
                     * menagihkan pesanan rekanan lain adalah kesalahan yang tidak mungkin
                     * disengaja dan sangat mungkin terjadi kalau seluruh pesanan ditampilkan.
                     */
                    Select::make('supply_purchase_id')
                        ->label('Pesanan pembelian')
                        ->options(function ($get): array {
                            if (blank($get('vendor_id'))) {
                                return [];
                            }

                            return SupplyPurchase::query()
                                ->where('vendor_id', $get('vendor_id'))
                                ->whereIn('status', ['disetujui', 'diterima_sebagian', 'selesai'])
                                ->orderByDesc('order_date')
                                ->with('lines')
                                ->get()
                                ->mapWithKeys(fn (SupplyPurchase $p): array => [
                                    $p->getKey() => $p->code.', '.$p->description.', '.$p->totalLabel(),
                                ])
                                ->all();
                        })
                        ->searchable()
                        ->placeholder(fn ($get): string => blank($get('vendor_id'))
                            ? 'Pilih rekanannya lebih dulu'
                            : 'Tidak menagih pesanan pembelian')
                        ->helperText('Boleh dikosongkan, dan biasanya memang kosong. Isi hanya kalau faktur ini menagih pembelian ATK yang sudah dicatat di menu Supply Purchases, dan layar ini lalu menyebutkan sendiri selisihnya terhadap barang yang benar benar sudah datang.'),
                    TextInput::make('invoice_number')
                        ->label('Nomor faktur rekanan')
                        ->maxLength(80)
                        ->placeholder('Tidak dicatat')
                        ->helperText('Nomor yang tertulis di fakturnya. Nomor internal GA dibuat sendiri oleh aplikasi.'),
                    DatePicker::make('invoice_date')
                        ->label('Tanggal faktur')
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required()
                        ->live()
                        ->helperText(fn ($get): string => filled($get('invoice_date'))
                            ? 'Tagihan ini membebani anggaran tahun '.Carbon::parse($get('invoice_date'))->year.'. Tanggal faktur yang menentukan, bukan tanggal bayar.'
                            : 'Tanggal ini yang menentukan tahun anggaran mana yang terbebani, bukan tanggal bayarnya.'),
                    DatePicker::make('due_date')
                        ->label('Jatuh tempo')
                        ->displayFormat('d M Y')
                        ->placeholder('Tidak dicatat')
                        ->helperText('Boleh dikosongkan, tetapi tanpa tanggal ini tagihan tidak bisa diingatkan sebelum telat.'),
                    Textarea::make('description')
                        ->label('Untuk apa')
                        ->rows(2)
                        ->required()
                        ->maxLength(500)
                        ->columnSpanFull()
                        ->placeholder('Contoh: tagihan listrik gedung Head Office periode Agustus 2026.'),
                    FileUpload::make('file_path')
                        ->label('Pindaian faktur')
                        ->disk('public')
                        ->directory('tagihan-rekanan')
                        ->visibility('public')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(10240)
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull()
                        ->storeFileNamesIn('original_name')
                        ->helperText('Foto atau PDF, maksimum 10 MB. Yang menyetujui membaca angka di sini, bukan hanya angka yang diketik ulang.'),
                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Contoh: faktur asli dititipkan ke bagian finance pada 5 September.'),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice')
                ->columns(3)
                ->schema([
                    TextEntry::make('code')->label('Nomor internal')->fontFamily('mono'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->state(fn (VendorBill $record): string => $record->statusLabel())
                        ->color(fn (VendorBill $record): string => $record->statusColor()),
                    TextEntry::make('nilai')
                        ->label('Nilai')
                        ->state(fn (VendorBill $record): string => $record->totalLabel())
                        ->helperText(fn (VendorBill $record): string => 'Dijumlahkan dari '
                            .$record->lines()->count().' baris rincian di bawah.'),
                    TextEntry::make('vendor.name')->label('Rekanan')->placeholder('Rekanan sudah dihapus'),
                    TextEntry::make('invoice_number')->label('Nomor faktur rekanan')->placeholder('Tidak dicatat'),
                    TextEntry::make('invoice_date')
                        ->label('Tanggal faktur')
                        ->date('d F Y')
                        ->helperText(fn (VendorBill $record): string => 'Membebani anggaran tahun '.$record->tahunAnggaran().'.'),
                    TextEntry::make('due_date')
                        ->label('Jatuh tempo')
                        ->state(fn (VendorBill $record): string => $record->due_date?->translatedFormat('d F Y') ?? 'Tidak dicatat')
                        ->helperText(fn (VendorBill $record): string => $record->jatuhTempoLabel())
                        ->color(fn (VendorBill $record): string => $record->jatuhTempoColor()),
                    TextEntry::make('description')->label('Untuk apa')->columnSpan(2),
                    /*
                     * Muncul hanya kalau tagihan ini memang menunjuk pesanan. Yang
                     * ditampilkan bukan angka telanjang melainkan kalimat, karena angka
                     * telanjang di layar persetujuan hanya melahirkan pertanyaan berikutnya,
                     * dan orang yang sedang menandatangani tidak sempat mencarinya sendiri.
                     */
                    TextEntry::make('selisih_pesanan')
                        ->label('Dibandingkan barang yang sudah datang')
                        ->columnSpanFull()
                        ->visible(fn (VendorBill $record): bool => $record->purchase !== null)
                        ->state(fn (VendorBill $record): string => ($record->purchase?->code ?? '')
                            .'. '.($record->selisihLabel() ?? ''))
                        ->color(fn (VendorBill $record): ?string => $record->selisihColor())
                        ->url(fn (VendorBill $record): ?string => $record->purchase !== null
                            ? SupplyPurchaseResource::getUrl('view', ['record' => $record->purchase])
                            : null),
                    TextEntry::make('file_path')
                        ->label('Pindaian faktur')
                        ->state(fn (VendorBill $record): string => filled($record->file_path)
                            ? ($record->original_name ?? 'Berkas tersimpan').', '.$record->sizeLabel()
                            : 'Tidak ada pindaian yang diunggah')
                        ->url(fn (VendorBill $record): ?string => filled($record->file_path)
                            ? Storage::disk('public')->url($record->file_path)
                            : null)
                        ->openUrlInNewTab(),
                    TextEntry::make('notes')->label('Catatan')->placeholder('Tidak ada')->columnSpanFull(),
                ]),

            Section::make('Approval & Payment')
                ->columns(2)
                ->schema([
                    TextEntry::make('persetujuan')
                        ->label('Persetujuan')
                        ->state(function (VendorBill $record): string {
                            if ($record->status === 'draft') {
                                return 'Masih draf. Rincian pembebanannya masih bisa diubah, dan tagihan ini belum masuk hitungan anggaran mana pun.';
                            }

                            if ($record->status === 'diajukan') {
                                return 'Menunggu persetujuan. Nilainya belum masuk realisasi anggaran, tetapi sudah tampil sebagai angka yang menunggu di layar anggaran.';
                            }

                            if (blank($record->approved_at)) {
                                return 'Belum disetujui.';
                            }

                            return 'Disetujui '.($record->approvedByUser?->name ?? 'pengguna yang sudah dihapus')
                                .' pada '.$record->approved_at->translatedFormat('d F Y, H:i').'.';
                        }),
                    TextEntry::make('pembayaran')
                        ->label('Pembayaran')
                        ->state(function (VendorBill $record): string {
                            if ($record->status === 'dibayar') {
                                return 'Dibayar '.($record->paid_date?->translatedFormat('d F Y') ?? 'tanggalnya tidak dicatat')
                                    .' oleh '.($record->paidByUser?->name ?? 'pengguna yang sudah dihapus')
                                    .(filled($record->payment_reference) ? '. Bukti: '.$record->payment_reference : '.');
                            }

                            return $record->status === 'disetujui'
                                ? 'Belum dibayar. '.$record->jatuhTempoLabel().'.'
                                : 'Belum sampai tahap pembayaran.';
                        }),
                    TextEntry::make('rejection_reason')
                        ->label('Alasan ditolak')
                        ->columnSpanFull()
                        ->visible(fn (VendorBill $record): bool => filled($record->rejection_reason)),
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
                    ->description(fn (VendorBill $record): ?string => $record->invoice_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vendor.name')
                    ->label('Rekanan')
                    ->description(fn (VendorBill $record): string => str($record->description)->limit(60)->value())
                    ->searchable()
                    ->wrap(),
                TextColumn::make('invoice_date')
                    ->label('Tanggal faktur')
                    ->date('d M Y')
                    ->description(fn (VendorBill $record): string => 'Anggaran '.$record->tahunAnggaran())
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->state(fn (VendorBill $record): string => $record->due_date?->translatedFormat('d M Y') ?? 'Tidak dicatat')
                    ->description(fn (VendorBill $record): string => $record->jatuhTempoLabel())
                    ->color(fn (VendorBill $record): string => $record->jatuhTempoColor())
                    ->sortable(),
                // Nilai tidak pernah tampil sendirian. Angka tanpa keterangan dibebankan
                // ke mana adalah persis angka yang orang salah pakai saat menyusun
                // anggaran tahun berikutnya.
                TextColumn::make('total')
                    ->label('Nilai')
                    ->state(fn (VendorBill $record): string => $record->totalLabel())
                    ->description(fn (VendorBill $record): string => $record->pembebananLabel())
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (VendorBill $record): string => $record->statusLabel())
                    ->color(fn (VendorBill $record): string => $record->statusColor())
                    ->sortable(),
                TextColumn::make('paid_date')
                    ->label('Dibayar')
                    ->date('d M Y')
                    ->placeholder('Belum dibayar')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_reference')
                    ->label('Bukti pembayaran')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dicatat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Yang paling dekat jatuh tempo ada di atas. Tagihan tanpa tanggal jatuh tempo
            // turun ke bawah karena memang tidak ada yang mengejarnya.
            ->defaultSort('due_date', 'asc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['vendor', 'lines.department'])
                ->orderByRaw("case when status in ('draft','diajukan','disetujui') then 0 else 1 end"))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('terbuka')
                    ->label('Hanya yang belum selesai')
                    ->query(fn (Builder $query): Builder => $query->terbuka()),
                Filter::make('menunggu_persetujuan')
                    ->label('Menunggu persetujuan')
                    ->query(fn (Builder $query): Builder => $query->menungguPersetujuan()),
                Filter::make('menunggu_pembayaran')
                    ->label('Menunggu pembayaran')
                    ->query(fn (Builder $query): Builder => $query->menungguPembayaran()),
                Filter::make('terlambat')
                    ->label('Jatuh temponya sudah lewat')
                    ->query(fn (Builder $query): Builder => $query->terlambat()),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(VendorBill::STATUSES)
                    ->multiple(),
                SelectFilter::make('vendor_id')
                    ->label('Rekanan')
                    ->relationship('vendor', 'name')
                    ->searchable(),
            ])
            ->recordActions([
                static::ajukanAction(),
                static::setujuiAction(),
                static::tolakAction(),
                static::bayarAction(),
                static::perbaikiAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (VendorBill $record): bool => static::canEdit($record)),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (VendorBill $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada tagihan rekanan')
            ->emptyStateDescription('Catat faktur yang masuk beserta pembebanannya ke departemen dan kategori biaya. Setelah disetujui, nilainya masuk sendiri ke realisasi anggaran, dan tidak ada yang perlu mengetik ulang angkanya di layar anggaran.');
    }

    // ------------------------------------------------------------------ tindakan

    public static function ajukanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('ajukan')
            ->label('Submit')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn (VendorBill $record): bool => $record->status === 'draft' && static::allows('update'))
            ->modalHeading(fn (VendorBill $record): string => 'Submit '.$record->code)
            ->modalDescription(fn (VendorBill $record): string => $record->alasanBelumBisaDiajukan()
                // Nama departemen tidak dikecilkan hurufnya. Departemen adalah nama diri,
                // dan "dibebankan ke finance" terbaca seperti salah ketik.
                ?? 'Nilai yang diajukan '.$record->totalLabel().', dibebankan ke '.$record->pembebananLabel()
                    .'. Setelah diajukan, rinciannya tidak bisa diubah lagi sampai tagihan ini ditolak dan dikembalikan ke draf.')
            ->modalSubmitActionLabel('Submit for Approval')
            ->action(function (VendorBill $record, Action $action, $livewire): void {
                $alasan = $record->alasanBelumBisaDiajukan();

                if ($alasan !== null) {
                    Notification::make()
                        ->warning()
                        ->title('Belum bisa diajukan')
                        ->body($alasan)
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                $record->ajukan();

                Notification::make()
                    ->success()
                    ->title($record->code.' diajukan')
                    ->body('Nilainya sudah tampil di layar anggaran sebagai angka yang menunggu persetujuan.')
                    ->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function setujuiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('setujui')
            ->label('Approve')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn (VendorBill $record): bool => $record->status === 'diajukan' && static::allows('approve'))
            ->requiresConfirmation()
            ->modalHeading(fn (VendorBill $record): string => 'Approve '.$record->code)
            /*
             * Selisih terhadap barang yang sudah datang disebut di sini, bukan hanya di
             * halaman lihat, karena inilah satu satunya saat orang benar benar membacanya:
             * detik sebelum ia menandatangani. Menaruhnya hanya di halaman lihat berarti
             * mengandalkan orang membuka halaman itu lebih dulu, dan hampir tidak ada yang
             * melakukannya kalau tombol setujui sudah kelihatan dari daftar.
             */
            ->modalDescription(function (VendorBill $record): string {
                $pokok = $record->totalLabel().' dari '
                    .($record->vendor?->name ?? 'rekanan yang sudah dihapus').', dibebankan ke '
                    .$record->pembebananLabel().'. Setelah disetujui, nilainya masuk ke realisasi anggaran tahun '
                    .$record->tahunAnggaran().'.';

                $selisih = $record->selisihLabel();

                return $selisih === null
                    ? $pokok
                    : $pokok.' Tagihan ini menagih pesanan '.$record->purchase->code.'. '.$selisih;
            })
            ->modalSubmitActionLabel('Approve Bill')
            ->action(function (VendorBill $record, $livewire): void {
                if (! $record->setujui()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title($record->code.' disetujui')
                    ->body('Nilainya sekarang terhitung sebagai realisasi anggaran, dan tagihannya masuk antrean pembayaran.')
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
            ->visible(fn (VendorBill $record): bool => $record->status === 'diajukan' && static::allows('approve'))
            ->modalHeading(fn (VendorBill $record): string => 'Reject '.$record->code)
            ->modalDescription('Tagihan yang ditolak tetap tersimpan beserta alasannya, dan bisa dikembalikan ke draf untuk diperbaiki.')
            ->modalSubmitActionLabel('Reject Bill')
            ->schema([
                Textarea::make('rejection_reason')
                    ->label('Alasan ditolak')
                    ->rows(3)
                    ->required()
                    ->maxLength(500)
                    ->placeholder('Contoh: pembebanan ke departemen Finance seharusnya masuk kategori sewa, bukan rumah tangga kantor.'),
            ])
            ->action(function (VendorBill $record, array $data, $livewire): void {
                if (! $record->tolak($data['rejection_reason'])) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' ditolak')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Mengembalikan tagihan yang ditolak ke draf.
     *
     * Tanpa tombol ini, tagihan yang ditolak menjadi jalan buntu dan orang akan membuat
     * tagihan kedua untuk faktur yang sama. Dua baris untuk satu faktur adalah cara
     * paling mudah membuat realisasi terhitung dua kali.
     */
    public static function perbaikiAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('perbaiki')
            ->label('Return to Draft')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->visible(fn (VendorBill $record): bool => $record->status === 'ditolak' && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (VendorBill $record): string => 'Return '.$record->code.' to Draft')
            ->modalDescription('Rinciannya bisa diubah lagi, lalu diajukan ulang. Alasan penolakannya sengaja tetap tersimpan supaya bisa dibaca sambil memperbaiki.')
            ->modalSubmitActionLabel('Return to Draft')
            ->action(function (VendorBill $record, $livewire): void {
                if (! $record->kembalikanKeDraft()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' kembali menjadi draf')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Membatalkan tagihan yang tidak jadi dibayar, misalnya karena rekanan menarik
     * fakturnya. Bukan menghapus: fakturnya pernah ada di meja GA, dan riwayat itu yang
     * dicari saat rekanan menagihnya lagi enam bulan kemudian.
     */
    public static function batalkanAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('batalkan')
            ->label('Cancel')
            ->icon('heroicon-o-x-circle')
            ->color('gray')
            ->visible(fn (VendorBill $record): bool => $record->isOpen() && static::allows('update'))
            ->requiresConfirmation()
            ->modalHeading(fn (VendorBill $record): string => 'Cancel '.$record->code)
            ->modalDescription(fn (VendorBill $record): string => $record->status === 'disetujui'
                ? 'Tagihan ini sudah disetujui, jadi nilainya sedang terhitung sebagai realisasi anggaran. Membatalkannya mengeluarkan nilai itu dari realisasi tahun '.$record->tahunAnggaran().'.'
                : 'Tagihan ini tetap tersimpan sebagai catatan bahwa fakturnya pernah masuk, dan tidak masuk hitungan anggaran mana pun.')
            ->modalSubmitActionLabel('Cancel Bill')
            ->action(function (VendorBill $record, $livewire): void {
                if (! $record->batalkan()) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' dibatalkan')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    public static function bayarAction(bool $iconOnly = true): Action
    {
        $aksi = Action::make('bayar')
            ->label('Mark as Paid')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->visible(fn (VendorBill $record): bool => $record->status === 'disetujui' && static::allows('pay'))
            ->modalHeading(fn (VendorBill $record): string => 'Mark '.$record->code.' as Paid')
            ->modalDescription(fn (VendorBill $record): string => $record->totalLabel().' ke '
                .($record->vendor?->name ?? 'rekanan yang sudah dihapus')
                .'. Realisasi anggarannya tidak berubah, karena nilainya sudah terhitung sejak disetujui.')
            ->modalSubmitActionLabel('Save Payment')
            ->schema([
                DatePicker::make('paid_date')
                    ->label('Tanggal bayar')
                    ->displayFormat('d M Y')
                    ->default(now())
                    ->maxDate(now())
                    ->required(),
                TextInput::make('payment_reference')
                    ->label('Bukti pembayaran')
                    ->maxLength(80)
                    ->placeholder('Tidak dicatat')
                    ->helperText('Nomor transfer, nomor cek, atau nomor voucher. Ini yang dicari saat rekanan menagih ulang faktur yang sudah dibayar.'),
            ])
            ->action(function (VendorBill $record, array $data, $livewire): void {
                if (! $record->tandaiDibayar($data['paid_date'], $data['payment_reference'] ?? null)) {
                    static::peringatanStatusBerubah();

                    return;
                }

                Notification::make()->success()->title($record->code.' ditandai sudah dibayar')->send();

                static::segarkan($livewire, $record);
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Memuat ulang halaman Lihat setelah status berubah.
     *
     * Rincian pembebanan hidup di komponen tersendiri, dan komponen itu tidak ikut
     * digambar ulang saat tombol di kepala halaman mengubah status tagihannya. Akibatnya
     * tombol Tambah pembebanan sempat tetap terlihat pada tagihan yang baru saja diajukan,
     * yaitu justru keadaan yang penguncian itu ada untuk mencegahnya. Pemuatan ulang
     * membuat kepala halaman dan rinciannya selalu bercerita hal yang sama. Pemberitahuan
     * tetap sampai karena Filament menitipkannya lewat sesi, bukan lewat peristiwa layar.
     */
    protected static function segarkan(mixed $livewire, VendorBill $record): void
    {
        if ($livewire instanceof ViewRecord) {
            $livewire->redirect(static::getUrl('view', ['record' => $record]));
        }
    }

    protected static function peringatanStatusBerubah(): void
    {
        Notification::make()
            ->warning()
            ->title('Statusnya sudah berubah')
            ->body('Tagihan ini tidak lagi berada pada tahap itu. Muat ulang halamannya untuk melihat keadaan terbaru.')
            ->send();
    }

    // ------------------------------------------------------------------ perizinan

    /**
     * Isi faktur hanya bisa diubah selama masih draf atau masih menunggu persetujuan.
     * Setelah disetujui, mengubahnya berarti mengubah angka yang sudah ditandatangani.
     */
    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record) && in_array($record->status, ['draft', 'diajukan'], true);
    }

    /**
     * Tagihan yang sudah disetujui tidak boleh dihapus, karena nilainya sudah menjadi
     * bagian dari realisasi anggaran dan menghapusnya membuat angka tahun berjalan
     * berubah tanpa jejak. Yang batal dibayar dibatalkan, bukan dihapus.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && in_array($record->status, ['draft', 'ditolak', 'dibatalkan'], true);
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
            'index' => ListVendorBills::route('/'),
            'create' => CreateVendorBill::route('/create'),
            'view' => ViewVendorBill::route('/{record}'),
        ];
    }
}
