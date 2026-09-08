<?php

namespace App\Filament\Resources\SupplyRequests\RelationManagers;

use App\Filament\Resources\SupplyRequests\SupplyRequestResource;
use App\Models\SupplyItem;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestLine;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Barang barang di dalam satu permintaan.
 *
 * Tabel ini dibaca dua kelompok orang pada dua saat yang berbeda, dan karena itu ia punya
 * dua mode. Saat permintaan masih draf, pemohon menambah dan mengubah barisnya. Saat
 * permintaan sudah disetujui, pemohon tidak bisa menyentuhnya lagi, dan yang bisa berubah
 * hanya satu kolom: jumlah yang benar benar akan diserahkan tim GA.
 *
 * Memisahkan keduanya menjadi dua tabel akan menggandakan layar yang isinya sama. Yang
 * dilakukan di sini adalah membuka tombol yang berbeda tergantung tahap dan izin, dan
 * empty state yang menjelaskan kenapa tombolnya tidak ada.
 *
 * Kolom stok selalu ditampilkan, termasuk saat pemohon masih menyusun daftarnya. Meminta
 * barang yang stoknya kosong tidak dilarang, karena permintaan yang tidak terpenuhi justru
 * angka yang paling berguna saat menyusun pesanan berikutnya, tetapi pemohon berhak tahu
 * sebelum menekan tombol ajukan.
 */
