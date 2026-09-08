<?php

namespace App\Filament\Resources\SupplyPurchases\RelationManagers;

use App\Filament\Resources\SupplyPurchases\SupplyPurchaseResource;
use App\Models\SupplyItem;
use App\Models\SupplyPurchase;
use App\Models\SupplyPurchaseLine;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

/**
 * Barang barang di dalam satu pembelian.
 *
 * Tabel ini menampilkan dua angka yang mudah tertukar kalau tidak dibedakan tegas: yang
 * dipesan dan yang sudah datang. Kolom sisa ada di antara keduanya supaya orang tidak perlu
 * mengurangi sendiri, karena pengurangan di kepala adalah tempat kesalahan gudang bermula.
 *
 * Kolom Sudah datang dan Sisa dijumlahkan dari baris penerimaan, bukan dibaca dari kolom
 * tersimpan, jadi keduanya tidak mungkin bertentangan dengan riwayat penerimaannya.
 */
class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Items';

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
                ->live()
                ->columnSpanFull()
                // Basis data menjaga satu barang cukup sekali per pembelian lewat kunci unik.
                // Tanpa penjagaan di layar, orang bertemu galat basis data mentah, bukan
                // kalimat yang bisa dibaca.
                ->disableOptionWhen(function (string $value, ?SupplyPurchaseLine $record): bool {
                    $sudahAda = $this->getOwnerRecord()->lines()
                        ->when($record !== null, fn ($q) => $q->whereKeyNot($record->getKey()))
                        ->pluck('supply_item_id')
                        ->all();

                    return in_array((int) $value, array_map('intval', $sudahAda), true);
                })
                // Harga bawaan diambil dari harga pembelian terakhir barang itu, sebagai
                // titik awal yang masuk akal, bukan sebagai harga yang benar. Yang mengetik
                // tetap harus mencocokkannya dengan penawaran rekanan.
                ->afterStateUpdated(function ($state, callable $set): void {
                    $harga = SupplyItem::find($state)?->last_price;

                    if (filled($harga)) {
                        $set('unit_price', (float) $harga);
                    }
                })
                ->helperText('Stok di dalam kurung adalah stok saat halaman ini dibuka, dipakai untuk memperkirakan berapa yang perlu dipesan.'),
            TextInput::make('quantity_ordered')
                ->label('Jumlah dipesan')
                ->numeric()
                ->integer()
                ->minValue(1)
                ->required()
                ->default(1)
                ->helperText('Dalam satuan barangnya sendiri, misalnya box atau rim, bukan dalam pcs.'),
            TextInput::make('unit_price')
                ->label('Harga satuan')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required()
                ->helperText('Terisi sendiri dari harga pembelian terakhir barang ini kalau ada. Cocokkan dengan penawaran rekanan sebelum disimpan.'),
            TextInput::make('notes')
                ->label('Keterangan')
                ->maxLength(200)
                ->columnSpanFull()
                ->placeholder('Boleh dikosongkan')
                ->helperText('Contoh: merek bebas, atau warna hitam saja.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')
                    ->label('Barang')
                    ->description(fn (SupplyPurchaseLine $record): ?string => $record->item?->code)
                    ->wrap(),
                TextColumn::make('quantity_ordered')
                    ->label('Dipesan')
                    ->state(fn (SupplyPurchaseLine $record): string => $record->dipesanLabel())
                    ->description(fn (SupplyPurchaseLine $record): string => $record->hargaLabel())
                    ->alignEnd(),
                TextColumn::make('diterima')
                    ->label('Sudah datang')
                    ->state(fn (SupplyPurchaseLine $record): string => $record->diterimaLabel())
                    ->alignEnd(),
                TextColumn::make('sisa')
                    ->label('Sisa')
                    ->state(fn (SupplyPurchaseLine $record): string => $record->sisaLabel())
                    ->color(fn (SupplyPurchaseLine $record): string => $record->sisa() === 0 ? 'success' : 'warning')
                    ->alignEnd(),
                TextColumn::make('subtotal')
                    ->label('Jumlah')
                    ->state(fn (SupplyPurchaseLine $record): string => $record->subtotalLabel())
                    ->alignEnd()
                    // Nilai pembelian adalah jumlah seluruh barisnya, dan ini satu satunya
                    // tempat angka itu ada. Menampilkannya di kaki kolom membuat pembaca
                    // tidak perlu menjumlah sendiri untuk mencocokkan dengan fakturnya.
                    // Dijumlahkan lewat satu kueri, bukan dengan mengambil seluruh baris ke
                    // PHP, karena jumlah baris pembelian bisa banyak dan kaki tabel ikut
                    // dihitung ulang setiap kali tabelnya digambar.
                    ->summarize(
                        Summarizer::make()
                            ->label('Nilai pembelian')
                            ->using(fn (Builder $query): float => (float) $query
                                ->selectRaw('coalesce(sum(quantity_ordered * unit_price), 0) as jumlah')
                                ->value('jumlah'))
                            ->formatStateUsing(fn ($state): string => Rupiah::penuh((float) $state))
                    ),
                TextColumn::make('notes')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id')
            ->modifyQueryUsing(fn ($query) => $query->with(['item', 'receiptLines', 'purchase']))
            ->headerActions([
                CreateAction::make()
                    ->label('Add Item')
                    ->modalHeading('Add Item')
                    ->modalSubmitActionLabel('Add Item')
                    ->after(fn () => $this->segarkanHalaman())
                    ->visible(fn (): bool => $this->bisaDiubah()),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->modalHeading(fn (SupplyPurchaseLine $record): string => 'Edit '
                        .($record->item?->name ?? 'Item'))
                    ->after(fn () => $this->segarkanHalaman())
                    ->visible(fn (): bool => $this->bisaDiubah()),
                DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(fn (SupplyPurchaseLine $record): string => 'Remove '
                        .($record->item?->name ?? 'Item'))
                    ->after(fn () => $this->segarkanHalaman())
                    ->visible(fn (): bool => $this->bisaDiubah())
                    ->modalDescription('Barisnya hilang dari pembelian ini dan nilai pembeliannya ikut berkurang. Stok tidak tersentuh, karena stok memang baru bertambah saat barangnya datang.'),
            ])
            ->emptyStateHeading('Belum ada barang yang dibeli')
            ->emptyStateDescription($this->bisaDiubah()
                ? 'Tambahkan satu baris per jenis barang, beserta jumlah dan harga satuannya. Jumlah seluruh baris inilah nilai pembeliannya, dan pembelian tanpa barang tidak bisa diajukan.'
                : 'Pembelian ini sudah tidak berstatus draf, jadi daftarnya tidak bisa diubah lagi. Pesanan yang perlu diperbaiki harus ditolak lebih dulu lalu dikembalikan ke draf.');
    }

    /**
     * Menggambar ulang halaman induk setelah baris berubah, karena nilai pesanan dihitung
     * dari baris baris ini tetapi ditampilkan di infolist halaman induk, dan halaman induk
     * adalah komponen Livewire terpisah yang tidak ikut digambar ulang dengan sendirinya.
     */
    protected function segarkanHalaman(): void
    {
        $this->dispatch('rincian-berubah');
    }

    protected function bisaDiubah(): bool
    {
        $pembelian = $this->getOwnerRecord();

        return $pembelian instanceof SupplyPurchase
            && $pembelian->rincianBisaDiubah()
            && SupplyPurchaseResource::canEdit($pembelian);
    }
}
