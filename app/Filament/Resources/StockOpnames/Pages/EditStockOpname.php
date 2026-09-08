<?php

namespace App\Filament\Resources\StockOpnames\Pages;

use App\Filament\Resources\StockOpnames\RelationManagers\LinesRelationManager;
use App\Filament\Resources\StockOpnames\StockOpnameResource;
use App\Models\StockOpnameLine;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EditStockOpname extends EditRecord
{
    protected static string $resource = StockOpnameResource::class;

    public function getSubheading(): ?string
    {
        $record = $this->record;

        $bagian = [$record->statusLabel(), $record->describeScope()];

        if ($record->isAdjusted()) {
            $bagian[] = 'Penyesuaian sudah diterapkan '.$record->adjusted_at->format('d M Y H:i');
        }

        return implode('. ', $bagian).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('susun')
                ->label('Build Target List')
                ->icon('heroicon-o-list-bullet')
                ->color('gray')
                ->visible(fn (): bool => $this->record->isDraft() && StockOpnameResource::canEdit($this->record))
                ->requiresConfirmation()
                ->modalHeading('Rebuild Target List')
                ->modalDescription('Daftar disusun dari cakupan yang tersimpan, dan baris yang sudah ada akan diganti. Jalankan ini sebelum pemeriksaan dimulai.')
                ->modalSubmitActionLabel('Build Now')
                ->action(function (): void {
                    $jumlah = $this->record->generateLines();

                    // Daftar target adalah komponen Livewire tersendiri di bawah formulir,
                    // dan tidak ikut digambar ulang saat tombol di kepala halaman ditekan.
                    // Tanpa baris ini, tabelnya masih menulis "Daftar target masih kosong"
                    // padahal barisnya sudah jadi.
                    $this->dispatch(LinesRelationManager::REFRESH_EVENT);

                    if ($jumlah === 0) {
                        Notification::make()
                            ->title('Tidak ada aset yang masuk cakupan')
                            ->body('Longgarkan cakupannya, atau pastikan asetnya sudah tercatat dan belum berstatus dilepas.')
                            ->warning()
                            ->persistent()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Daftar target tersusun')
                        ->body("{$jumlah} aset masuk daftar pemeriksaan.")
                        ->success()
                        ->send();
                }),

            Action::make('mulai')
                ->label('Start Count')
                ->icon('heroicon-o-play')
                ->visible(fn (): bool => $this->record->isDraft() && StockOpnameResource::canEdit($this->record))
                ->requiresConfirmation()
                ->modalHeading('Start Count')
                ->modalDescription('Setelah dimulai, daftar target tidak bisa disusun ulang. Pastikan cakupannya sudah benar.')
                ->modalSubmitActionLabel('Start')
                ->action(function (): void {
                    if ($this->record->lines()->count() === 0) {
                        Notification::make()
                            ->title('Daftar target masih kosong')
                            ->body('Susun daftar target dulu sebelum pemeriksaan dimulai.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->record->forceFill([
                        'status' => 'berjalan',
                        'started_at' => now(),
                        'started_by_user_id' => Auth::id(),
                    ])->save();

                    // Tombol centang dan tombol catat temuan baru boleh muncul setelah
                    // sesi berjalan, jadi tabelnya perlu digambar ulang di sini juga.
                    $this->dispatch(LinesRelationManager::REFRESH_EVENT);

                    Notification::make()->title('Pemeriksaan dimulai')->success()->send();
                }),

            Action::make('selesaikan')
                ->label('Close Session')
                ->icon('heroicon-o-check-circle')
                ->visible(fn (): bool => $this->record->isRunning() && StockOpnameResource::canEdit($this->record))
                ->requiresConfirmation()
                ->modalHeading('Close Stock Opname Session')
                ->modalDescription(fn (): string => $this->belumDiperiksa() > 0
                    ? 'Masih ada '.$this->belumDiperiksa().' baris yang belum diperiksa. Baris itu akan tercatat apa adanya sebagai belum diperiksa.'
                    : 'Semua baris sudah diperiksa. Setelah ditutup, temuan tidak bisa diubah lagi.')
                ->modalSubmitActionLabel('Close Session')
                ->action(function (): void {
                    $this->record->forceFill([
                        'status' => 'selesai',
                        'finished_at' => now(),
                        'finished_by_user_id' => Auth::id(),
                    ])->save();

                    $this->dispatch(LinesRelationManager::REFRESH_EVENT);

                    Notification::make()
                        ->title('Sesi ditutup')
                        ->body('Langkah berikutnya: terapkan penyesuaian supaya lokasi dan kondisi di data aset ikut diperbarui.')
                        ->success()
                        ->send();
                }),

            Action::make('terapkan')
                ->label('Apply Adjustments')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('warning')
                ->visible(fn (): bool => $this->record->isFinished()
                    && ! $this->record->isAdjusted()
                    && StockOpnameResource::canApprove())
                ->requiresConfirmation()
                ->modalHeading('Apply Findings to Asset Data')
                ->modalDescription('Lokasi dan kondisi aset akan diperbarui mengikuti temuan di lapangan. Aset yang tidak ditemukan sengaja tidak diubah statusnya, karena itu perlu ditindaklanjuti orang, bukan diputuskan sistem. Tindakan ini hanya bisa dijalankan sekali.')
                ->modalSubmitActionLabel('Apply')
                ->action(function (): void {
                    $hasil = $this->record->applyAdjustments();

                    Notification::make()
                        ->title('Penyesuaian diterapkan')
                        ->body("Lokasi diperbarui pada {$hasil['lokasi']} aset, kondisi pada {$hasil['kondisi']} aset. "
                            ."Ada {$hasil['tidak_ditemukan']} aset yang tidak ditemukan dan perlu ditindaklanjuti terpisah.")
                        ->success()
                        ->persistent()
                        ->send();
                }),

            Action::make('batalkan')
                ->label('Cancel Session')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => in_array($this->record->status, ['draft', 'berjalan'], true)
                    && StockOpnameResource::canEdit($this->record))
                ->requiresConfirmation()
                ->modalHeading('Cancel Stock Opname Session')
                ->modalDescription('Sesi ditandai batal dan tidak bisa dilanjutkan. Daftar target beserta temuan yang sudah dicatat tetap tersimpan sebagai riwayat.')
                ->modalSubmitActionLabel('Cancel Session')
                ->action(function (): void {
                    $this->record->forceFill(['status' => 'dibatalkan'])->save();

                    $this->dispatch(LinesRelationManager::REFRESH_EVENT);

                    Notification::make()->title('Sesi dibatalkan')->warning()->send();
                }),

            Action::make('unduh')
                ->label('Download CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn (): bool => $this->record->lines()->exists())
                ->action(fn (): StreamedResponse => response()->streamDownload(
                    fn () => print ($this->hasilCsv()),
                    'opname-'.str_replace(['/', '\\'], '-', $this->record->code).'.csv',
                    ['Content-Type' => 'text/csv; charset=UTF-8'],
                )),

            // Filament menilai tombol hapus lewat Gate, dan Gate::before meloloskan
            // super admin untuk semua kemampuan. Aturan "sesi yang sudah diterapkan
            // penyesuaiannya tidak boleh dihapus" ada di StockOpnameResource::canDelete(),
            // jadi harus disebut sendiri di sini supaya tombolnya benar benar hilang.
            DeleteAction::make()
                ->visible(fn (): bool => StockOpnameResource::canDelete($this->record)),
        ];
    }

    protected function belumDiperiksa(): int
    {
        return $this->record->lines()->where('checked', false)->count();
    }

    protected function hasilCsv(): string
    {
        $baris = ['kode_aset;nama_aset;lokasi_tercatat;lokasi_ditemukan;kondisi_tercatat;kondisi_ditemukan;hasil;catatan'];

        $this->record->lines()
            ->with(['expectedLocation', 'foundLocation'])
            ->orderBy('asset_code')
            ->chunk(500, function ($lines) use (&$baris) {
                foreach ($lines as $line) {
                    $baris[] = implode(';', [
                        $line->asset_code,
                        $this->bersihkan($line->asset_name),
                        $this->bersihkan($line->expectedLocation?->code ?? ''),
                        $this->bersihkan($line->foundLocation?->code ?? ''),
                        $line->expected_condition,
                        $line->found_condition ?? '',
                        StockOpnameLine::RESULTS[$line->result()] ?? '',
                        $this->bersihkan($line->notes ?? ''),
                    ]);
                }
            });

        return implode("\n", $baris)."\n";
    }

    protected function bersihkan(string $nilai): string
    {
        return str_replace([';', "\n", "\r"], [',', ' ', ' '], $nilai);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
