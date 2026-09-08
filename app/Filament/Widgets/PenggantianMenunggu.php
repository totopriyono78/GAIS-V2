<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use App\Models\Reimbursement;
use App\Support\Rupiah;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Pengajuan penggantian biaya yang masih menunggu seseorang bertindak.
 *
 * Draf sengaja tidak masuk daftar ini. Draf memang belum menunggu siapa pun kecuali
 * pemiliknya sendiri, dan memasukkannya akan membuat dasbor menyebut angka yang tidak
 * bisa ditindaklanjuti orang lain.
 *
 * Urutannya dari yang paling lama menunggu, karena uang karyawan yang belum kembali
 * adalah hal yang paling cepat dikeluhkan, dan yang paling lama menunggu adalah yang
 * paling dekat menjadi keluhan.
 */
class PenggantianMenunggu extends TableWidget
{
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Reimbursements Awaiting Action';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('reimbursements.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ReimbursementResource::getEloquentQuery()
                ->with(['employee', 'department', 'approver'])
                ->withSum('lines', 'amount')
                ->whereIn('reimbursements.status', ['diajukan', 'diperiksa', 'disetujui'])
                // Yang paling dekat menjadi keluhan lebih dulu: menunggu ditransfer,
                // lalu menunggu tim GA, lalu menunggu atasan. Di dalam tiap kelompok,
                // yang paling lama menunggu ada di atas.
                ->orderByRaw("case status when 'disetujui' then 0 when 'diperiksa' then 1 else 2 end")
                ->orderBy('submitted_at'))
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono'),
                TextColumn::make('employee.full_name')
                    ->label('Pemohon')
                    ->description(fn (Reimbursement $record): string => $record->department?->name
                        ?? 'Belum terbebankan ke departemen')
                    ->wrap(),
                TextColumn::make('title')
                    ->label('Untuk apa')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('lines_sum_amount')
                    ->label('Nilai')
                    ->state(fn (Reimbursement $record): string => Rupiah::penuh((float) $record->lines_sum_amount))
                    ->alignEnd(),
                TextColumn::make('status')
                    ->label('Menunggu')
                    ->badge()
                    ->state(fn (Reimbursement $record): string => $record->statusLabel())
                    ->color(fn (Reimbursement $record): string => $record->statusColor())
                    ->description(fn (Reimbursement $record): string => $record->tahapLabel()),
                TextColumn::make('submitted_at')
                    ->label('Sejak')
                    ->state(fn (Reimbursement $record): string => $record->submitted_at?->translatedFormat('d M Y') ?? 'Belum diajukan')
                    ->description(fn (Reimbursement $record): string => $record->lamaMenunggu()),
            ])
            ->recordUrl(fn (Reimbursement $record): string => ReimbursementResource::getUrl('view', ['record' => $record]))
            ->paginated([10, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tidak ada penggantian biaya yang menunggu')
            ->emptyStateDescription('Semua pengajuan sudah selesai diperiksa dan ditransfer. Pengajuan baru dibuat dari menu Penggantian biaya, dan draf yang masih disusun pemohon tidak dihitung di sini.');
    }
}
