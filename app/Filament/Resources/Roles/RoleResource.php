<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Models\Module;
use App\Models\Role;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use UnitEnum;

class RoleResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Role::class;

    protected static string $moduleCode = 'roles';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Access Control';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'role';

    protected static ?string $pluralModelLabel = 'roles';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role Identity')
                ->description('Kode dipakai sistem sebagai penanda tetap. Setelah role dibuat, kodenya tidak bisa diubah.')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode')
                        ->required()
                        ->maxLength(50)
                        ->alphaDash()
                        ->unique(ignoreRecord: true)
                        ->disabled(fn (?Role $record): bool => $record !== null)
                        ->dehydrated(fn (?Role $record): bool => $record === null)
                        ->helperText('Huruf kecil dan tanda hubung, contoh: staf-ga'),
                    TextInput::make('name')
                        ->label('Nama role')
                        ->required()
                        ->maxLength(100),
                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(2)
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Select::make('data_scope')
                        ->label('Cakupan data')
                        ->options(Role::DATA_SCOPES)
                        ->default('all')
                        ->required()
                        ->helperText('Disimpan sekarang untuk dipakai modul transaksional di tahap berikutnya. Di Tahap 1 belum membatasi baris.'),
                    Toggle::make('is_active')
                        ->label('Role aktif')
                        ->default(true)
                        ->helperText('Role yang dinonaktifkan tidak memberi izin apa pun kepada penggunanya.'),
                ]),

            Section::make('Permissions by Module')
                ->description('Centang aksi yang boleh dilakukan role ini. Modul yang aksi Lihat-nya tidak dicentang tidak akan muncul di menu pengguna.')
                ->schema(static::permissionFields()),
        ]);
    }

    /**
     * Matriks izin dibangun dari registri modul, bukan dari daftar yang diketik di kode.
     * Menambah modul baru cukup lewat tabel modules, matriksnya ikut bertambah sendiri.
     */
    protected static function permissionFields(): array
    {
        $modules = static::activeModules();

        if ($modules->isEmpty()) {
            return [
                CheckboxList::make('perm_kosong')
                    ->label('Belum ada modul terdaftar')
                    ->options([])
                    ->helperText('Jalankan php artisan db:seed --class=ModuleSeeder untuk mengisi registri modul.')
                    ->dehydrated(false),
            ];
        }

        $sections = [];

        foreach ($modules->groupBy(fn (Module $module) => $module->group ?: 'Lainnya') as $group => $groupModules) {
            $fields = [];

            foreach ($groupModules as $module) {
                $fields[] = CheckboxList::make('perm_'.$module->code)
                    ->label($module->name)
                    ->helperText($module->description)
                    ->options(collect($module->available_actions ?? [])
                        ->mapWithKeys(fn (string $action) => [
                            $module->code.'.'.$action => Module::actionLabel($action),
                        ])
                        ->all())
                    ->columns(4)
                    ->bulkToggleable()
                    ->gridDirection('row');
            }

            $sections[] = Section::make((string) $group)
                ->schema($fields)
                ->collapsible();
        }

        return $sections;
    }

    public static function activeModules(): Collection
    {
        try {
            return Module::query()
                ->where('is_active', true)
                ->orderBy('group')
                ->orderBy('sort')
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     */
    public static function extractPermissionKeys(array $data): array
    {
        $keys = [];

        foreach (array_keys($data) as $field) {
            if (! str_starts_with((string) $field, 'perm_')) {
                continue;
            }

            $keys = array_merge($keys, (array) $data[$field]);
            unset($data[$field]);
        }

        return [$data, array_values(array_unique($keys))];
    }

    public static function fillPermissionKeys(array $data, Role $role): array
    {
        $owned = $role->permissions()->pluck('key')->all();

        foreach (static::activeModules() as $module) {
            $moduleKeys = array_map(
                fn (string $action) => $module->code.'.'.$action,
                $module->available_actions ?? [],
            );

            $data['perm_'.$module->code] = array_values(array_intersect($owned, $moduleKeys));
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role')
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
                TextColumn::make('permissions_count')
                    ->label('Jumlah izin')
                    ->counts('permissions')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Pengguna')
                    ->counts('users')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('data_scope')
                    ->label('Cakupan data')
                    ->formatStateUsing(fn (?string $state): string => Role::DATA_SCOPES[$state] ?? '-')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada role')
            ->emptyStateDescription('Buat role pertama, misalnya Staf GA, lalu centang modul dan aksi yang boleh diaksesnya.');
    }

    public static function canDelete(Model $record): bool
    {
        // Role bawaan sistem dipakai sebagai jaring pengaman akses, jadi tidak boleh dihapus dari layar.
        if ($record->is_system) {
            return false;
        }

        return static::allows('delete');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
