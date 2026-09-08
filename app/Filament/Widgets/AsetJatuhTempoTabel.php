<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Daftar aset yang garansinya akan habis dalam tiga bulan, diurutkan dari yang
 * paling dekat. Melengkapi angka ringkasnya: angka menjawab berapa banyak, tabel
 * ini menjawab yang mana, supaya tindakannya bisa langsung dikerjakan.
 */
class AsetJatuhTempoTabel extends TableWidget
{
    protected static ?int $sort = 5;

    /*
     * Widget dasbor dimuat bersama halamannya, bukan lewat permintaan susulan.
     * Isinya hanya beberapa kueri agregat, jadi satu permintaan lebih cepat
     * daripada enam permintaan sekaligus, dan tulisan "Loading..." tidak sempat
     * berkedip. Ini juga yang membuat dasbor tetap terbuka penuh di server
     * bawaan PHP, yang hanya melayani satu permintaan pada satu waktu.
     */
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Assets With Expiring Warranty';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('assets.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Asset::query()
                ->with(['category', 'location', 'custodian'])
                ->where('status', '!=', 'dilepas')
                ->where('has_warranty', true)
                ->whereNotNull('warranty_until')
                ->whereBetween('warranty_until', [now()->toDateString(), now()->addMonths(3)->toDateString()]))
            ->columns([
                TextColumn::make('code')
                    ->label('Kode aset')
                    ->fontFamily('mono'),
                TextColumn::make('name')
                    ->label('Aset')
                    ->description(fn (Asset $record): ?string => trim(($record->brand ?? '').' '.($record->model ?? '')) ?: null)
                    ->wrap(),
                TextColumn::make('location.code')
                    ->label('Lokasi')
                    ->placeholder('Belum diisi'),
                TextColumn::make('custodian.full_name')
                    ->label('Penanggung jawab')
                    ->placeholder('Belum ditunjuk')
                    ->wrap(),
                TextColumn::make('warranty_until')
                    ->label('Garansi sampai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('sisa')
                    ->label('Sisa')
                    ->state(function (Asset $record): string {
                        $sisa = $record->warrantyDaysLeft();

                        return $sisa === null ? 'Tidak diketahui' : $sisa.' hari';
                    })
                    ->alignEnd()
                    ->color(fn (Asset $record): string => ($record->warrantyDaysLeft() ?? 999) <= 30 ? 'danger' : 'warning'),
            ])
            ->defaultSort('warranty_until')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak ada garansi yang segera habis')
            ->emptyStateDescription('Tidak ada aset yang masa garansinya berakhir dalam tiga bulan ke depan. Masa garansi diisi per aset di menu Daftar aset.');
    }
}
