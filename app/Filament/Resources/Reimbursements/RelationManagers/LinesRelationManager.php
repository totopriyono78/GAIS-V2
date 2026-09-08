<?php

namespace App\Filament\Resources\Reimbursements\RelationManagers;

use App\Filament\Resources\Reimbursements\ReimbursementResource;
use App\Models\ExpenseCategory;
use App\Models\Reimbursement;
use App\Models\ReimbursementLine;
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
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/**
 * Struk struk dalam satu pengajuan.
 *
 * Satu baris adalah satu struk, dan fotonya menempel di barisnya sendiri. Menaruh berkas
 * di tingkat pengajuan akan memaksa orang menggabung lima struk jadi satu PDF sebelum bisa
 * mengunggahnya, dan itu pekerjaan yang tidak perlu ada.
 *
 * Foto boleh kosong dan tidak memblokir pengajuan. Struk memang kadang hilang, dan menutup
 * jalannya berarti memaksa orang mengarang berkas supaya bisa lanjut. Yang dilakukan
 * aplikasi adalah menghitung berapa yang belum ada fotonya dan menyebutkannya di kotak
 * persetujuan, sehingga tim GA memutuskan dengan tahu.
 */
class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Receipts';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            DatePicker::make('expense_date')
                ->label('Tanggal struk')
                ->displayFormat('d M Y')
                ->required()
                ->default(now())
                ->maxDate(now())
                ->helperText('Tanggal yang tertulis di struk, bukan tanggal pengajuan. Tanggal inilah yang menentukan tahun anggaran mana yang terbebani.'),
            Select::make('expense_category_id')
                ->label('Kategori biaya')
                ->options(fn (): array => ExpenseCategory::query()
                    ->where('is_active', true)
                    ->where('source', 'tagihan')
                    ->orderBy('code')
                    ->get()
                    ->mapWithKeys(fn (ExpenseCategory $k) => [$k->id => $k->pickerLabel()])
                    ->all())
                ->searchable()
                ->required()
                ->helperText('Hanya kategori yang realisasinya memang datang dari struk dan faktur. Pemeliharaan dan BBM tidak ada di daftar ini karena sudah dijumlahkan dari perintah kerja dan pengisian BBM.'),
            TextInput::make('description')
                ->label('Keterangan')
                ->required()
                ->maxLength(200)
                ->columnSpanFull()
                ->placeholder('Contoh: taksi kantor ke Samsat Jakarta Selatan')
                ->helperText('Cukup satu baris. Ini yang dibaca tim GA sambil mencocokkan dengan fotonya.'),
            TextInput::make('amount')
                ->label('Nilai')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required()
                ->helperText('Sesuai yang tertulis di struk.'),
            FileUpload::make('file_path')
                ->label('Foto struk')
                ->disk('public')
                ->directory('struk-penggantian')
                ->visibility('public')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240)
                ->openable()
                ->downloadable()
                ->storeFileNamesIn('original_name')
                ->helperText('Foto atau PDF, maksimum 10 MB. Boleh dikosongkan kalau struknya benar benar hilang, tetapi tim GA akan menanyakannya.'),
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
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->description(fn (ReimbursementLine $record): ?string => $record->category?->name)
                    ->wrap(),
                TextColumn::make('file_path')
                    ->label('Bukti')
                    ->state(fn (ReimbursementLine $record): string => $record->buktiLabel())
                    ->color(fn (ReimbursementLine $record): ?string => blank($record->file_path) ? 'warning' : null),
                TextColumn::make('amount')
                    ->label('Nilai')
                    ->state(fn (ReimbursementLine $record): string => $record->amountLabel())
                    ->alignEnd()
                    ->sortable()
                    // Jumlah seluruh struk adalah nilai pengajuannya, dan itu satu satunya
                    // tempat angka itu ada. Menampilkannya di kaki kolom membuat pemeriksa
                    // tidak perlu menjumlah sendiri untuk mencocokkan dengan amplopnya.
                    ->summarize(
                        Sum::make()
                            ->label('Nilai pengajuan')
                            ->formatStateUsing(fn ($state): string => Rupiah::penuh((float) $state))
                    ),
            ])
            ->defaultSort('expense_date')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Receipt')
                    ->visible(fn (): bool => $this->bisaDiubah()),
            ])
            ->recordActions([
                Action::make('buka')
                    ->label('Open Receipt Photo')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->visible(fn (ReimbursementLine $record): bool => filled($record->file_path))
                    ->url(fn (ReimbursementLine $record): ?string => filled($record->file_path)
                        ? Storage::disk('public')->url($record->file_path)
                        : null)
                    ->openUrlInNewTab(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => $this->bisaDiubah()),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => $this->bisaDiubah())
                    ->modalDescription('Menghapus baris ini mengurangi nilai pengajuannya, karena nilai pengajuan adalah jumlah seluruh struknya.'),
            ])
            ->emptyStateHeading('Belum ada struk')
            ->emptyStateDescription($this->bisaDiubah()
                ? 'Tambahkan satu baris per struk: tanggalnya, keperluannya, kategori biayanya, nilainya, dan fotonya. Jumlah seluruh baris inilah nilai pengajuannya, dan pengajuan tanpa struk tidak bisa diajukan.'
                : 'Pengajuan ini sudah tidak berstatus draf, jadi struknya tidak bisa diubah lagi. Untuk memperbaikinya, pengajuan perlu ditolak lebih dulu lalu dikembalikan ke draf.');
    }

    protected function bisaDiubah(): bool
    {
        $pengajuan = $this->getOwnerRecord();

        return $pengajuan instanceof Reimbursement
            && $pengajuan->rincianBisaDiubah()
            && ReimbursementResource::canEdit($pengajuan);
    }
}
