<?php

namespace App\Filament\Resources\Locations;

use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Models\Location;
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
use Filament\Tables\Table;
use UnitEnum;

class LocationResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Location::class;

    protected static string $moduleCode = 'locations';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?string $navigationLabel = 'Lokasi';

    protected static ?string $modelLabel = 'lokasi';

    protected static ?string $pluralModelLabel = 'lokasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->description('Aset perusahaan berada di satu lokasi, yaitu Head Office. Karena itu yang dicatat di sini adalah kedalamannya: gedung, lantai, ruangan, sampai area.')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true)
                        ->helperText('Kode singkat yang nanti ikut tercetak di label aset, contoh: HO-L3-R05'),
                    TextInput::make('name')
                        ->label('Nama lokasi')
                        ->required()
                        ->maxLength(150),
                    Select::make('type')
                        ->label('Jenis')
                        ->options(Location::TYPES)
                        ->default('ruangan')
                        ->required(),
                    Select::make('parent_id')
                        ->label('Berada di dalam')
                        ->options(fn (?Location $record): array => Location::query()
                            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                            ->orderBy('code')
                            ->get()
                            ->mapWithKeys(fn (Location $location) => [
                                $location->id => $location->code.' '.$location->name,
                            ])
                            ->all())
                        ->searchable()
                        ->placeholder('Lokasi teratas'),
                    TextInput::make('description')
                        ->label('Keterangan')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Lokasi dipakai')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Lokasi')
                    ->description(fn (Location $record): ?string => $record->parent?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => Location::TYPES[$state] ?? '-'),
                TextColumn::make('description')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis lokasi')
                    ->options(Location::TYPES),
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
            ->emptyStateHeading('Belum ada lokasi')
            ->emptyStateDescription('Mulai dari gedung Head Office, lalu tambahkan lantai dan ruangannya. Struktur ini yang dipakai untuk melacak posisi aset.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocations::route('/'),
        ];
    }
}
