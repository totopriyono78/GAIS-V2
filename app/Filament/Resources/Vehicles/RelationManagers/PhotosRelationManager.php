<?php

namespace App\Filament\Resources\Vehicles\RelationManagers;

use App\Filament\Resources\Vehicles\VehicleResource;
use App\Models\VehiclePhoto;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Foto kendaraan.
 *
 * Tabel ini memakai kolom gambar, bukan hanya nama berkas, karena orang yang membukanya
 * sedang membandingkan keadaan, bukan mencari nama file. Melihat enam petak kecil lalu
 * membuka satu yang mencurigakan jauh lebih cepat daripada membuka tujuh tautan
 * satu per satu.
 *
 * Tanggal pengambilan dan odometer dicatat berdampingan karena keduanya yang menentukan
 * apakah sebuah lecet sudah ada sebelum kendaraan dipinjam, dan itu pertanyaan yang
 * selalu muncul terlambat, saat kendaraannya sudah kembali.
 */
class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Vehicle Photos';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('type')
                ->label('Sudut pengambilan')
                ->options(VehiclePhoto::TYPES)
                ->default('depan')
                ->required(),
            TextInput::make('name')
                ->label('Keterangan foto')
                ->required()
                ->maxLength(150)
                ->placeholder('Contoh: Bumper depan kiri, lecet lama'),
            DatePicker::make('taken_date')
                ->label('Tanggal diambil')
                ->displayFormat('d M Y')
                ->default(now())
                ->maxDate(now())
                ->helperText('Tanggal fotonya diambil, bukan tanggal diunggah.'),
            TextInput::make('odometer_km')
                ->label('Odometer saat difoto')
                ->numeric()
                ->minValue(0)
                ->suffix('km')
                ->placeholder('Tidak dicatat')
                ->helperText(function (): string {
                    $terakhir = $this->getOwnerRecord()->last_odometer_km;

                    return filled($terakhir)
                        ? 'Odometer terakhir yang tercatat: '.number_format((int) $terakhir, 0, ',', '.').' km.'
                        : 'Odometer kendaraan ini belum pernah dicatat.';
                }),
            FileUpload::make('file_path')
                ->label('Foto')
                ->disk('public')
                ->directory('foto-kendaraan')
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->required()
                ->columnSpanFull()
                ->helperText('JPG, PNG, atau WebP, maksimum 10 MB. Untuk mengganti foto, hapus dulu yang lama lalu unggah yang baru.')
                ->storeFileNamesIn('original_name'),
            Textarea::make('notes')
                ->label('Catatan')
                ->rows(2)
                ->columnSpanFull()
                ->placeholder('Contoh: lecet ini sudah ada sejak diterima dari dealer.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Foto')
                    ->disk('public')
                    ->height(64)
                    ->extraImgAttributes(['loading' => 'lazy']),
                TextColumn::make('type')
                    ->label('Sudut')
                    ->badge()
                    ->color(fn (VehiclePhoto $record): string => $record->type === 'kerusakan' ? 'warning' : 'gray')
                    ->state(fn (VehiclePhoto $record): string => $record->jenisLabel()),
                TextColumn::make('name')
                    ->label('Keterangan')
                    ->description(fn (VehiclePhoto $record): ?string => $record->notes)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('taken_date')
                    ->label('Diambil')
                    ->state(fn (VehiclePhoto $record): string => $record->keteranganWaktu())
                    ->sortable(),
                TextColumn::make('uploadedByUser.name')
                    ->label('Diunggah oleh')
                    ->placeholder('Tidak diketahui'),
                TextColumn::make('size_bytes')
                    ->label('Ukuran')
                    ->state(fn (VehiclePhoto $record): string => $record->sizeLabel())
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // Yang terbaru di atas, karena yang dicari hampir selalu keadaan terakhir.
            ->defaultSort('taken_date', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('Sudut pengambilan')
                    ->options(VehiclePhoto::TYPES)
                    ->multiple(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Upload Photo')
                    ->modalHeading('Upload Vehicle Photo')
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
            ])
            ->recordActions([
                Action::make('buka')
                    ->label('Open Full Size')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->url(fn (VehiclePhoto $record): ?string => $record->url())
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord())),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => VehicleResource::canEdit($this->getOwnerRecord()))
                    ->modalDescription('Berkas fotonya ikut terhapus dari penyimpanan, tidak hanya barisnya. Kalau foto ini dipakai sebagai bukti keadaan sebelum peminjaman, simpan salinannya dulu.'),
            ])
            ->emptyStateHeading('Belum ada foto kendaraan')
            ->emptyStateDescription('Unggah tampak depan, belakang, samping, dan interiornya sekali saat pendataan, lalu tambahkan foto kerusakan setiap kali ada lecet baru. Foto bertanggal inilah yang menyelesaikan perdebatan tentang kapan sebuah lecet muncul.');
    }
}
