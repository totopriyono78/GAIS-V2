<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SupplyRequests\SupplyRequestResource;
use App\Models\SupplyRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Permintaan ATK yang masih menunggu seseorang bertindak.
 *
 * Draf sengaja tidak masuk daftar ini, sama seperti pada penggantian biaya. Draf memang
 * belum menunggu siapa pun kecuali pemiliknya sendiri, dan memasukkannya membuat dasbor
 * menyebut angka yang tidak bisa ditindaklanjuti orang lain.
 *
 * Kolom stok ada di sini, bukan hanya di halaman permintaannya, karena pertanyaan yang
 * dibawa orang ke tab ini bukan "mana yang menunggu" melainkan "mana yang bisa saya
 * kerjakan sekarang". Permintaan yang disetujui tetapi stoknya kurang tidak bisa
 * dikerjakan hari ini, dan itu perlu terbaca tanpa membuka satu per satu.
 */
class PermintaanBarangMenunggu extends TableWidget
{
    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Supply Requests Awaiting Action';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('supply_requests.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => SupplyRequestResource::getEloquentQuery()
                ->with(['employee', 'department', 'approver', 'lines.item'])
                ->withCount('lines')
                ->whereIn('supply_requests.status', ['diajukan', 'disetujui'])
                // Yang paling dekat bisa dikerjakan lebih dulu: menunggu tim GA, lalu
                // menunggu atasan. Di dalam tiap kelompok, yang paling lama menunggu di atas.
                ->orderByRaw("case status when 'disetujui' then 0 else 1 end")
                ->orderBy('submitted_at'))
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono'),
                TextColumn::make('employee.full_name')
                    ->label('Pemohon')
                    ->description(fn (SupplyRequest $record): string => $record->department?->name
                        ?? 'Departemen sudah dihapus')
                    ->wrap(),
                TextColumn::make('purpose')
                    ->label('Untuk keperluan apa')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('lines_count')
                    ->label('Isi')
                    ->state(fn (SupplyRequest $record): string => $record->jenisLabel())
                    ->description(fn (SupplyRequest $record): string => $record->kebutuhanLabel()),
                TextColumn::make('status')
                    ->label('Menunggu')
                    ->badge()
                    ->state(fn (SupplyRequest $record): string => $record->statusLabel())
                    ->color(fn (SupplyRequest $record): string => $record->statusColor())
                    ->description(fn (SupplyRequest $record): string => $record->tahapLabel()),
                TextColumn::make('stok')
                    ->label('Kesiapan stok')
                    ->state(function (SupplyRequest $record): string {
                        if ($record->status !== 'disetujui') {
                            return 'Belum diperiksa';
                        }

                        $kurang = count($record->kekuranganStok());

                        return $kurang === 0
                            ? 'Siap diserahkan'
                            : $kurang.' barang stoknya kurang';
                    })
                    ->color(function (SupplyRequest $record): ?string {
                        if ($record->status !== 'disetujui') {
                            return null;
                        }

                        return $record->kekuranganStok() === [] ? 'success' : 'danger';
                    })
                    ->description(fn (SupplyRequest $record): string => $record->lamaMenunggu()),
            ])
            ->recordUrl(fn (SupplyRequest $record): string => SupplyRequestResource::getUrl('view', ['record' => $record]))
            ->paginated([10, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tidak ada permintaan ATK yang menunggu')
            ->emptyStateDescription('Semua permintaan sudah disetujui dan barangnya diserahkan. Permintaan baru dibuat dari menu Supply Requests, dan draf yang masih disusun pemohon tidak dihitung di sini.');
    }
}