class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Requested Items';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('supply_item_id')
                ->label('Barang')
                ->options(fn (): array => SupplyItem::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->mapWithKeys(fn (SupplyItem $item): array => [
                        $item->getKey() => $item->name.' ('.$item->code.', stok '
                            .$item->formatQuantity(max($item->currentStock(), 0)).')',
                    ])
                    ->all())
                ->searchable()
                ->required()
                ->columnSpanFull()
                /*
                 * Basis data menjaga satu jenis barang cukup sekali per permintaan lewat
                 * kunci unik. Tanpa penjagaan di layar, orang yang menambahkan barang yang
                 * sama dua kali akan bertemu galat basis data mentah, bukan kalimat yang
                 * bisa dibaca. Barang yang sedang diubah tidak ikut dimatikan, karena
                 * kalau ikut, barisnya sendiri jadi tidak bisa disimpan ulang.
                 */
                ->disableOptionWhen(function (string $value, ?SupplyRequestLine $record): bool {
                    $sudahAda = $this->getOwnerRecord()->lines()
                        ->when($record !== null, fn ($q) => $q->whereKeyNot($record->getKey()))
                        ->pluck('supply_item_id')
                        ->all();

                    return in_array((int) $value, array_map('intval', $sudahAda), true);
                })
                ->helperText('Stok di dalam kurung adalah stok saat halaman ini dibuka. Barang yang stoknya kosong tetap boleh diminta, dan tim GA yang memutuskan berapa yang bisa diserahkan. Barang yang sudah ada di daftar ini tidak bisa dipilih dua kali, tambahkan jumlahnya di barisnya sendiri.'),
            TextInput::make('quantity_requested')
                ->label('Jumlah diminta')
                ->numeric()
                ->integer()
                ->minValue(1)
                ->required()
                ->default(1)
                ->helperText('Dalam satuan barangnya sendiri, misalnya box atau rim, bukan dalam pcs.'),
            TextInput::make('notes')
                ->label('Keterangan')
                ->maxLength(200)
                ->placeholder('Boleh dikosongkan')
                ->helperText('Contoh: warna hitam, atau ukuran F4.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')
                    ->label('Barang')
                    ->description(fn (SupplyRequestLine $record): ?string => $record->item?->code)
                    ->wrap(),
                TextColumn::make('quantity_requested')
                    ->label('Diminta')
                    ->state(fn (SupplyRequestLine $record): string => $record->dimintaLabel())
                    ->alignEnd(),
                TextColumn::make('quantity_issued')
                    ->label('Diserahkan')
                    ->state(fn (SupplyRequestLine $record): string => $record->diserahkanLabel())
                    ->color(fn (SupplyRequestLine $record): ?string => $record->quantity_issued !== null
                        && $record->quantity_issued < $record->quantity_requested
                            ? 'warning'
                            : null)
                    ->alignEnd(),
                TextColumn::make('stok')
                    ->label('Stok sekarang')
                    ->state(fn (SupplyRequestLine $record): string => $record->stokLabel())
                    ->color(fn (SupplyRequestLine $record): ?string => $record->request?->status === 'diserahkan'
                        || $record->stokCukup()
                            ? null
                            : 'danger')
                    ->alignEnd(),
                TextColumn::make('notes')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('transaction.code')
                    ->label('Mutasi stok')
                    ->fontFamily('mono')
                    ->placeholder('Belum ada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with(['item', 'transaction', 'request']))
            ->headerActions([
                CreateAction::make()
                    ->label('Add Item')
                    // Judul kotak dialog ditulis sendiri. Bawaan Filament menyusunnya dari
                    // nama kelas model dan berbunyi "Buat Supply Request Line", istilah
                    // basis data yang tidak pernah dipakai siapa pun saat bicara.
                    ->modalHeading('Add Item')
                    ->modalSubmitActionLabel('Add Item')
                    ->visible(fn (): bool => $this->bisaDiubahPemohon()),
            ])
            ->recordActions([
                // Satu satunya cara mengubah baris setelah permintaan disetujui, dan yang
                // bisa diubah hanya jumlah serahnya. Dipisah dari Ubah biasa supaya tim GA
                // tidak sanggup mengganti barang atau jumlah yang sudah ditandatangani.
                Action::make('setel_serah')
                    ->label('Adjust Issued Quantity')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->iconButton()
                    ->color('warning')
                    ->visible(fn (): bool => $this->bisaDisetelGa())
                    ->modalHeading(fn (SupplyRequestLine $record): string => 'Adjust '.($record->item?->name ?? 'Item'))
                    ->modalDescription(fn (SupplyRequestLine $record): string => 'Diminta '.$record->dimintaLabel()
                        .', '.lcfirst($record->stokLabel())
                        .'. Isi nol kalau barang ini tidak jadi diserahkan sama sekali. Barisnya tetap tersimpan sebagai catatan permintaan yang tidak terpenuhi.')
                    ->modalSubmitActionLabel('Save Quantity')
                    ->fillForm(fn (SupplyRequestLine $record): array => [
                        'quantity_issued' => $record->jumlahUntukDiserahkan(),
                    ])
                    ->schema([
                        TextInput::make('quantity_issued')
                            ->label('Jumlah yang akan diserahkan')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required()
                            ->helperText('Belum mengurangi stok. Stok baru berkurang saat tombol Issue Supplies ditekan di halaman permintaan.'),
                    ])
                    ->action(function (SupplyRequestLine $record, array $data, Action $action): void {
                        $jumlah = (int) $data['quantity_issued'];
                        $tersedia = max($record->item?->currentStock() ?? 0, 0);

                        if ($jumlah > $tersedia) {
                            Notification::make()
                                ->warning()
                                ->title('Melebihi stok')
                                ->body('Stok '.($record->item?->name ?? 'barang ini').' tinggal '
                                    .($record->item?->formatQuantity($tersedia) ?? $tersedia)
                                    .'. Turunkan jumlahnya, atau tambah stoknya dari menu Supply Movements lebih dulu.')
                                ->persistent()
                                ->send();

                            $action->halt();
                        }

                        $record->forceFill(['quantity_issued' => $jumlah])->save();

                        Notification::make()
                            ->success()
                            ->title('Jumlah serah disimpan')
                            ->body(($record->item?->name ?? 'Barang').' akan diserahkan '.$record->diserahkanLabel().'.')
                            ->send();
                    }),
                EditAction::make()
                    ->iconButton()
                    ->modalHeading(fn (SupplyRequestLine $record): string => 'Edit '
                        .($record->item?->name ?? 'Item'))
                    ->visible(fn (): bool => $this->bisaDiubahPemohon()),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => $this->bisaDiubahPemohon())
                    ->modalHeading(fn (SupplyRequestLine $record): string => 'Remove '
                        .($record->item?->name ?? 'Item'))
                    ->modalDescription('Barisnya hilang dari permintaan ini. Stok tidak tersentuh, karena stok memang baru berkurang saat barangnya diserahkan.'),
            ])
            ->emptyStateHeading('Belum ada barang yang diminta')
            ->emptyStateDescription($this->pesanKosong());
    }

    /** Pemohon menyusun daftarnya. Hanya selama permintaan masih draf. */
    protected function bisaDiubahPemohon(): bool
    {
        $permintaan = $this->getOwnerRecord();

        return $permintaan instanceof SupplyRequest
            && $permintaan->rincianBisaDiubah()
            && SupplyRequestResource::canEdit($permintaan);
    }

    /** Tim GA menyetel jumlah serah. Hanya selama permintaan menunggu diserahkan. */
    protected function bisaDisetelGa(): bool
    {
        $permintaan = $this->getOwnerRecord();

        return $permintaan instanceof SupplyRequest
            && $permintaan->jumlahSerahBisaDisetel()
            && (Auth::user()?->hasPermission('supply_requests.issue') ?? false);
    }

    protected function pesanKosong(): string
    {
        if ($this->bisaDiubahPemohon()) {
            return 'Tambahkan satu baris per jenis barang, beserta jumlahnya. Permintaan tanpa barang tidak bisa diajukan, dan barang yang stoknya sedang kosong tetap boleh diminta.';
        }

        $permintaan = $this->getOwnerRecord();

        if ($permintaan instanceof SupplyRequest && $permintaan->status === 'draft') {
            return 'Daftar ini masih kosong dan hanya pemohonnya yang bisa mengisinya.';
        }

        return 'Permintaan ini sudah tidak berstatus draf, jadi daftarnya tidak bisa diubah lagi. Untuk memperbaikinya, permintaan perlu ditolak lebih dulu lalu dikembalikan ke draf.';
    }
}
