<?php

namespace App\Filament\Resources\ServiceRequests\RelationManagers;

use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/**
 * Foto keadaan yang dilampirkan pemohon.
 *
 * Satu foto mengubah "AC bocor" menjadi keterangan yang cukup untuk membawa alat yang
 * benar sejak kunjungan pertama. Foto ini juga yang dicari saat vendor menagih pekerjaan
 * yang tidak dikerjakan, atau saat kerusakan disangkal sudah ada sejak awal.
 *
 * Lampiran hanya bisa ditambah selama permintaan belum diterima tim GA. Setelah itu,
 * foto hasil pekerjaan tempatnya di perintah kerja, supaya bukti sebelum dan sesudah
 * tidak tersebar di dua layar yang berbeda.
 */
class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Foto keadaan';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            TextInput::make('name')
                ->label('Keterangan foto')
                ->required()
                ->maxLength(150)
                ->placeholder('Contoh: Indoor unit menetes di sisi kiri'),
            FileUpload::make('file_path')
                ->label('Berkas')
                ->disk('public')
                ->directory('lampiran-permintaan')
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->required()
                ->helperText('Foto atau PDF, maksimum 10 MB. Untuk mengganti berkas, hapus dulu yang lama lalu unggah yang baru.')
                ->storeFileNamesIn('original_name'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('size_bytes')
                    ->label('Ukuran')
                    ->state(fn (ServiceRequestAttachment $record): string => $record->sizeLabel())
                    ->alignEnd(),
                TextColumn::make('uploadedByUser.name')
                    ->label('Diunggah oleh')
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'asc')
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah foto')
                    ->visible(fn (): bool => $this->masihBolehDiubah()),
            ])
            ->recordActions([
                Action::make('unduh')
                    ->label('Buka')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->url(fn (ServiceRequestAttachment $record): ?string => filled($record->file_path)
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => $this->masihBolehDiubah()),
            ])
            ->emptyStateHeading('Belum ada foto')
            ->emptyStateDescription('Foto keadaan sebelum diperbaiki membuat teknisi datang membawa alat yang tepat sejak kunjungan pertama, dan menjadi bukti saat hasil pekerjaan dipersoalkan kemudian.');
    }

    /**
     * Foto masih boleh ditambah selama permintaan belum diterima tim GA, dan hanya oleh
     * orang yang memang boleh mengubah permintaan itu.
     */
    protected function masihBolehDiubah(): bool
    {
        /** @var ServiceRequest $tiket */
        $tiket = $this->getOwnerRecord();

        return ServiceRequestResource::canEdit($tiket);
    }
}
