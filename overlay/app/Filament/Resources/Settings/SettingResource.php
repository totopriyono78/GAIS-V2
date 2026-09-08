<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Models\Setting;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SettingResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Setting::class;

    protected static string $moduleCode = 'settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $modelLabel = 'pengaturan';

    protected static ?string $pluralModelLabel = 'pengaturan';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->schema([
                    TextInput::make('label')
                        ->label('Nama pengaturan')
                        ->disabled()
                        ->dehydrated(false),
                    /*
                     * Pengaturan yang jawabannya sudah pasti ditampilkan sebagai daftar
                     * pilihan, bukan kotak teks. Kotak teks pada pengaturan seperti ini
                     * mengundang salah eja, dan salah eja pada kebijakan penyusutan
                     * berarti angka yang salah di seluruh laporan tanpa ada yang tahu.
                     */
                    Select::make('value')
                        ->label('Nilai')
                        ->options(fn (?Setting $record): array => Setting::choicesFor($record?->key))
                        ->native(false)
                        ->required()
                        ->visible(fn (?Setting $record): bool => (bool) $record?->hasChoices())
                        ->helperText(fn (?Setting $record): ?string => $record?->description),
                    Textarea::make('value')
                        ->label('Nilai')
                        ->rows(fn (?Setting $record): int => $record?->type === 'textarea' ? 4 : 2)
                        ->maxLength(2000)
                        ->visible(fn (?Setting $record): bool => ! $record?->hasChoices())
                        ->helperText(fn (?Setting $record): string => trim(($record?->description ?? '').
                            ($record?->type === 'boolean' ? ' Isi 1 untuk ya, 0 untuk tidak.' : ''))),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Pengaturan')
                    ->description(fn (Setting $record): ?string => $record->description)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->label('Kunci')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('value')
                    ->label('Nilai')
                    ->state(fn (Setting $record): ?string => $record->valueLabel())
                    ->placeholder('Belum diisi')
                    ->wrap(),
                TextColumn::make('group')
                    ->label('Kelompok')
                    ->sortable(),
            ])
            ->defaultSort('group')
            ->filters([
                SelectFilter::make('group')
                    ->label('Kelompok')
                    ->options(fn (): array => Setting::query()
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make()->label('Ubah nilai')->iconButton(),
            ])
            ->emptyStateHeading('Belum ada pengaturan')
            ->emptyStateDescription('Jalankan php artisan db:seed --class=SettingSeeder untuk mengisi pengaturan bawaan.');
    }

    // Daftar pengaturan ditentukan oleh fitur yang ada, bukan diketik bebas dari layar.
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
            'index' => ListSettings::route('/'),
        ];
    }
}
