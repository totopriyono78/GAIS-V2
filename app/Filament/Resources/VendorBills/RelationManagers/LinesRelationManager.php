<?php

namespace App\Filament\Resources\VendorBills\RelationManagers;

use App\Filament\Resources\VendorBills\VendorBillResource;
use App\Models\Department;
use App\Models\ExpenseCategory;
use App\Models\VendorBill;
use App\Models\VendorBillLine;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Rincian pembebanan satu tagihan.
 *
 * Inilah yang membuat tagihan berguna untuk anggaran, bukan sekadar arsip faktur. Satu
 * tagihan listrik dibagi ke lima departemen di sini, dan jumlah seluruh barisnya adalah
 * nilai tagihannya. Tidak ada tempat lain di mana totalnya bisa diketik, sehingga tidak
 * ada keadaan di mana total dan rinciannya berbeda.
 *
 * Pilihan kategorinya sengaja dibatasi pada kategori yang bersumber tagihan. Kategori
 * seperti pemeliharaan dan BBM sudah dijumlahkan dari catatan aslinya di modul lain, dan
 * memasukkan fakturnya lagi di sini akan membuat angkanya terhitung dua kali di layar
 * anggaran, dalam bentuk yang sangat sulit ditemukan orang.
 */
class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    protected static ?string $title = 'Cost Allocation';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
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
                ->helperText('Hanya kategori yang realisasinya memang datang dari tagihan. Pemeliharaan dan BBM tidak ada di daftar ini karena sudah dijumlahkan dari perintah kerja dan pengisian BBM, dan memasukkannya lagi akan terhitung dua kali.'),
            Select::make('department_id')
                ->label('Departemen yang dibebani')
                ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->placeholder('Belum terbebankan ke departemen')
                ->helperText('Kosongkan untuk biaya kantor bersama seperti listrik koridor. Angkanya tetap dihitung, dan muncul di layar anggaran sebagai biaya yang belum bisa dibebankan.'),
            TextInput::make('amount')
                ->label('Nilai')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required()
                ->helperText('Nilai yang dibebankan pada baris ini, bukan nilai seluruh fakturnya.'),
            Textarea::make('description')
                ->label('Keterangan')
                ->rows(2)
                ->maxLength(200)
                ->placeholder('Contoh: pemakaian lantai 3, 1.240 kWh.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategori biaya')
                    ->description(fn (VendorBillLine $record): ?string => $record->category?->code)
                    ->wrap(),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->state(fn (VendorBillLine $record): string => $record->departemenLabel())
                    ->color(fn (VendorBillLine $record): ?string => $record->department === null ? 'warning' : null)
                    ->wrap(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(80),
                TextColumn::make('amount')
                    ->label('Nilai')
                    ->state(fn (VendorBillLine $record): string => $record->amountLabel())
                    ->alignEnd()
                    ->sortable()
                    // Jumlah seluruh baris adalah nilai tagihannya, dan itu satu satunya
                    // tempat angka itu ada. Menampilkannya di kaki kolom membuat orang
                    // tidak perlu menjumlah sendiri untuk memeriksa kecocokannya dengan
                    // faktur di tangannya.
                    ->summarize(
                        Sum::make()
                            ->label('Nilai tagihan')
                            ->formatStateUsing(fn ($state): string => Rupiah::penuh((float) $state))
                    ),
            ])
            ->defaultSort('id')
            ->headerActions([
                CreateAction::make()
                    ->label('Add Allocation')
                    ->after(fn () => $this->segarkanHalaman())
                    ->visible(fn (): bool => $this->bisaDiubah()),
            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->after(fn () => $this->segarkanHalaman())
                    ->visible(fn (): bool => $this->bisaDiubah()),
                DeleteAction::make()
                    ->iconButton()
                    ->after(fn () => $this->segarkanHalaman())
                    ->visible(fn (): bool => $this->bisaDiubah())
                    ->modalDescription('Menghapus baris ini mengurangi nilai tagihannya, karena nilai tagihan adalah jumlah seluruh barisnya.'),
            ])
            ->emptyStateHeading('Belum ada rincian pembebanan')
            ->emptyStateDescription($this->bisaDiubah()
                ? 'Tambahkan minimal satu baris: kategori biayanya, departemen yang memakainya, dan nilainya. Jumlah seluruh baris inilah nilai tagihannya, dan tagihan tanpa rincian tidak bisa diajukan untuk disetujui.'
                : 'Tagihan ini sudah tidak berstatus draf, jadi rinciannya tidak bisa diubah lagi. Untuk memperbaikinya, tagihan perlu ditolak lebih dulu lalu dikembalikan ke draf.');
    }

    /**
     * Menggambar ulang halaman induk setelah baris berubah.
     *
     * Nilai tagihan, dan sejak kiriman N juga selisihnya terhadap barang yang sudah diterima,
     * dihitung dari baris baris ini tetapi ditampilkan di infolist halaman induk. Halaman
     * induk adalah komponen Livewire yang berbeda dan tidak ikut digambar ulang saat baris di
     * sini berubah, jadi tanpa penyegaran ini angkanya tetap memperlihatkan keadaan sebelum
     * baris terakhir ditambahkan.
     *
     * Untuk nilai tagihan, angka basi hanya membingungkan sebentar. Untuk kalimat selisih,
     * angka basi berbahaya: kalimat itu ada justru untuk menahan orang menandatangani faktur
     * yang menagih lebih banyak daripada barang yang datang, dan kalimat yang salah lebih
     * buruk daripada tidak ada kalimat sama sekali.
     */
    protected function segarkanHalaman(): void
    {
        $this->dispatch('rincian-berubah');
    }

    /**
     * Rincian hanya bisa diubah selama tagihan masih draf dan orangnya memang boleh
     * mengubah tagihan. Setelah diajukan, mengubah barisnya berarti mengubah angka yang
     * sedang atau sudah ditandatangani orang lain tanpa ia tahu.
     */
    protected function bisaDiubah(): bool
    {
        $tagihan = $this->getOwnerRecord();

        return $tagihan instanceof VendorBill
            && $tagihan->rincianBisaDiubah()
            && VendorBillResource::canEdit($tagihan);
    }
}
