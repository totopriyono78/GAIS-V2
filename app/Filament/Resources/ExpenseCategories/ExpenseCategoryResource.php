<?php

namespace App\Filament\Resources\ExpenseCategories;

use App\Filament\Resources\ExpenseCategories\Pages\ListExpenseCategories;
use App\Models\ExpenseCategory;
use App\Services\RealisasiBiaya;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Kategori biaya GA.
 *
 * Kolom yang paling menentukan di layar ini adalah Sumber realisasi. Kolom itu yang
 * memutuskan dari catatan mana angka realisasi kategori ini dijumlahkan, dan karenanya
 * juga kapan angkanya lengkap. Karena itu ia muncul sebagai kolom, bukan disembunyikan
 * di formulir.
 */
class ExpenseCategoryResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = ExpenseCategory::class;

    protected static string $moduleCode = 'expense_categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?string $navigationLabel = 'Kategori biaya';

    protected static ?string $modelLabel = 'kategori biaya';

    protected static ?string $pluralModelLabel = 'kategori biaya';

    protected static ?int $navigationSort = 60;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('code')
                ->label('Kode')
                ->required()
                ->maxLength(30)
                ->unique(ignoreRecord: true)
                ->placeholder('Contoh: PMLH'),
            TextInput::make('name')
                ->label('Nama kategori')
                ->required()
                ->maxLength(150)
                ->placeholder('Contoh: Pemeliharaan gedung dan peralatan'),
            Select::make('source')
                ->label('Sumber realisasi')
                ->options(RealisasiBiaya::SOURCES)
                ->default('tagihan')
                ->required()
                ->live()
                ->columnSpanFull()
                ->helperText(fn ($get): string => match ($get('source')) {
                    'tagihan' => 'Realisasinya dijumlahkan dari tagihan rekanan dan struk penggantian biaya karyawan yang sudah disetujui. Yang masih menunggu persetujuan tampil terpisah di layar anggaran, bukan ikut dijumlahkan.',
                    default => 'Realisasinya dijumlahkan sendiri dari catatan yang sudah ada, dan dibebankan ke departemen mengikuti jejak asetnya. Tidak ada yang perlu mengetik ulang angkanya.',
                }),
            TextInput::make('account_code')
                ->label('Nomor akun')
                ->maxLength(30)
                ->placeholder('Belum dipetakan')
                ->helperText('Nomor akun di sistem akuntansi perusahaan. Biarkan kosong sampai tim finance menyebutkannya, karena nomor yang dikarang akan terbawa sampai ke jurnal.'),
            Toggle::make('is_active')
                ->label('Masih dipakai')
                ->default(true),
            Textarea::make('description')
                ->label('Keterangan')
                ->rows(2)
                ->columnSpanFull()
                ->maxLength(500)
                ->placeholder('Contoh: seluruh biaya servis, suku cadang, dan jasa tukang untuk gedung dan peralatan kantor.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Kategori')
                    ->description(fn (ExpenseCategory $record): ?string => $record->description)
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                TextColumn::make('source')
                    ->label('Sumber realisasi')
                    ->badge()
                    // Lencananya menjawab "kapan angkanya lengkap", bukan "otomatis atau
                    // tidak". Kelimanya sama sama dijumlahkan sendiri; yang berbeda adalah
                    // biaya BBM sudah tercatat pada hari pengisian, sedangkan biaya listrik
                    // baru muncul setelah fakturnya masuk dan disetujui.
                    ->state(fn (ExpenseCategory $record): string => $record->kesiapanLabel())
                    ->color(fn (ExpenseCategory $record): string => $record->dariTagihan() ? 'info' : 'success')
                    ->description(fn (ExpenseCategory $record): string => $record->sourceLabel())
                    ->wrap(),
                TextColumn::make('account_code')
                    ->label('Nomor akun')
                    ->state(fn (ExpenseCategory $record): string => $record->accountLabel())
                    ->color(fn (ExpenseCategory $record): ?string => filled($record->account_code) ? null : 'gray')
                    ->fontFamily('mono'),
                TextColumn::make('budgets_count')
                    ->label('Pagu')
                    ->counts('budgets')
                    ->alignEnd()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('source')
                    ->label('Sumber realisasi')
                    ->options(RealisasiBiaya::SOURCES)
                    ->multiple(),
                Filter::make('belum_dipetakan')
                    ->label('Belum punya nomor akun')
                    ->query(fn (Builder $query): Builder => $query->whereNull('account_code')),
                TernaryFilter::make('is_active')
                    ->label('Status pemakaian')
                    ->placeholder('Semua')
                    ->trueLabel('Masih dipakai')
                    ->falseLabel('Sudah dimatikan'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (ExpenseCategory $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada kategori biaya')
            ->emptyStateDescription('Kategori biaya adalah tempat pagu anggaran ditetapkan dan realisasi dijumlahkan. Tambahkan lewat tombol di kanan atas, lalu tentukan sumber realisasinya supaya aplikasi tahu dari catatan mana angkanya diambil.');
    }

    /**
     * Kategori yang sudah punya pagu tidak boleh dihapus. Menghapusnya membuang seluruh
     * pagu departemen yang menempel padanya, dan itu terjadi tanpa peringatan apa pun
     * kalau hanya mengandalkan cascade basis data.
     */
    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && ! $record->budgets()->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenseCategories::route('/'),
        ];
    }
}
