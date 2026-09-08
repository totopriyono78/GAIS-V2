<?php

namespace App\Filament\Resources\SupplyPurchases\RelationManagers;

use App\Models\SupplyPurchase;
use App\Models\SupplyReceipt;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Riwayat kedatangan barang atas satu pembelian.
 *
 * Hanya baca, dan itu disengaja. Penerimaan tidak pernah dibuat dari sini melainkan dari
 * tombol Receive Goods di kepala halaman, karena membuatnya berarti menambah stok, dan
 * menambah stok lewat tombol Tambah di sebuah tabel kecil akan membuat tindakan sebesar itu
 * terasa seperti mengetik satu baris data.
 *
 * Penerimaan juga tidak bisa diubah maupun dihapus dari sini. Setiap penerimaan sudah
 * melahirkan mutasi stoknya sendiri, dan mengubah angkanya belakangan berarti stok yang
 * tercatat tidak lagi sama dengan penjumlahan mutasinya. Kalau memang salah hitung, cara
 * yang jujur adalah mencatat koreksi stok di menu Supply Movements, karena koreksi itu
 * meninggalkan jejak sedangkan penyuntingan diam diam tidak.
 */
class ReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'receipts';

    protected static ?string $title = 'Deliveries Received';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono'),
                TextColumn::make('receipt_date')
                    ->label('Tanggal datang')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('isi')
                    ->label('Apa yang datang')
                    ->state(fn (SupplyReceipt $record): string => $record->isiLabel())
                    ->wrap(),
                TextColumn::make('nilai')
                    ->label('Nilai')
                    ->state(fn (SupplyReceipt $record): string => $record->totalLabel())
                    ->alignEnd(),
                TextColumn::make('delivery_note_number')
                    ->label('Surat jalan')
                    ->placeholder('Tidak dicatat'),
                TextColumn::make('receivedByUser.name')
                    ->label('Diterima oleh')
                    ->placeholder('Tidak tercatat')
                    ->description(fn (SupplyReceipt $record): ?string => $record->notes),
            ])
            ->defaultSort('receipt_date')
            ->modifyQueryUsing(fn ($query) => $query->with(['lines.purchaseLine.item', 'receivedByUser']))
            ->emptyStateHeading('Belum ada barang yang datang')
            ->emptyStateDescription($this->pesanKosong());
    }

    protected function pesanKosong(): string
    {
        $pembelian = $this->getOwnerRecord();

        if (! $pembelian instanceof SupplyPurchase) {
            return 'Belum ada catatan kedatangan barang atas pembelian ini.';
        }

        return match (true) {
            $pembelian->status === 'draft' => 'Pembelian ini masih draf. Barang baru bisa dicatat datang setelah pesanannya disetujui.',
            $pembelian->status === 'diajukan' => 'Pesanan ini masih menunggu persetujuan manajer, jadi belum dikirim ke rekanan.',
            in_array($pembelian->status, SupplyPurchase::RECEIVABLE_STATUSES, true) => 'Barangnya belum datang. Saat kiriman pertama sampai, catat lewat tombol Receive Goods di atas, dan stok akan bertambah dari situ.',
            default => 'Tidak ada barang yang pernah datang atas pembelian ini.',
        };
    }
}
