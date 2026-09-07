<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Permission;
use App\Models\User;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UserResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = User::class;

    protected static string $moduleCode = 'users';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan Akses';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'pengguna';

    protected static ?string $pluralModelLabel = 'pengguna';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Akun')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(150),
                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true),
                    TextInput::make('password')
                        ->label('Kata sandi')
                        ->password()
                        ->revealable()
                        ->minLength(8)
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText('Saat mengubah pengguna, kosongkan kolom ini kalau kata sandinya tidak diganti.'),
                    Toggle::make('is_active')
                        ->label('Akun aktif')
                        ->default(true)
                        ->helperText('Akun yang dinonaktifkan tidak bisa masuk ke aplikasi.'),
                    Toggle::make('is_super_admin')
                        ->label('Super admin')
                        ->helperText('Melewati seluruh pemeriksaan izin. Hanya super admin yang boleh memberikan status ini.')
                        ->visible(fn (): bool => (bool) Auth::user()?->is_super_admin)
                        ->dehydrated(fn (): bool => (bool) Auth::user()?->is_super_admin),
                ]),

            Section::make('Role')
                ->description('Izin pengguna adalah gabungan izin dari semua role aktif yang dipilih.')
                ->schema([
                    CheckboxList::make('roles')
                        ->label('Role yang dipegang')
                        ->relationship('roles', 'name')
                        ->columns(3)
                        ->gridDirection('row')
                        ->bulkToggleable(),
                ]),

            Section::make('Izin khusus pengguna ini')
                ->description('Dipakai untuk pengecualian, misalnya satu orang yang boleh menghapus data padahal rolenya tidak. Pengecualian menang atas role.')
                ->collapsed()
                ->schema([
                    CheckboxList::make('extra_permissions')
                        ->label('Izin tambahan di luar role')
                        ->options(fn (): array => static::permissionOptions())
                        ->columns(2)
                        ->gridDirection('row')
                        ->searchable(),
                    CheckboxList::make('revoked_permissions')
                        ->label('Izin yang dicabut walaupun diberikan role')
                        ->options(fn (): array => static::permissionOptions())
                        ->columns(2)
                        ->gridDirection('row')
                        ->searchable(),
                ]),
        ]);
    }

    public static function permissionOptions(): array
    {
        try {
            return Permission::query()
                ->orderBy('key')
                ->get()
                ->mapWithKeys(fn (Permission $permission) => [
                    $permission->id => $permission->name.' ('.$permission->key.')',
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public static function fillOverrides(array $data, User $user): array
    {
        $data['extra_permissions'] = $user->permissionOverrides()
            ->wherePivot('granted', true)
            ->pluck('permissions.id')
            ->all();

        $data['revoked_permissions'] = $user->permissionOverrides()
            ->wherePivot('granted', false)
            ->pluck('permissions.id')
            ->all();

        return $data;
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, array<string, bool>>}
     */
    public static function extractOverrides(array $data): array
    {
        $extra = (array) ($data['extra_permissions'] ?? []);
        $revoked = (array) ($data['revoked_permissions'] ?? []);

        unset($data['extra_permissions'], $data['revoked_permissions']);

        $sync = [];

        foreach ($extra as $id) {
            $sync[(int) $id] = ['granted' => true];
        }

        // Pencabutan dievaluasi terakhir, jadi kalau satu izin dicentang di kedua kolom, hasilnya dicabut.
        foreach ($revoked as $id) {
            $sync[(int) $id] = ['granted' => false];
        }

        return [$data, $sync];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->description(fn (User $record): string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Belum ada role'),
                IconColumn::make('is_super_admin')
                    ->label('Super admin')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('last_login_at')
                    ->label('Terakhir masuk')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum pernah masuk')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status akun')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif')
                    ->placeholder('Semua'),
                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
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
            ->emptyStateHeading('Belum ada pengguna')
            ->emptyStateDescription('Tambahkan akun untuk staf GA, lalu berikan role sesuai tugasnya.');
    }

    public static function canDelete(Model $record): bool
    {
        // Mencegah pengguna menghapus akunnya sendiri lalu kehilangan akses.
        if ($record->getKey() === Auth::id()) {
            return false;
        }

        return static::allows('delete');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
