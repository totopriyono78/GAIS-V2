<?php

namespace App\Filament\Resources\MaintenanceSchedules\RelationManagers;

use App\Filament\Resources\MaintenanceSchedules\MaintenanceScheduleResource;
use App\Models\MaintenanceVisit;
use App\Support\Rupiah;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Riwayat penjadwalan: tiap jatuh tempo dan apa yang terjadi padanya.
 *
 * Inilah layar yang menjawab "servis kuartal lalu sudah dikerjakan vendor atau belum".
 * Urutannya jatuh tempo terbaru di atas, karena yang paling sering ditanya adalah dua
 * atau tiga kunjungan terakhir, bukan yang paling awal.
 */
class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Schedule History';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sequence')
                    ->label('Ke')
                    ->alignEnd(),
                TextColumn::make('due_date')
                    ->label('Jatuh tempo')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Keadaan')
                    ->badge()
                    ->state(fn (MaintenanceVisit $record): string => $record->statusLabel())
                    ->color(fn (MaintenanceVisit $record): string => $record->statusColor())
                    ->description(fn (MaintenanceVisit $record): string => $record->keteranganWaktu()),
                TextColumn::make('completed_date')
                    ->label('Dikerjakan')
                    ->date('d M Y')
                    ->placeholder('Belum')
                    ->sortable(),
                TextColumn::make('pelaksana')
                    ->label('Dikerjakan oleh')
                    // Kunjungan yang dilewati tidak dikerjakan siapa pun. Menampilkan
                    // nama rekanan di sini terbaca seolah mereka yang mengerjakannya,
                    // padahal justru itu bulan yang mereka tidak datang.
                    ->state(fn (MaintenanceVisit $record): string => match ($record->status) {
                        'dikerjakan' => $record->pelaksana(),
                        'dilewati' => 'Tidak dikerjakan',
                        default => 'Belum dikerjakan',
                    })
                    ->wrap(),
                TextColumn::make('result')
                    ->label('Hasil atau alasan')
                    ->state(fn (MaintenanceVisit $record): string => match ($record->status) {
                        'dikerjakan' => filled($record->result) ? $record->result : 'Tidak dicatat',
                        'dilewati' => $record->skip_reason ?: 'Alasan tidak dicatat',
                        default => 'Belum ada',
                    })
                    ->limit(80)
                    ->wrap(),
                TextColumn::make('cost')
                    ->label('Biaya')
                    ->state(fn (MaintenanceVisit $record): string => filled($record->cost)
                        ? Rupiah::penuh((float) $record->cost)
                        : 'Tidak ada')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('closedByUser.name')
                    ->label('Ditutup oleh')
                    ->placeholder('Belum ditutup')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('due_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Keadaan')
                    ->options(MaintenanceVisit::STATUSES)
                    ->multiple(),
            ])
            ->recordActions([
                MaintenanceScheduleResource::catatKunjunganAction(),
                MaintenanceScheduleResource::lewatiKunjunganAction(),
                MaintenanceScheduleResource::buatPerintahKerjaAction(),
            ])
            ->emptyStateHeading('Belum ada riwayat')
            ->emptyStateDescription('Kunjungan pertama dibuat bersamaan dengan jadwalnya, jadi daftar ini seharusnya tidak pernah kosong. Kalau kosong, jadwalnya sedang dihentikan.');
    }

    /*
     * Kunjungan tidak dibuat, diubah, atau dihapus tangan. Semuanya lahir dari jadwalnya
     * dan ditutup lewat tiga tindakan di atas. Membiarkan orang menyisipkan satu baris
     * riwayat akan membuat riwayat itu berhenti menjadi bukti.
     */
    public function canCreate(): bool
    {
        return false;
    }
}
