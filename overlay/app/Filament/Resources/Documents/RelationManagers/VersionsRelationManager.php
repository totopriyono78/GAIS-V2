<?php

namespace App\Filament\Resources\Documents\RelationManagers;

use App\Enums\VersionStatus;
use App\Exceptions\MasalahVersiDokumen;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Services\PengelolaDokumen;
use App\Support\Berkas;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Riwayat versi satu dokumen, beserta pengesahannya.
 *
 * Empat hal di layar ini yang berbeda dari daftar biasa, dan keempatnya lahir
 * dari satu aturan: versi yang sudah disahkan adalah catatan, bukan data yang
 * boleh diperbaiki.
 *
 * - Tidak ada tombol ubah. Versi yang salah diganti dengan versi baru, bukan
 *   disunting, supaya yang dibaca orang sebulan lalu tetap bisa ditelusuri.
 * - Tidak ada tombol hapus. Foreign key-nya pun memakai restrictOnDelete.
 * - Pengesahan menutup versi lama pada tanggal berlakunya yang baru, bukan
 *   mengubah statusnya. Itu yang membuat pertanyaan "versi mana yang berlaku
 *   15 Maret lalu" tetap bisa dijawab.
 * - Penarikan tanpa pengganti dipisah dari pengesahan, karena hasilnya berbeda:
 *   dokumen jadi tidak punya versi berlaku sama sekali, dan itu keadaan yang sah.
 */
class VersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';

    protected static ?string $title = 'Versions';

    protected static ?string $modelLabel = 'versi';

    protected static ?string $pluralModelLabel = 'versi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('berkas')
                ->label('Berkas versi baru')
                ->required()
                ->disk(Berkas::DISK)
                // Sama seperti di layar unggah: yang menentukan letak, nama, dan
                // sidik jari berkas adalah lapisan layanan, bukan formulir.
                ->storeFiles(false)
                ->maxSize(51200)
                ->columnSpanFull(),
            Textarea::make('change_note')
                ->label('Apa yang berubah')
                ->required()
                ->rows(3)
                ->maxLength(1000)
                ->helperText('Ditulis untuk orang yang membandingkan versi ini dengan versi sebelumnya, bukan untuk arsip. Sebutkan bagian yang berubah, bukan sekadar "revisi".')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version_number')
                    ->label('Versi')
                    ->formatStateUsing(fn (int $state): string => 'v'.$state)
                    ->badge()
                    /*
                     * Dinilai dari baris versinya sendiri, bukan dari
                     * current_version_id milik dokumennya.
                     *
                     * Dokumen induk dimuat sekali di awal permintaan, jadi
                     * sesudah sebuah pengesahan ia masih memegang nilai lama
                     * dan versi yang baru saja digantikan tetap berlabel
                     * berlaku sekarang sampai halamannya dimuat ulang. Versi
                     * yang berlaku adalah yang disahkan dan belum ditutup, dan
                     * itu terbaca dari barisnya sendiri tanpa perlu bertanya
                     * ke dokumennya.
                     */
                    ->color(fn (?DocumentVersion $record): string => static::sedangBerlaku($record) ? 'success' : 'gray')
                    ->description(fn (?DocumentVersion $record): ?string => static::sedangBerlaku($record)
                        ? 'Berlaku sekarang'
                        : null)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (DocumentVersion $record): string => $record->status->warna())
                    ->formatStateUsing(fn (DocumentVersion $record): string => $record->status->label()),
                TextColumn::make('effective_from')
                    ->label('Masa berlaku')
                    ->state(fn (DocumentVersion $record): string => match (true) {
                        $record->effective_from === null => 'Belum berlaku',
                        $record->effective_until === null => 'Sejak '.$record->effective_from->translatedFormat('d M Y'),
                        // Mulai dan berakhir pada tanggal yang sama berarti versi
                        // ini digantikan di hari yang sama, jadi ia tidak pernah
                        // berlaku sehari penuh. Ditulis begitu supaya tidak
                        // terbaca seperti masa berlaku selama satu hari.
                        $record->effective_until->isSameDay($record->effective_from) => 'Digantikan di hari yang sama, '
                            .$record->effective_from->translatedFormat('d M Y'),
                        default => $record->effective_from->translatedFormat('d M Y')
                            .' sampai '.$record->effective_until->translatedFormat('d M Y'),
                    }),
                TextColumn::make('original_name')
                    ->label('Berkas')
                    ->description(fn (DocumentVersion $record): string => $record->ukuranTerbaca()
                        .' · '.strtolower($record->scan_status->label()))
                    ->wrap(),
                TextColumn::make('uploader.name')
                    ->label('Diunggah oleh')
                    ->description(fn (DocumentVersion $record): ?string => $record->created_at?->translatedFormat('d M Y, H:i')),
                TextColumn::make('change_note')
                    ->label('Catatan perubahan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('version_number', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Version')
                    ->modalHeading('Add Version')
                    ->modalDescription('Berkas lama tidak ditimpa. Versi baru masuk sebagai draf, dan baru berlaku setelah disahkan.')
                    ->visible(fn (): bool => $this->bolehTambahVersi())
                    ->using(function (array $data): DocumentVersion {
                        $berkas = $data['berkas'] ?? null;

                        if (is_array($berkas)) {
                            $berkas = reset($berkas);
                        }

                        abort_unless($berkas instanceof UploadedFile, 422, 'Berkasnya belum terunggah dengan benar.');

                        return app(PengelolaDokumen::class)->tambahVersi(
                            dokumen: $this->getOwnerRecord(),
                            file: $berkas,
                            catatanPerubahan: $data['change_note'],
                            user: Auth::user(),
                        );
                    }),
            ])
            ->recordActions([
                Action::make('unduh')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->color('gray')
                    ->url(fn (?DocumentVersion $record): ?string => $record === null
                        ? null
                        : route('gais.dokumen.unduh', [$this->getOwnerRecord(), $record]))
                    ->openUrlInNewTab()
                    /*
                     * Barisnya boleh kosong, dan itu bukan hal aneh.
                     *
                     * Filament menilai visibilitas tombol bukan hanya saat
                     * menggambar barisnya, melainkan juga saat tombol itu
                     * dipasang lewat namanya, misalnya ketika kotak dialognya
                     * dibuka. Pada saat itu barisnya belum ditentukan, dan
                     * penutupan yang menuntut DocumentVersion akan menggagalkan
                     * seluruh permintaan.
                     */
                    ->visible(fn (?DocumentVersion $record): bool => $record !== null
                        && $record->bolehDiunduh()
                        && (bool) Auth::user()?->hasPermission('documents.download')),

                Action::make('sahkan')
                    ->label('Approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->modalHeading('Approve Version')
                    ->modalDescription('Versi yang berlaku sebelumnya akan ditutup pada tanggal ini, bukan dihapus. Riwayatnya tetap bisa ditelusuri.')
                    ->modalSubmitActionLabel('Approve')
                    ->schema([
                        DatePicker::make('berlaku_mulai')
                            ->label('Berlaku mulai')
                            ->native(false)
                            ->default(now())
                            ->required()
                            // Batas bawahnya adalah tanggal berlaku versi yang
                            // sekarang berjalan, sebab versi itu akan ditutup
                            // pada tanggal ini. Lebih awal dari itu berarti
                            // meminta versi yang berakhir sebelum ia dimulai,
                            // dan lebih baik pilihannya dimatikan di kalender
                            // daripada ditolak setelah tombolnya ditekan.
                            ->minDate(fn (): ?string => $this->mulaiVersiBerjalan())
                            ->helperText(function (): string {
                                $batas = $this->mulaiVersiBerjalan();

                                $dasar = 'Boleh tanggal lampau, misalnya saat dokumen sudah ditandatangani minggu lalu dan baru diunggah sekarang.';

                                return $batas === null
                                    ? $dasar
                                    : $dasar.' Paling awal '
                                        .Carbon::parse($batas)->translatedFormat('d F Y')
                                        .', yaitu tanggal berlaku versi yang sekarang.';
                            }),
                    ])
                    ->action(function (DocumentVersion $record, array $data): void {
                        try {
                            app(PengelolaDokumen::class)->sahkan(
                                versi: $record,
                                pengesah: Auth::user(),
                                berlakuMulai: Carbon::parse($data['berlaku_mulai']),
                            );
                        } catch (MasalahVersiDokumen $e) {
                            // Bukan kerusakan, melainkan penolakan yang sah:
                            // pengesahan lain sudah menempati periode itu, atau
                            // tanggal yang dipilih lebih awal dari tanggal
                            // berlaku versi yang sekarang. Keduanya bisa
                            // diperbaiki sendiri oleh pemakainya.
                            Notification::make()
                                ->title('Pengesahan tidak jadi disimpan')
                                ->body($e->getMessage())
                                ->warning()
                                ->persistent()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Versi '.$record->version_number.' berlaku mulai '
                                .Carbon::parse($data['berlaku_mulai'])->translatedFormat('d F Y'))
                            ->success()
                            ->send();
                    })
                    // Hanya draf dan yang sedang ditinjau yang bisa disahkan.
                    // Versi yang sudah disahkan tidak disahkan ulang, dan yang
                    // ditolak perlu diunggah ulang sebagai versi baru.
                    ->visible(fn (?DocumentVersion $record): bool => $record !== null
                        && in_array($record->status, [VersionStatus::Draf, VersionStatus::Ditinjau], true)
                        && $this->bolehSahkan()),

                Action::make('tolak')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Reject Version')
                    ->modalSubmitActionLabel('Reject')
                    ->schema([
                        Textarea::make('alasan')
                            ->label('Alasan penolakan')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Dibaca pengunggahnya saat menyiapkan versi berikutnya, jadi sebutkan yang perlu diperbaiki.'),
                    ])
                    ->action(function (DocumentVersion $record, array $data): void {
                        app(PengelolaDokumen::class)->tolak($record, Auth::user(), $data['alasan']);

                        Notification::make()
                            ->title('Versi '.$record->version_number.' ditolak')
                            ->body('Alasannya tercatat di catatan perubahan versi itu.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (?DocumentVersion $record): bool => $record !== null
                        && in_array($record->status, [VersionStatus::Draf, VersionStatus::Ditinjau], true)
                        && $this->bolehSahkan()),

                Action::make('tarik')
                    ->label('Withdraw')
                    ->icon('heroicon-o-archive-box-x-mark')
                    ->color('warning')
                    ->modalHeading('Withdraw Without Replacement')
                    ->modalDescription('Dipakai saat dokumen dicabut dan penggantinya belum ada. Setelah ini dokumen tidak punya versi berlaku sama sekali, dan itu keadaan yang sah.')
                    ->modalSubmitActionLabel('Withdraw')
                    ->schema([
                        DatePicker::make('sampai')
                            ->label('Berlaku sampai')
                            ->native(false)
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (DocumentVersion $record, array $data): void {
                        try {
                            app(PengelolaDokumen::class)->tarikTanpaPengganti($record, Carbon::parse($data['sampai']));
                        } catch (MasalahVersiDokumen $e) {
                            Notification::make()
                                ->title('Penarikan tidak jadi disimpan')
                                ->body($e->getMessage())
                                ->warning()
                                ->persistent()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Versi '.$record->version_number.' ditarik')
                            ->body('Dokumen ini sekarang tidak punya versi yang berlaku.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (?DocumentVersion $record): bool => static::sedangBerlaku($record)
                        && $this->bolehSahkan()),
            ])
            ->emptyStateHeading('Belum ada versi')
            ->emptyStateDescription('Dokumen ini belum punya satu pun berkas. Tekan Add Version untuk mengunggah yang pertama.');
    }

    /**
     * Tanggal berlaku versi yang sekarang berjalan, kalau ada. Dipakai sebagai
     * batas bawah kalender pengesahan.
     */
    private function mulaiVersiBerjalan(): ?string
    {
        return $this->getOwnerRecord()
            ->versions()
            ->where('status', VersionStatus::Disahkan->value)
            ->whereNull('effective_until')
            ->whereNotNull('effective_from')
            ->orderByDesc('effective_from')
            ->value('effective_from');
    }

    /**
     * Versi yang sedang berlaku: sudah disahkan, dan belum ditutup.
     */
    private static function sedangBerlaku(?DocumentVersion $versi): bool
    {
        return $versi !== null
            && $versi->status === VersionStatus::Disahkan
            && $versi->effective_until === null;
    }

    private function bolehSahkan(): bool
    {
        return (bool) Auth::user()?->hasPermission('documents.approve');
    }

    /**
     * Jenis dokumen yang tidak memakai versi tidak menampilkan tombol tambah.
     * Lampiran dikoreksi lewat transaksinya, bukan direvisi sebagai versi baru,
     * dan aturan itu ditegakkan juga di lapisan layanan.
     */
    private function bolehTambahVersi(): bool
    {
        $dokumen = $this->getOwnerRecord();

        return $dokumen instanceof Document
            && (bool) $dokumen->type?->is_versioned
            && (bool) Auth::user()?->hasPermission('documents.create');
    }
}
