<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\VehicleDocument;
use App\Support\Rupiah;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Riwayat dokumen satu kendaraan.
 *
 * Satu baris adalah satu masa berlaku. Perpanjangan tahun ini tidak menimpa yang tahun
 * lalu, dan itu disengaja: yang tahun lalu masih dibutuhkan saat menjawab berapa biaya
 * pajak naik dari tahun ke tahun, dan saat auditor meminta bukti kendaraan tidak pernah
 * menganggur tanpa dokumen sah.
 *
 * Tombol Perpanjang menyalin nomor dan penerbit dari baris yang dipilih, lalu menyodorkan
 * tanggal berakhir setahun berikutnya. Mengetik ulang sembilan belas digit nomor rangka
 * setiap tahun adalah cara termudah membuat data jadi salah.
 */
class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Tax, STNK, KIR & Insurance';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('type')
                ->label('Jenis dokumen')
                ->options(VehicleDocument::TYPES)
                ->required()
                ->live()
                ->helperText(fn ($get): string => match ($get('type')) {
                    'pajak_tahunan' => 'Pengesahan tahunan di Samsat, berlaku satu tahun.',
                    'stnk_lima_tahun' => 'Penggantian STNK dan pelat nomor, berlaku lima tahun.',
                    'kir' => 'Uji berkala untuk kendaraan angkutan, umumnya berlaku enam bulan.',
                    'asuransi' => 'Polis asuransi kendaraan beserta masa pertanggungannya.',
                    default => 'Pilih jenisnya supaya jatuh temponya diingatkan dengan tenggang yang tepat.',
                }),
            TextInput::make('document_number')
                ->label('Nomor dokumen')
                ->maxLength(60)
                ->placeholder('Tidak dicatat')
                ->helperText('Nomor STNK, nomor SKPD, atau nomor polis.'),
            DatePicker::make('issued_date')
                ->label('Tanggal terbit')
                ->displayFormat('d M Y')
                ->maxDate(now()->addMonth())
                ->live()
                ->placeholder('Tidak dicatat'),
            DatePicker::make('expires_at')
                ->label('Berlaku sampai')
                ->displayFormat('d M Y')
                ->required()
                ->helperText('Wajib diisi. Dokumen tanpa tanggal berakhir tidak bisa diingatkan, dan itu penyebab paling umum denda pajak kendaraan.'),
            TextInput::make('issuer')
                ->label('Diterbitkan oleh')
                ->maxLength(120)
                ->placeholder('Tidak dicatat')
                ->helperText('Nama Samsat, kantor uji KIR, atau perusahaan asuransinya.'),
            TextInput::make('cost')
                ->label('Biaya')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->placeholder('Tidak dicatat')
                ->helperText('Yang benar benar dibayar. Angka ini yang dipakai membandingkan biaya antar tahun.'),
            FileUpload::make('file_path')
                ->label('Pindaian dokumen')
                ->disk('public')
                ->directory('dokumen-kendaraan')
                ->visibility('public')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->columnSpanFull()
                ->helperText('Foto atau PDF, maksimum 10 MB. Boleh dikosongkan, tetapi pindaian inilah yang menolong saat dokumen aslinya sedang dibawa orang lain.')
                ->storeFileNamesIn('original_name'),
            Textarea::make('notes')
                ->label('Catatan')
                ->rows(2)
                ->columnSpanFull()
                ->placeholder('Contoh: dibayar lewat calo Samsat, kuitansi menyusul.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Jenis')
                    ->state(fn (VehicleDocument $record): string => $record->jenisLabel())
                    ->description(fn (VehicleDocument $record): ?string => $record->document_number)
                    ->wrap(),
                TextColumn::make('expires_at')
                    ->label('Berlaku sampai')
                    ->date('d M Y')
                    ->description(fn (VehicleDocument $record): string => $record->keteranganWaktu())
                    ->color(fn (VehicleDocument $record): string => $this->berlaku($record)
                        ? $record->keadaanColor()
                        : 'gray')
                    ->sortable(),
                TextColumn::make('berlaku')
                    ->label('Keadaan')
                    ->badge()
                    ->state(fn (VehicleDocument $record): string => $this->berlaku($record)
                        ? 'Berlaku sekarang'
                        : 'Sudah diperpanjang')
                    ->color(fn (VehicleDocument $record): string => $this->berlaku($record) ? 'primary' : 'gray'),
                TextColumn::make('issued_date')
                    ->label('Terbit')
                    ->date('d M Y')
                    ->placeholder('Tidak dicatat')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('issuer')
                    ->label('Diterbitkan oleh')
                    ->placeholder('Tidak dicatat')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cost')
                    ->label('Biaya')
                    ->state(fn (VehicleDocument $record): string => filled($record->cost)
                        ? Rupiah::penuh((float) $record->cost)
                        : 'Tidak dicatat')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('size_bytes')
                    ->label('Pindaian')
                    ->state(fn (VehicleDocument $record): string => $record->sizeLabel())
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expires_at', 'desc')
            ->filters([
                Filter::make('berlaku')
                    ->label('Hanya yang berlaku sekarang')
                    ->query(fn (Builder $query): Builder => $query->berlaku()),
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(VehicleDocument::TYPES)
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Document')
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
            ])
            ->recordActions([
                $this->perpanjangAction(),
                Action::make('buka')
                    ->label('Open Scan')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->visible(fn (VehicleDocument $record): bool => filled($record->file_path))
                    ->url(fn (VehicleDocument $record): ?string => filled($record->file_path)
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord()))
                    ->modalDescription('Baris ini adalah satu masa berlaku. Menghapusnya menghilangkan bukti bahwa kendaraan sempat berdokumen sah pada periode itu, dan biayanya keluar dari rekap biaya dokumen.'),
            ])
            ->emptyStateHeading('Belum ada dokumen yang dicatat')
            ->emptyStateDescription('Catat pajak tahunan, perpanjangan STNK lima tahunan, uji KIR, dan asuransinya. Setelah tanggal berakhir terisi, kendaraan ini ikut muncul di daftar yang perlu diurus dan di lencana angka pada menu.');
    }

    /**
     * Perpanjangan sebagai baris baru, bukan pengubahan baris lama.
     *
     * Nomor dan penerbit disalin, tanggal berakhirnya diusulkan sesuai kebiasaan jenisnya,
     * dan biaya sengaja dikosongkan karena angka tahun lalu bukan angka tahun ini.
     */
    protected function perpanjangAction(): Action
    {
        return Action::make('perpanjang')
            ->label('Renew')
            ->icon('heroicon-o-arrow-path')
            ->color('success')
            ->iconButton()
            ->visible(fn (VehicleDocument $record): bool => $this->berlaku($record)
                && VehicleResource::canEdit($this->getOwnerRecord()))
            // Nama jenis tidak dikecilkan hurufnya, karena STNK dan KIR adalah singkatan
            // dan "perpanjang pajak tahunan dan pengesahan stnk" terbaca seperti salah ketik.
            ->modalHeading(fn (VehicleDocument $record): string => 'Renew '.$record->jenisLabel())
            ->modalDescription('Baris lama tetap disimpan sebagai riwayat. Yang dibuat di sini adalah masa berlaku berikutnya.')
            ->modalSubmitActionLabel('Save Renewal')
            ->fillForm(fn (VehicleDocument $record): array => [
                'document_number' => $record->document_number,
                'issuer' => $record->issuer,
                'issued_date' => now()->toDateString(),
                'expires_at' => $record->expires_at->copy()->addMonths($this->bulanBerlaku($record->type))->toDateString(),
            ])
            ->schema([
                DatePicker::make('issued_date')
                    ->label('Tanggal terbit')
                    ->displayFormat('d M Y')
                    ->required(),
                DatePicker::make('expires_at')
                    ->label('Berlaku sampai')
                    ->displayFormat('d M Y')
                    ->required()
                    ->helperText('Sudah diusulkan menurut kebiasaan jenis dokumen ini. Ganti kalau di dokumennya tertulis lain.'),
                TextInput::make('document_number')
                    ->label('Nomor dokumen')
                    ->maxLength(60)
                    ->helperText('Disalin dari dokumen sebelumnya. Ganti kalau nomornya berubah.'),
                TextInput::make('issuer')
                    ->label('Diterbitkan oleh')
                    ->maxLength(120),
                TextInput::make('cost')
                    ->label('Biaya')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Belum diketahui')
                    ->helperText('Sengaja dikosongkan, karena biaya tahun ini belum tentu sama dengan tahun lalu.'),
                FileUpload::make('file_path')
                    ->label('Pindaian dokumen baru')
                    ->disk('public')
                    ->directory('dokumen-kendaraan')
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(10240)
                    ->openable()
                    ->storeFileNamesIn('original_name'),
            ])
            ->action(function (VehicleDocument $record, array $data): void {
                $this->getOwnerRecord()->documents()->create($data + ['type' => $record->type]);

                $this->getOwnerRecord()->load('documents');
            });
    }

    /** Berapa bulan satu jenis dokumen umumnya berlaku. */
    protected function bulanBerlaku(string $jenis): int
    {
        return match ($jenis) {
            'stnk_lima_tahun' => 60,
            'kir' => 6,
            default => 12,
        };
    }

    /**
     * Apakah baris ini yang berlaku sekarang untuk jenisnya, yaitu yang tanggal
     * berakhirnya paling jauh.
     */
    protected function berlaku(VehicleDocument $record): bool
    {
        $berlaku = $this->getOwnerRecord()->dokumenBerlaku($record->type);

        return $berlaku !== null && (int) $berlaku->getKey() === (int) $record->getKey();
    }
}
