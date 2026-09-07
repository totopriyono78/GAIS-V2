<?php

namespace App\Filament\Resources\WorkOrders\RelationManagers;

use App\Filament\Resources\WorkOrders\WorkOrderResource;
use App\Models\WorkOrderAttachment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/**
 * Lampiran satu perintah kerja: foto sebelum, foto sesudah, nota, faktur.
 *
 * Foto sebelum dan sesudah bukan hiasan. Keduanya yang dipakai saat vendor menagih
 * pekerjaan yang tidak dikerjakan, dan saat klaim garansi ditolak dengan alasan
 * kerusakannya sudah ada sejak awal.
 */
class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Lampiran';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('type')
                ->label('Jenis lampiran')
                ->options(WorkOrderAttachment::TYPES)
                ->required()
                ->default('foto_kerusakan'),
            TextInput::make('name')
                ->label('Nama lampiran')
                ->required()
                ->maxLength(150)
                ->placeholder('Contoh: Foto indoor unit sebelum dicuci'),
            FileUpload::make('file_path')
                ->label('Berkas')
                ->disk('public')
                ->directory('lampiran-perintah-kerja')
                ->visibility('public')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->required()
                ->columnSpanFull()
                ->helperText('Foto atau PDF, maksimum 10 MB. Untuk mengganti berkas, hapus dulu yang lama lalu unggah yang baru.')
                ->storeFileNamesIn('original_name'),
            Textarea::make('notes')
                ->label('Catatan')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->state(fn (WorkOrderAttachment $record): string => $record->typeLabel()),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('size_bytes')
                    ->label('Ukuran')
                    ->state(fn (WorkOrderAttachment $record): string => $record->sizeLabel())
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('uploadedByUser.name')
                    ->label('Oleh')
                    ->placeholder('Tidak diketahui')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options(WorkOrderAttachment::TYPES)
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah lampiran')
                    ->visible(fn (): bool => WorkOrderResource::canEdit($this->getOwnerRecord())),
            ])
            ->recordActions([
                Action::make('unduh')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->url(fn (WorkOrderAttachment $record): ?string => filled($record->file_path)
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => WorkOrderResource::canEdit($this->getOwnerRecord())),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => WorkOrderResource::canEdit($this->getOwnerRecord())),
            ])
            ->emptyStateHeading('Belum ada lampiran')
            ->emptyStateDescription('Unggah foto kerusakan sebelum dikerjakan, foto hasil sesudahnya, dan nota atau faktur dari rekanan. Foto sebelum dan sesudah yang paling sering dicari saat menagih atau mengklaim garansi.');
    }
}
