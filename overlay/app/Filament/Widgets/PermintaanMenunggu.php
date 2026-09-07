<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Permintaan perbaikan yang masih menunggu seseorang bertindak.
 *
 * Daftarnya memakai penyempitan yang sama dengan menu Permintaan perbaikan, jadi
 * karyawan biasa melihat tiketnya sendiri dan kepala departemen melihat tiket
 * departemennya. Dasbor yang menyebut angka yang tidak boleh dibuka orangnya hanya
 * menimbulkan pertanyaan yang tidak bisa dijawab layar mana pun.
 *
 * Barisnya bisa diklik langsung ke tiketnya, karena baris di sini selalu berarti ada
 * yang perlu disetujui atau diterima, bukan sekadar diketahui.
 */
class PermintaanMenunggu extends TableWidget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Permintaan yang menunggu tindakan';

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('service_requests.read') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ServiceRequestResource::getEloquentQuery()
                ->with(['requester', 'category', 'location'])
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->orderByRaw("case priority when 'mendesak' then 0 when 'tinggi' then 1 when 'normal' then 2 else 3 end")
                ->orderBy('submitted_at'))
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono'),
                TextColumn::make('title')
                    ->label('Permintaan')
                    ->description(fn (ServiceRequest $record): ?string => $record->category?->name)
                    ->wrap()
                    ->limit(70),
                TextColumn::make('requester.full_name')
                    ->label('Pemohon')
                    ->description(fn (ServiceRequest $record): ?string => $record->location?->name)
                    ->wrap(),
                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->state(fn (ServiceRequest $record): string => $record->priorityLabel())
                    ->color(fn (ServiceRequest $record): string => $record->priorityColor()),
                TextColumn::make('status')
                    ->label('Menunggu')
                    ->badge()
                    ->state(fn (ServiceRequest $record): string => $record->statusLabel())
                    ->color(fn (ServiceRequest $record): string => $record->statusColor())
                    ->description(fn (ServiceRequest $record): string => 'Sudah '.$record->lamaMenunggu()),
            ])
            ->recordUrl(fn (ServiceRequest $record): string => ServiceRequestResource::getUrl('view', ['record' => $record]))
            ->paginated([10, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading('Tidak ada permintaan yang menunggu')
            ->emptyStateDescription('Semua permintaan perbaikan sudah disetujui dan diterima tim GA. Permintaan baru dibuat dari menu Permintaan perbaikan.');
    }
}
