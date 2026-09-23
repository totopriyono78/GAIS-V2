<?php

namespace App\Filament\Resources\DocumentCategories;

use App\Filament\Resources\DocumentCategories\Pages\ListDocumentCategories;
use App\Models\DocumentCategory;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DocumentCategoryResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = DocumentCategory::class;

    protected static string $moduleCode = 'document_categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static string|UnitEnum|null $navigationGroup = 'Documents';

    protected static ?string $navigationLabel = 'Document Categories';

    protected static ?string $modelLabel = 'document category';

    protected static ?string $pluralModelLabel = 'document categories';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode kategori')
                        ->required()
                        ->maxLength(40)
                        ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*(?:\.[a-z0-9]+(?:-[a-z0-9]+)*)?$/')
                        ->unique(ignoreRecord: true)
                        // Kode dipakai modul lain untuk merujuk kategori. Mengubahnya
                        // setelah dipakai memutus rujukan itu tanpa pesan apa pun, jadi
                        // ia dikunci begitu barisnya ada.
                        ->disabled(fn (?DocumentCategory $record): bool => $record !== null)
                        ->dehydrated(fn (?DocumentCategory $record): bool => $record === null)
                        ->helperText('Huruf kecil dan tanda hubung. Kategori anak memakai kode induknya, titik, lalu namanya sendiri. Contoh: kontrak.sewa')
                        ->placeholder('kontrak.sewa'),
                    TextInput::make('name')
                        ->label('Nama kategori')
                        ->required()
                        ->maxLength(120),
                    Select::make('parent_id')
                        ->label('Kategori induk')
                        ->relationship(
                            name: 'parent',
                            titleAttribute: 'name',
                            // Hanya kategori induk yang boleh dipilih, dan dirinya
                            // sendiri dikeluarkan. Dua duanya yang menjaga kedalaman
                            // berhenti di dua tingkat.
                            modifyQueryUsing: fn (Builder $query, ?DocumentCategory $record): Builder => $query
                                ->whereNull('parent_id')
                                ->when($record?->id, fn (Builder $sub, int $id): Builder => $sub->whereKeyNot($id))
                                ->orderBy('sort_order'),
                        )
                        ->searchable()
                        ->preload()
                        ->placeholder('Tidak ada, ini kategori induk')
                        ->helperText('Dikosongkan berarti kategori ini berdiri sendiri di tingkat pertama.')
                        // Kategori yang sudah punya anak tidak boleh dijadikan anak,
                        // karena itu akan membuat cucu.
                        ->disabled(fn (?DocumentCategory $record): bool => $record?->children()->exists() ?? false),
                    TextInput::make('sort_order')
                        ->label('Urutan tampil')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->helperText('Angka kecil tampil lebih dulu. Kategori berurutan sama diurutkan menurut namanya.'),
                    Toggle::make('is_active')
                        ->label('Kategori dipakai')
                        ->default(true)
                        ->helperText('Dimatikan berarti kategori ini tidak muncul lagi saat mengunggah dokumen baru, tetapi dokumen yang sudah memakainya tidak terganggu.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kategori')
                    ->description(fn (DocumentCategory $record): ?string => $record->parent_id === null
                        ? null
                        : 'Di dalam '.$record->parent->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('children_count')
                    ->label('Subkategori')
                    ->counts('children')
                    ->alignEnd()
                    ->placeholder('0')
                    ->sortable(),
                TextColumn::make('documents_count')
                    ->label('Jumlah dokumen')
                    ->counts('documents')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
            ])
            // Diurutkan menurut kode, bukan nama, karena kode anak selalu diawali
            // kode induknya. Hasilnya pohon yang tersusun benar tanpa perlu query
            // rekursif maupun kolom lintasan tersendiri.
            ->defaultSort('code')
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Kategori induk')
                    ->relationship('parent', 'name', fn (Builder $query): Builder => $query->whereNull('parent_id'))
                    ->placeholder('Semua'),
                TernaryFilter::make('is_active')
                    ->label('Kategori dipakai')
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada kategori dokumen')
            ->emptyStateDescription('Jalankan php artisan db:seed --class=DocumentCategorySeeder untuk mengisi delapan kategori awal beserta subkategorinya, lalu sesuaikan dengan cara perusahaan ini menyimpan dokumennya.');
    }

    /**
     * Kategori yang masih dipakai dokumen, atau yang masih punya subkategori,
     * tidak bisa dihapus. Foreign key sudah menolaknya, tetapi menanyakan lebih
     * dulu membuat tombolnya hilang alih alih memunculkan pesan galat.
     */
    public static function canDelete(Model $record): bool
    {
        if (! $record->bisaDihapus()) {
            return false;
        }

        return static::allows('delete');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentCategories::route('/'),
        ];
    }
}
