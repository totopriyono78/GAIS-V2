<?php

namespace App\Filament\Resources\Budgets;

use App\Filament\Resources\Budgets\Pages\ListBudgets;
use App\Models\Budget;
use App\Models\Department;
use App\Models\ExpenseCategory;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Anggaran versus realisasi.
 *
 * Yang disimpan di layar ini hanya pagunya. Kolom Realisasi, Sisa, dan Terpakai tidak
 * pernah diketik siapa pun: ketiganya dijumlahkan saat halaman dibuka, dari perintah
 * kerja yang ditutup, pengisian BBM yang dicatat, pajak yang diperpanjang, barang yang
 * keluar gudang, dan tagihan rekanan yang sudah disetujui. Itulah satu satunya alasan
 * modul ini lebih berguna daripada spreadsheet, dan alasan angkanya masih benar tiga
 * bulan dari sekarang.
 *
 * Tagihan yang fakturnya belum disetujui disebut terpisah di bawah angka realisasi, bukan
 * dijumlahkan ke dalamnya. Uang itu memang belum keluar, tetapi menyembunyikannya membuat
 * pagu terlihat sehat tepat pada saat ia sedang tidak sehat.
 */
class BudgetResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Budget::class;

    protected static string $moduleCode = 'budgets';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|UnitEnum|null $navigationGroup = 'Budget & Expenses';

    protected static ?string $navigationLabel = 'Budgets';

    protected static ?string $modelLabel = 'budget';

    protected static ?string $pluralModelLabel = 'budgets';

    protected static ?int $navigationSort = 10;

    /** Jumlah pagu yang sudah terlewati tahun ini. */
    public static function getNavigationBadge(): ?string
    {
        $lewat = Budget::query()
            ->tahun((int) now()->format('Y'))
            ->with(['category'])
            ->get()
            ->filter(fn (Budget $b): bool => $b->keadaan() === 'lewat')
            ->count();

        return $lewat > 0 ? (string) $lewat : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('department_id')
                ->label('Departemen')
                ->options(fn (): array => Department::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            Select::make('expense_category_id')
                ->label('Kategori biaya')
                ->options(fn (): array => ExpenseCategory::query()
                    ->where('is_active', true)
                    ->orderBy('code')
                    ->get()
                    ->mapWithKeys(fn (ExpenseCategory $k) => [$k->id => $k->pickerLabel()])
                    ->all())
                ->searchable()
                ->required()
                ->live()
                ->helperText(fn ($get): string => ExpenseCategory::find($get('expense_category_id'))?->sourceLabel()
                    ?? 'Pilih kategorinya untuk melihat dari mana realisasinya akan dijumlahkan.'),
            TextInput::make('fiscal_year')
                ->label('Tahun anggaran')
                ->numeric()
                ->minValue(2020)
                ->maxValue((int) now()->addYears(3)->format('Y'))
                ->default((int) now()->format('Y'))
                ->required(),
            TextInput::make('amount')
                ->label('Pagu setahun')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required()
                ->helperText('Untuk satu tahun penuh. Realisasinya dijumlahkan dari 1 Januari sampai 31 Desember tahun itu.'),
            Textarea::make('notes')
                ->label('Catatan')
                ->rows(2)
                ->columnSpanFull()
                ->placeholder('Contoh: naik 15 persen dari tahun lalu karena penambahan lantai 4.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fiscal_year')
                    ->label('Tahun')
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->description(fn (Budget $record): ?string => $record->category?->sourceLabel())
                    ->searchable()
                    ->wrap(),
                TextColumn::make('amount')
                    ->label('Pagu')
                    ->state(fn (Budget $record): string => Rupiah::penuh((float) $record->amount))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('realisasi')
                    ->label('Realisasi')
                    ->state(fn (Budget $record): string => Rupiah::penuh($record->realisasi()))
                    // Nilai yang fakturnya belum disetujui disebut di sini, bukan
                    // dijumlahkan ke atasnya. Yang membaca pagu berhak tahu ada berapa
                    // yang sedang menunggu tanda tangan di belakang angka ini.
                    ->description(fn (Budget $record): string => $record->sisaLabel()
                        .($record->tertundaLabel() !== null ? '. '.$record->tertundaLabel() : ''))
                    ->color(fn (Budget $record): ?string => $record->tertunda() > 0 ? 'warning' : null)
                    ->alignEnd(),
                TextColumn::make('keadaan')
                    ->label('Terpakai')
                    ->badge()
                    ->state(fn (Budget $record): string => $record->persenLabel())
                    ->color(fn (Budget $record): string => $record->keadaanColor())
                    ->description(fn (Budget $record): string => $record->keadaanLabel()),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fiscal_year', 'desc')
            /*
             * Yang paling mendesak dibaca adalah pagu yang sudah lewat, dan itu tidak bisa
             * diurutkan lewat SQL karena realisasinya baru dihitung di PHP. Yang bisa
             * dilakukan di sini adalah memuat relasinya sekaligus, supaya tiga puluh baris
             * tidak menjadi enam puluh query.
             */
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['department', 'category']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('fiscal_year')
                    ->label('Tahun anggaran')
                    ->options(fn (): array => Budget::query()
                        ->distinct()
                        ->orderByDesc('fiscal_year')
                        ->pluck('fiscal_year', 'fiscal_year')
                        ->all())
                    ->default((int) now()->format('Y')),
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable(),
                SelectFilter::make('expense_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable(),
                Filter::make('lewat_pagu')
                    ->label('Sudah melewati pagu')
                    // Penyaring ini bekerja di PHP lewat daftar id, karena realisasinya
                    // memang tidak ada di basis data untuk dibandingkan lewat SQL.
                    ->query(function (Builder $query): Builder {
                        $lewat = Budget::query()
                            ->with('category')
                            ->get()
                            ->filter(fn (Budget $b): bool => $b->keadaan() === 'lewat')
                            ->pluck('id')
                            ->all();

                        return $query->whereIn('id', $lewat ?: [0]);
                    }),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->modalDescription('Yang dihapus hanya pagunya. Biaya yang sudah terlanjur tercatat di perintah kerja, pengisian BBM, dan mutasi barang tidak ikut hilang.'),
            ])
            ->emptyStateHeading('Belum ada pagu anggaran')
            ->emptyStateDescription('Tetapkan pagu per departemen untuk tiap kategori biaya dalam satu tahun. Realisasinya tidak perlu diketik: aplikasi menjumlahkannya sendiri dari perintah kerja yang ditutup, pengisian BBM, pajak kendaraan, barang yang keluar gudang, dan tagihan rekanan yang sudah disetujui.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgets::route('/'),
        ];
    }
}
