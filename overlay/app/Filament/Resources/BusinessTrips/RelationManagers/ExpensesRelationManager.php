<?php

namespace App\Filament\Resources\BusinessTrips\RelationManagers;

use App\Models\BusinessTrip;
use App\Models\BusinessTripExpense;
use App\Support\Concerns\DetectsTableFilters;
use App\Support\Rupiah;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Rincian pertanggungjawaban perjalanan.
 *
 * Jumlah seluruh barisnya adalah biaya sebenarnya, dan itu satu satunya tempat angka itu ada.
 * Karena itu jumlahnya ditampilkan di kaki kolom, supaya pemeriksa tidak perlu menjumlah
 * sendiri untuk mencocokkannya dengan uang muka.
 */
class ExpensesRelationManager extends RelationManager
{
    use DetectsTableFilters;

    protected static string $relationship = 'expenses';

    protected static ?string $title = 'Settlement Details';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('expense_date')
                ->label('Tanggal pengeluaran')
                ->native(false)
                ->displayFormat('d M Y')
                ->default(fn (): string => $this->getOwnerRecord()->start_date->toDateString())
                ->required()
                ->helperText('Tanggal ini yang menentukan biaya ini masuk tahun anggaran yang mana.'),
            Select::make('category')
                ->label('Jenis pengeluaran')
                ->options(BusinessTripExpense::CATEGORIES)
                ->default('transport')
                ->required(),
            TextInput::make('description')
                ->label('Keterangan')
                ->required()
                ->maxLength(255)
                ->placeholder('Misalnya tiket kereta Yogyakarta ke Jakarta untuk 3 orang')
                ->helperText('Sebutkan untuk siapa kalau pengeluarannya menyangkut sebagian peserta saja.'),
            TextInput::make('amount')
                ->label('Nilai')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required(),
            FileUpload::make('file_path')
                ->label('Bukti')
                ->disk('public')
                ->directory('bukti-perjalanan')
                ->visibility('public')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->storeFileNamesIn('original_name')
                ->helperText('Foto struk atau PDF, maksimum 10 MB. Baris tanpa bukti adalah baris yang paling sering ditanyakan pemeriksa.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('expense_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => BusinessTripExpense::CATEGORIES[$state] ?? '-'),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(70),
                TextColumn::make('bukti')
                    ->label('Bukti')
                    ->state(fn (BusinessTripExpense $record): string => $record->buktiLabel())
                    ->color(fn (BusinessTripExpense $record): ?string => filled($record->file_path) ? null : 'warning'),
                TextColumn::make('amount')
                    ->label('Nilai')
                    ->state(fn (BusinessTripExpense $record): string => $record->amountLabel())
                    ->alignEnd()
                    ->sortable()
                    // Jumlah seluruh baris adalah biaya sebenarnya perjalanan ini, dan
                    // menampilkannya di kaki kolom membuat pemeriksa tidak perlu menjumlah
                    // sendiri untuk membandingkannya dengan uang muka.
                    ->summarize(
                        Sum::make()
                            ->label('Biaya sebenarnya')
                            ->formatStateUsing(fn ($state): string => Rupiah::penuh((float) $state))
                    ),
            ])
            ->defaultSort('expense_date')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('category')
                    ->label('Jenis pengeluaran')
                    ->options(BusinessTripExpense::CATEGORIES),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Expense')
                    ->modalHeading('Add Expense')
                    /*
                     * Halaman induk diberi tahu setiap kali daftar ini berubah. Biaya
                     * sebenarnya dan kalimat kurang bayar hidup di infolist halaman induk,
                     * yang merupakan komponen Livewire berbeda dan tidak ikut digambar ulang
                     * sendiri. Cacat ini sudah dikenal sejak D-17 pada kiriman K dan D-25
                     * pada kiriman N, jadi dipasang sejak awal di sini.
                     */
                    ->after(fn () => $this->dispatch('rincian-berubah'))
                    ->visible(fn (): bool => $this->bisaDiubah()),
            ])
            ->recordActions([
                Action::make('buka_bukti')
                    ->label('Open Receipt')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->visible(fn (BusinessTripExpense $record): bool => filled($record->file_path))
                    ->url(fn (BusinessTripExpense $record): ?string => $record->buktiUrl())
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->iconButton()
                    ->modalHeading(fn (BusinessTripExpense $record): string => 'Edit '.$record->description)
                    ->after(fn () => $this->dispatch('rincian-berubah'))
                    ->visible(fn (): bool => $this->bisaDiubah()),
                DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(fn (BusinessTripExpense $record): string => 'Remove '.$record->description)
                    ->modalDescription('Menghapus baris ini mengurangi biaya sebenarnya, jadi kurang bayar atau sisa uang mukanya ikut berubah.')
                    ->after(fn () => $this->dispatch('rincian-berubah'))
                    ->visible(fn (): bool => $this->bisaDiubah()),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada pengeluaran yang cocok'
                : 'Belum ada rincian pengeluaran')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Rinciannya ada, hanya saja tidak ada yang cocok dengan penyaring yang sedang menyala. Bersihkan penyaringnya untuk melihat seluruh baris lagi.'
                : $this->pesanKosong());
    }

    protected function bisaDiubah(): bool
    {
        $perjalanan = $this->getOwnerRecord();

        return $perjalanan instanceof BusinessTrip && $perjalanan->rincianBisaDiubah();
    }

    protected function pesanKosong(): string
    {
        $perjalanan = $this->getOwnerRecord();

        if (! $perjalanan instanceof BusinessTrip) {
            return 'Belum ada rincian pengeluaran.';
        }

        return match (true) {
            $perjalanan->isDiajukan() => 'Pengajuannya masih menunggu persetujuan. Rincian biaya baru bisa dicatat setelah perjalanannya disetujui.',
            $perjalanan->isSelesai() => 'Perjalanan ini ditutup tanpa satu pun rincian biaya.',
            $perjalanan->rincianBisaDiubah() => 'Catat tiap pengeluaran satu per satu beserta buktinya, termasuk pengeluaran untuk peserta lain yang ikut berangkat. Jumlah seluruh baris inilah yang dibandingkan dengan uang mukanya.',
            default => 'Tidak ada rincian pengeluaran pada perjalanan ini.',
        };
    }
}
