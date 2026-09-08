<?php

namespace App\Filament\Resources\CleaningInspections\RelationManagers;

use App\Models\CleaningInspection;
use App\Models\CleaningInspectionLine;
use App\Support\Concerns\DetectsTableFilters;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Lembar pemeriksaan: satu baris per area.
 *
 * Barisnya tidak bisa ditambah maupun dihapus dari sini, sama seperti lembar opname. Daftar
 * ini lahir dari cakupan putaran, dan membiarkan orang menambah area di tengah pemeriksaan
 * berarti membiarkan cakupan yang tertulis di kepala putaran berbeda dari apa yang benar
 * benar diperiksa. Yang bisa dilakukan di sini hanya satu: mencatat keadaan yang dilihat.
 *
 * Hasilnya dipilih lewat tombol radio, bukan daftar pilihan yang perlu dibuka dulu. Ketiga
 * pilihannya selalu terlihat sekaligus, dan itu menghemat satu ketukan pada setiap area bagi
 * pengawas yang sedang berdiri sambil memegang telepon.
 */
class LinesRelationManager extends RelationManager
{
    use DetectsTableFilters;

    protected static string $relationship = 'lines';

    protected static ?string $title = 'Inspection Sheet';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('area_name')
                    ->label('Area')
                    ->description(fn (CleaningInspectionLine $record): string => $record->area_code)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('staff_name')
                    ->label('Penanggung jawab')
                    ->placeholder('Tanpa penanggung jawab')
                    ->searchable(),
                TextColumn::make('result')
                    ->label('Hasil')
                    ->badge()
                    ->state(fn (CleaningInspectionLine $record): string => $record->hasilLabel())
                    ->color(fn (CleaningInspectionLine $record): string => $record->hasilColor()),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('file_path')
                    ->label('Foto')
                    ->state(fn (CleaningInspectionLine $record): string => filled($record->file_path) ? 'Ada' : 'Tidak ada')
                    ->color(fn (CleaningInspectionLine $record): ?string => filled($record->file_path) ? null : 'gray'),
            ])
            ->defaultSort('area_code')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('inspection'))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('belum_diperiksa')
                    ->label('Belum diperiksa')
                    ->query(fn (Builder $query): Builder => $query->where('checked', false)),
                Filter::make('temuan')
                    ->label('Hanya yang bermasalah')
                    ->query(fn (Builder $query): Builder => $query->whereIn('result', ['kurang', 'kotor'])),
            ])
            ->recordActions([
                Action::make('catat')
                    ->label('Record Result')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->color('primary')
                    ->visible(fn (): bool => $this->bisaDicatat())
                    ->modalHeading(fn (CleaningInspectionLine $record): string => 'Inspect '.$record->area_name)
                    ->modalDescription(fn (CleaningInspectionLine $record): string => 'Penanggung jawabnya '
                        .$record->petugasLabel().'. Catat keadaan yang Anda lihat sekarang.')
                    ->modalSubmitActionLabel('Save Result')
                    ->fillForm(fn (CleaningInspectionLine $record): array => [
                        'result' => $record->result,
                        'notes' => $record->notes,
                        'file_path' => $record->file_path,
                    ])
                    ->schema([
                        Radio::make('result')
                            ->label('Keadaan area')
                            ->options(CleaningInspectionLine::RESULTS)
                            ->required()
                            ->helperText('Kurang rapi dipakai untuk yang sudah dikerjakan tetapi belum tuntas, misalnya sudah disapu tetapi tempat sampahnya masih penuh.'),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Boleh dikosongkan kalau areanya memang bersih')
                            ->helperText('Sebutkan bagian mana yang bermasalah, supaya petugasnya tahu persis apa yang perlu dikerjakan.'),
                        FileUpload::make('file_path')
                            ->label('Foto')
                            ->disk('public')
                            ->directory('pemeriksaan-kebersihan')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(10240)
                            ->openable()
                            ->storeFileNamesIn('original_name')
                            ->helperText('Foto, maksimum 10 MB. Boleh dikosongkan, tetapi temuan yang berfoto jauh lebih mudah ditindaklanjuti.'),
                    ])
                    ->action(function (CleaningInspectionLine $record, array $data): void {
                        // Foto lama dihapus kalau penggantinya benar benar berbeda. Tanpa ini,
                        // pengawas yang memotret ulang area yang sama meninggalkan satu berkas
                        // yatim di penyimpanan setiap kali ia memperbaiki fotonya.
                        $fotoBaru = $data['file_path'] ?? null;

                        if (filled($record->file_path) && $record->file_path !== $fotoBaru) {
                            $record->hapusFoto();
                        }

                        $record->forceFill([
                            'result' => $data['result'],
                            'notes' => $data['notes'] ?? null,
                            'file_path' => $data['file_path'] ?? null,
                            'original_name' => $data['original_name'] ?? null,
                            'checked' => true,
                        ])->save();

                        $this->dispatch('rincian-berubah');

                        Notification::make()
                            ->success()
                            ->title($record->area_name.' tercatat')
                            ->body($record->hasilLabel().'.')
                            ->send();
                    }),
                Action::make('batal_catat')
                    ->label('Clear Result')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->iconButton()
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (CleaningInspectionLine $record): bool => $this->bisaDicatat() && $record->checked)
                    ->modalHeading(fn (CleaningInspectionLine $record): string => 'Clear Result for '.$record->area_name)
                    ->modalDescription('Barisnya kembali berstatus belum diperiksa. Catatan dan fotonya ikut dihapus.')
                    ->modalSubmitActionLabel('Clear Result')
                    ->action(function (CleaningInspectionLine $record): void {
                        // Fotonya ikut dihapus dari penyimpanan, bukan sekadar dilepas dari
                        // barisnya. Berkas yang tidak lagi ditunjuk siapa pun tidak akan pernah
                        // dibuka lagi, tetapi tetap memakan tempat dan tetap bisa dibuka orang
                        // yang menyimpan alamatnya.
                        $record->hapusFoto();

                        $record->forceFill([
                            'result' => null,
                            'notes' => null,
                            'file_path' => null,
                            'original_name' => null,
                            'checked' => false,
                        ])->save();

                        $this->dispatch('rincian-berubah');

                        Notification::make()->success()->title($record->area_name.' kembali belum diperiksa')->send();
                    }),
                Action::make('buka_foto')
                    ->label('Open Photo')
                    ->icon('heroicon-o-photo')
                    ->iconButton()
                    ->visible(fn (CleaningInspectionLine $record): bool => filled($record->file_path))
                    ->url(fn (CleaningInspectionLine $record): ?string => $record->fotoUrl())
                    ->openUrlInNewTab(),
            ])
            /*
             * Penyaring disimpan per sesi peramban, jadi penyaring yang dinyalakan pada satu
             * putaran masih menyala saat putaran lain dibuka. Tanpa pembedaan ini, lembar yang
             * sebenarnya berisi akan berbunyi "daftar areanya kosong" lalu menyuruh pembacanya
             * menyusun ulang daftar, dan menyusun ulang daftar menghapus seluruh hasil yang
             * sudah dicatat. Pesan yang salah di tempat ini tidak sekadar membingungkan, ia
             * menyuruh orang membuang pekerjaannya sendiri.
             */
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada area yang cocok'
                : 'Daftar areanya kosong')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Lembar ini ada isinya, hanya saja tidak ada yang cocok dengan penyaring yang sedang menyala. Bersihkan penyaringnya untuk melihat seluruh area lagi.'
                : $this->pesanKosong());
    }

    protected function bisaDicatat(): bool
    {
        $putaran = $this->getOwnerRecord();

        return $putaran instanceof CleaningInspection && $putaran->bisaDicatat();
    }

    protected function pesanKosong(): string
    {
        $putaran = $this->getOwnerRecord();

        if (! $putaran instanceof CleaningInspection) {
            return 'Belum ada area yang masuk lembar pemeriksaan.';
        }

        return match (true) {
            $putaran->isCancelled() => 'Putaran ini dibatalkan sebelum daftarnya sempat berisi apa pun.',
            default => 'Cakupan putaran ini tidak menghasilkan satu pun area yang masih dipakai. Ubah cakupannya lewat Ubah di atas, lalu tekan Rebuild Area List. Kalau daftar area layanan memang masih kosong, isi dulu di menu Service Areas.',
        };
    }
}
