<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Models\Employee;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
use UnitEnum;

class EmployeeResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = Employee::class;

    protected static string $moduleCode = 'employees';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Employees';

    protected static ?string $modelLabel = 'employee';

    protected static ?string $pluralModelLabel = 'employees';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Employee Data')
                ->columns(2)
                ->schema([
                    TextInput::make('nip')
                        ->label('NIP')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true),
                    TextInput::make('full_name')
                        ->label('Nama lengkap')
                        ->required()
                        ->maxLength(150),
                    TextInput::make('email')
                        ->label('Email kantor')
                        ->email()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true),
                    TextInput::make('phone')
                        ->label('Nomor telepon')
                        ->tel()
                        ->maxLength(30),
                    Select::make('department_id')
                        ->label('Departemen')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload(),
                    TextInput::make('position')
                        ->label('Jabatan')
                        ->maxLength(100),
                    Select::make('employment_status')
                        ->label('Status kepegawaian')
                        ->options(Employee::EMPLOYMENT_STATUSES)
                        ->default('tetap')
                        ->required(),
                    DatePicker::make('join_date')
                        ->label('Tanggal bergabung')
                        ->displayFormat('d M Y'),
                ]),

            Section::make('Application Account')
                ->description('Hubungkan karyawan ini dengan akun sistem kalau dia perlu masuk ke aplikasi. Karyawan tanpa akun tetap bisa dicatat sebagai penanggung jawab aset.')
                ->columns(2)
                ->schema([
                    Select::make('user_id')
                        ->label('Akun sistem')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->unique(ignoreRecord: true)
                        ->placeholder('Belum punya akun'),
                    Toggle::make('is_active')
                        ->label('Karyawan aktif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label('Nama')
                    ->description(fn (Employee $record): ?string => $record->position)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->placeholder('Belum diisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (?string $state): string => Employee::EMPLOYMENT_STATUSES[$state] ?? '-'),
                TextColumn::make('user.email')
                    ->label('Akun')
                    ->placeholder('Belum punya akun')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('full_name')
            ->filters([
                SelectFilter::make('department_id')
                    ->label('Departemen')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('employment_status')
                    ->label('Status kepegawaian')
                    ->options(Employee::EMPLOYMENT_STATUSES),
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
            ->emptyStateHeading('Belum ada karyawan')
            ->emptyStateDescription('Tambahkan karyawan pertama. Data ini dipakai untuk penanggung jawab aset dan pengaju permintaan di tahap berikutnya.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
