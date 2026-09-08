<?php

namespace App\Filament\Widgets;

use App\Models\SupplyItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Daftar barang yang stoknya sudah sampai atau di bawah batas minimum.
 *
 * Ditampilkan sebagai tabel, bukan diagram, karena yang dibutuhkan orang di sini
 * bukan proporsi melainkan daftar belanja: barang apa, kurang berapa. Diurutkan
 * dari yang paling parah, yaitu selisih terbesar terhadap batas minimumnya.
 */
class PersediaanPerluDipesan extends TableWidget
{
    protected static ?int $sort = 3;

    /*
     * Widget dasbor dimuat bersama halamannya, bukan lewat permintaan susulan.
     * Isinya hanya beberapa kueri agregat, jadi satu permintaan lebih cepat
     * daripada enam permintaan sekaligus, dan tulisan "Loading..." tidak sempat
     * berkedip. Ini juga yang membuat dasbor tetap terbuka penuh di server
     * bawaan PHP, yang hanya melayani satu permintaan pada satu waktu.
     */
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Items to Reorder';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('supply_items.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            // Kekurangan dihitung di dalam kueri, bukan sesudahnya, supaya urutannya
            // benar lintas halaman. Kalau diurutkan di PHP, halaman pertama hanya akan
            // terurut di antara isinya sendiri, dan barang paling parah bisa terdampar
            // di halaman dua. Postgres menerima nama kolom hasil pada ORDER BY.
            ->query(fn (): Builder => SupplyItem::query()
                ->where('is_active', true)
                ->needsRestock()
                ->withSum('transactions', 'quantity')
                ->selectRaw(
                    'supply_items.*, supply_items.minimum_stock - (select coalesce(sum(quantity), 0)'
                    .' from supply_transactions where supply_transactions.supply_item_id = supply_items.id)'
                    .' as kekurangan'
                )
                ->orderByRaw('kekurangan desc')
                ->orderBy('name'))
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono'),
                // Kategori tidak lagi jadi kolom sendiri, melainkan keterangan di bawah
                // nama barang. Tabel ini dibaca sambil menyusun daftar belanja, dan
                // tujuh kolom membuat nama barang terpotong lebih dulu daripada
                // informasi yang sebenarnya dicari.
                TextColumn::make('name')
                    ->label('Barang')
                    ->description(fn (SupplyItem $record): string => SupplyItem::CATEGORIES[$record->category] ?? (string) $record->category)
                    ->wrap(),
                TextColumn::make('stok')
                    ->label('Stok')
                    ->state(fn (SupplyItem $record): string => $record->formatQuantity((int) $record->transactions_sum_quantity))
                    ->alignEnd(),
                TextColumn::make('minimum_stock')
                    ->label('Minimum')
                    ->state(fn (SupplyItem $record): string => $record->formatQuantity($record->minimum_stock))
                    ->alignEnd(),
                // Barang yang stoknya persis di batas minimum ikut masuk daftar ini,
                // dan kekurangannya nol. Menulis "0 pcs" di kolom kekurangan membuat
                // orang bertanya kenapa barang itu ada di sini, jadi keadaannya
                // disebut apa adanya.
                TextColumn::make('kurang')
                    ->label('Kurang')
                    ->state(function (SupplyItem $record): string {
                        $kurang = max($record->minimum_stock - (int) $record->transactions_sum_quantity, 0);

                        return $kurang === 0 ? 'Pas di batas' : $record->formatQuantity($kurang);
                    })
                    ->alignEnd()
                    ->color('warning'),
                TextColumn::make('keadaan')
                    ->label('Keadaan')
                    ->badge()
                    ->state(fn (SupplyItem $record): string => $record->stockStateLabel((int) $record->transactions_sum_quantity))
                    ->color(fn (SupplyItem $record): string => $record->stockStateColor((int) $record->transactions_sum_quantity)),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tidak ada yang perlu dipesan')
            ->emptyStateDescription('Semua barang habis pakai masih di atas batas minimumnya. Batas itu diatur per barang di menu Barang habis pakai.');
    }
}
