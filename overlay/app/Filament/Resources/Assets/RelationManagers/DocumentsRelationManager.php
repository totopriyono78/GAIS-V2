<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use App\Filament\Resources\Assets\AssetResource;
use App\Models\AssetDocument;
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
 * Berkas pendukung satu aset: kartu garansi, buku manual, faktur, kontrak sewa,
 * sertifikat, dan lainnya.
 *
 * Diletakkan sebagai daftar tersendiri, bukan sebagai kolom unggah di formulir aset,
 * karena jumlah berkas per aset tidak tetap dan tiap berkas perlu jenis serta namanya
 * sendiri supaya masih bisa dicari setahun kemudian.
 */
class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Dokumen pendukung';

    /*
     * Daftar dokumen ikut dimuat bersama halaman aset, bukan lewat permintaan susulan.
     * Isinya satu kueri kecil terhadap dokumen milik satu aset saja, dan orang yang
     * membuka layar ubah aset hampir selalu turun sampai ke sini. Menunda pemuatannya
     * hanya menukar satu kueri ringan dengan satu putaran jaringan tambahan.
     */
    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('type')
                ->label('Jenis dokumen')
                ->options(AssetDocument::TYPES)
                ->required()
                ->default('kartu_garansi'),
            TextInput::make('name')
                ->label('Nama dokumen')
                ->required()
                ->maxLength(150)
                ->placeholder('Contoh: Kartu garansi 2 tahun dari distributor'),
            FileUpload::make('file_path')
                ->label('Berkas')
                ->disk('public')
                ->directory('dokumen-aset')
                ->visibility('public')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->required()
                ->columnSpanFull()
                ->helperText('PDF, foto, Word, atau Excel, maksimum 10 MB. Untuk mengganti berkas, hapus dulu yang lama lalu unggah yang baru.')
                // Nama asli dan ukurannya disimpan supaya masih terbaca di layar,
                // sementara nama berkas di cakram dibuat acak untuk menghindari tabrakan.
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
                    ->formatStateUsing(fn (AssetDocument $record): string => $record->typeLabel()),
                TextColumn::make('name')
                    ->label('Nama dokumen')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('original_name')
                    ->label('Berkas')
                    ->placeholder('Tidak diketahui')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('size_bytes')
                    ->label('Ukuran')
                    ->state(fn (AssetDocument $record): string => $record->sizeLabel())
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
                    ->options(AssetDocument::TYPES)
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Unggah dokumen')
                    ->visible(fn (): bool => AssetResource::canEdit($this->getOwnerRecord())),
            ])
            ->recordActions([
                Action::make('unduh')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->url(fn (AssetDocument $record): ?string => filled($record->file_path)
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => AssetResource::canEdit($this->getOwnerRecord())),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => AssetResource::canEdit($this->getOwnerRecord())),
            ])
            ->emptyStateHeading('Belum ada dokumen pendukung')
            ->emptyStateDescription('Unggah kartu garansi, buku manual, faktur, kontrak sewa, atau sertifikat di sini, supaya berkasnya tidak hilang di map fisik dan bisa dicari lewat kode asetnya.');
    }
}
