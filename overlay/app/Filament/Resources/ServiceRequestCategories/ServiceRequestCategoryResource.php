<?php

namespace App\Filament\Resources\ServiceRequestCategories;

use App\Filament\Resources\ServiceRequestCategories\Pages\ListServiceRequestCategories;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Jenis permintaan perbaikan.
 *
 * Dua kolom di sini yang benar benar menentukan perilaku modul permintaan: prioritas
 * bawaan, yang menentukan seberapa cepat tiket baru terlihat mendesak, dan target waktu,
 * yang menentukan kapan tiket dihitung terlambat. Keduanya keputusan perusahaan, bukan
 * keputusan pemrogram, jadi keduanya dibuat bisa diubah tanpa menyentuh kode.
 */
class ServiceRequestCategoryResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = ServiceRequestCategory::class;

    protected static string $moduleCode = 'service_request_categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Request Types';

    protected static ?string $modelLabel = 'request type';

    protected static ?string $pluralModelLabel = 'request types';

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('code')
                ->label('Kode')
                ->required()
                ->maxLength(30)
                ->unique(ignoreRecord: true)
                ->placeholder('Contoh: AC')
                ->helperText('Dipakai di daftar dan penyaring. Singkat saja.'),
            TextInput::make('name')
                ->label('Nama jenis')
                ->required()
                ->maxLength(150)
                ->placeholder('Contoh: Pendingin ruangan'),
            Select::make('default_priority')
                ->label('Prioritas bawaan')
                ->options(ServiceRequest::PRIORITIES)
                ->default('normal')
                ->required()
                ->helperText('Terisi otomatis saat pemohon memilih jenis ini, dan masih bisa dinaikkan.'),
            TextInput::make('sla_hours')
                ->label('Target waktu penyelesaian')
                ->numeric()
                ->minValue(1)
                ->maxValue(2160)
                ->suffix('jam')
                ->placeholder('Belum ada target')
                ->helperText('Dihitung sejak tim GA menerima tiket, bukan sejak diajukan. Boleh dikosongkan sampai targetnya disepakati, dan selama kosong tiket jenis ini tidak pernah dihitung terlambat.'),
            Textarea::make('description')
                ->label('Keterangan')
                ->rows(2)
                ->columnSpanFull()
                ->maxLength(500)
                ->placeholder('Contoh: AC tidak dingin, bocor, atau berisik.')
                ->helperText('Muncul sebagai penjelas saat pemohon memilih jenis ini.'),
            Toggle::make('is_active')
                ->label('Masih dipakai')
                ->default(true)
                ->helperText('Jenis yang dimatikan tidak lagi muncul di formulir permintaan baru, tetapi tiket lama tetap menyebutnya.'),
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
                    ->label('Jenis')
                    ->description(fn (ServiceRequestCategory $record): ?string => $record->description)
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                TextColumn::make('default_priority')
                    ->label('Prioritas bawaan')
                    ->badge()
                    ->state(fn (ServiceRequestCategory $record): string => ServiceRequest::PRIORITIES[$record->default_priority] ?? $record->default_priority)
                    ->color(fn (ServiceRequestCategory $record): string => match ($record->default_priority) {
                        'mendesak' => 'danger',
                        'tinggi' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('sla_hours')
                    ->label('Target waktu')
                    ->state(fn (ServiceRequestCategory $record): string => $record->slaLabel())
                    ->color(fn (ServiceRequestCategory $record): ?string => blank($record->sla_hours) ? 'gray' : null)
                    ->sortable(),
                TextColumn::make('service_requests_count')
                    ->label('Permintaan')
                    ->counts('serviceRequests')
                    ->alignEnd()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Dipakai')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->persistFiltersInSession()
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status pemakaian')
                    ->placeholder('Semua')
                    ->trueLabel('Masih dipakai')
                    ->falseLabel('Sudah dimatikan'),
                Filter::make('tanpa_target')
                    ->label('Belum punya target waktu')
                    ->query(fn (Builder $query): Builder => $query->whereNull('sla_hours')),
            ])
            ->recordActions([
                EditAction::make()->iconButton(),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (ServiceRequestCategory $record): bool => static::canDelete($record)),
            ])
            ->emptyStateHeading('Belum ada jenis permintaan')
            ->emptyStateDescription('Jenis permintaan adalah pilihan yang dilihat karyawan saat melapor: listrik, AC, kebocoran, dan seterusnya. Tambahkan lewat tombol di kanan atas, lalu isi target waktunya setelah tim GA menyepakati berapa lama tiap jenis seharusnya selesai.');
    }

    /**
     * Jenis yang sudah pernah dipakai tiket tidak boleh dihapus. Menghapusnya membuat
     * tiket lama kehilangan sebutan jenisnya, dan laporan berapa banyak keluhan AC
     * tahun lalu berubah tanpa jejak. Yang sudah tidak dipakai dimatikan, bukan dihapus.
     */
    public static function canDelete(Model $record): bool
    {
        return static::allows('delete') && ! $record->serviceRequests()->exists();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequestCategories::route('/'),
        ];
    }
}
