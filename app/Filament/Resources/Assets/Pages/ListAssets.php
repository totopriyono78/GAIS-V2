<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Resources\Assets\AssetResource;
use App\Services\AssetImporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected const IMPORT_FOLDER = 'impor-aset';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Tambah aset'),

            Action::make('impor')
                ->label('Impor dari CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->visible(fn (): bool => AssetResource::canCreate())
                ->modalHeading('Impor data aset dari CSV')
                ->modalDescription('Simpan berkas Excel Anda sebagai CSV terlebih dulu. Pemisah titik koma maupun koma sama sama diterima.')
                ->modalSubmitActionLabel('Jalankan impor')
                ->schema([
                    FileUpload::make('berkas')
                        ->label('Berkas CSV')
                        ->required()
                        ->disk('local')
                        ->directory(self::IMPORT_FOLDER)
                        ->visibility('private')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->helperText($this->importHelpText()),
                    Toggle::make('periksa_saja')
                        ->label('Periksa dulu, jangan simpan')
                        ->default(true)
                        ->helperText('Biarkan menyala untuk melihat masalah apa saja yang ada tanpa mengubah data. Matikan kalau sudah yakin.'),
                ])
                ->action(function (array $data): void {
                    $this->runImport($data);
                }),

            Action::make('templat')
                ->label('Unduh templat CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->visible(fn (): bool => AssetResource::canCreate())
                ->action(fn (): StreamedResponse => response()->streamDownload(
                    fn () => print (AssetImporter::templateCsv()),
                    'templat-impor-aset.csv',
                    ['Content-Type' => 'text/csv; charset=UTF-8'],
                )),

            Action::make('laporan_impor')
                ->label('Unduh laporan impor terakhir')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('gray')
                ->visible(fn (): bool => AssetResource::canCreate() && $this->latestReportPath() !== null)
                ->action(function () {
                    $path = $this->latestReportPath();

                    if ($path === null) {
                        Notification::make()
                            ->title('Belum ada laporan impor')
                            ->body('Jalankan impor dulu, laporannya dibuat setelah itu.')
                            ->warning()
                            ->send();

                        return null;
                    }

                    return response()->download(Storage::disk('local')->path($path), basename($path));
                }),
        ];
    }

    protected function importHelpText(): string
    {
        $categories = AssetResource::activeCategoryNames();

        $base = 'Kolom yang dikenali: nama, kategori, departemen, lokasi, penanggung_jawab, jenis, merek, model, '
            .'nomor_seri, tanggal_perolehan, sumber_perolehan, nilai_perolehan, status, kondisi, garansi_sampai, catatan. '
            .'Yang wajib ada isinya: nama, kategori, dan departemen. Ketiganya membentuk kode aset.';

        return $categories === ''
            ? $base.' Belum ada kategori aset yang nomor akunnya terisi, jadi semua baris akan ditolak. Isi nomor akun COA di menu Kategori aset dulu.'
            : $base.' Kode kategori yang siap dipakai: '.$categories.'.';
    }

    protected function runImport(array $data): void
    {
        $relativePath = is_array($data['berkas'] ?? null)
            ? (string) reset($data['berkas'])
            : (string) ($data['berkas'] ?? '');

        if ($relativePath === '' || ! Storage::disk('local')->exists($relativePath)) {
            Notification::make()
                ->title('Berkas impor tidak ditemukan')
                ->body('Unggahannya tidak tersimpan. Coba unggah ulang berkasnya.')
                ->danger()
                ->send();

            return;
        }

        $dryRun = (bool) ($data['periksa_saja'] ?? true);
        $importer = new AssetImporter($dryRun);

        try {
            $importer->run(Storage::disk('local')->path($relativePath));
        } catch (\Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Impor dihentikan')
                ->body($exception->getMessage())
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $reportName = 'laporan-impor-'.now()->format('Ymd-His').'.csv';
        Storage::disk('local')->put(self::IMPORT_FOLDER.'/'.$reportName, $importer->reportCsv());

        $errorCount = count($importer->errors);
        $warningCount = count($importer->warnings);

        $summary = $dryRun
            ? "Diperiksa {$importer->total} baris, {$importer->imported} siap diimpor."
            : "Diproses {$importer->total} baris, {$importer->imported} aset tersimpan.";

        $summary .= " Ditolak {$errorCount} baris, peringatan {$warningCount}.";

        foreach (array_slice($importer->errors, 0, 3) as $error) {
            $summary .= " Baris {$error['baris']}: {$error['pesan']}";
        }

        if ($errorCount > 3) {
            $summary .= ' Sisanya ada di laporan.';
        }

        $summary .= " Laporan lengkap: {$reportName}, unduh lewat tombol Unduh laporan impor terakhir.";

        $notification = Notification::make()
            ->title($dryRun ? 'Pemeriksaan selesai, data belum disimpan' : 'Impor selesai')
            ->body($summary)
            ->persistent();

        $errorCount > 0 ? $notification->warning() : $notification->success();

        $notification->send();

        if (! $dryRun) {
            $this->resetTable();
        }
    }

    protected function latestReportPath(): ?string
    {
        $files = collect(Storage::disk('local')->files(self::IMPORT_FOLDER))
            ->filter(fn (string $file): bool => str_contains(basename($file), 'laporan-impor-'))
            ->sort()
            ->values();

        return $files->last();
    }
}
