<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Models\Department;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class DepartmentResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Department::class;

    protected static string $moduleCode = 'departments';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Departments';

    protected static ?string $modelLabel = 'department';

    protected static ?string $pluralModelLabel = 'departments';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Kode')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true),
                    TextInput::make('name')
                        ->label('Nama departemen')
                        ->required()
                        ->maxLength(150),
                    TextInput::make('cost_center')
                        ->label('Cost center')
                        ->maxLength(50)
                        ->helperText('Dipakai untuk membebankan biaya GA ke departemen di tahap anggaran.'),
                    Select::make('parent_id')
                        ->label('Induk departemen')
                        ->options(fn (?Department $record): array => Department::query()
                            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->placeholder('Tidak ada induk'),
                    Select::make('head_employee_id')
                        ->label('Kepala departemen')
                        ->relationship('head', 'full_name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Belum ditentukan'),
                    Toggle::make('is_active')
                        ->label('Departemen aktif')
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
                    ->label('Departemen')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label('Induk')
                    ->placeholder('Tidak ada')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('head.full_name')
                    ->label('Kepala')
                    ->placeholder('Belum ditentukan'),
                TextColumn::make('employees_count')
                    ->label('Karyawan')
                    ->counts('employees')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('cost_center')
                    ->label('Cost center')
                    ->placeholder('Belum diisi')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Keaktifan')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak aktif')
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
            ->emptyStateHeading('Belum ada departemen')
            ->emptyStateDescription('Tambahkan departemen sesuai struktur perusahaan Anda. Data ini dipakai untuk pembebanan biaya dan penanggung jawab aset.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
        ];
    }
}
