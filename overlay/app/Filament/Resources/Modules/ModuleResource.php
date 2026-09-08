<?php

namespace App\Filament\Resources\Modules;

use App\Filament\Resources\Modules\Pages\ListModules;
use App\Models\Module;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ModuleResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Module::class;

    protected static string $moduleCode = 'modules';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan Akses';

    protected static ?string $navigationLabel = 'Modul';

    protected static ?string $modelLabel = 'modul';

    protected static ?string $pluralModelLabel = 'modul';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Modul')
                ->columnSpanFull()
                ->description('Registri ini adalah sumber daftar izin. Menyimpan perubahan di sini langsung menyamakan baris izin yang tersedia di layar role.')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode modul')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Kode ditetapkan saat modul dibangun dan tidak bisa diubah dari layar.'),
                    TextInput::make('name')
                        ->label('Nama tampilan')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('group')
                        ->label('Kelompok menu')
                        ->maxLength(50),
                    TextInput::make('sort')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0),
                    TextInput::make('description')
                        ->label('Keterangan')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    CheckboxList::make('available_actions')
                        ->label('Aksi yang tersedia untuk modul ini')
                        ->options(Module::ACTIONS)
                        ->columns(4)
                        ->gridDirection('row')
                        ->required()
                        ->columnSpanFull()
                        ->helperText('Mencabut satu aksi akan menghapus izin aksi itu dari semua role.'),
                    Toggle::make('is_active')
                        ->label('Modul aktif')
                        ->helperText('Modul yang dinonaktifkan tidak muncul di matriks izin.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Modul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('group')
                    ->label('Kelompok')
                    ->sortable(),
                TextColumn::make('available_actions')
                    ->label('Aksi tersedia')
                    ->state(fn (Module $record): string => collect($record->available_actions ?? [])
                        ->map(fn (string $action): string => Module::actionLabel($action))
                        ->join(', '))
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('permissions_count')
                    ->label('Izin')
                    ->counts('permissions')
                    ->alignEnd(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make()->iconButton(),
            ])
            ->emptyStateHeading('Registri modul kosong')
            ->emptyStateDescription('Jalankan php artisan db:seed --class=ModuleSeeder untuk mengisi daftar modul yang sudah dibangun.');
    }

    // Modul dibuat bersamaan dengan layarnya oleh pengembang, bukan dari antarmuka,
    // supaya tidak ada modul terdaftar yang tidak punya halaman.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModules::route('/'),
        ];
    }
}
