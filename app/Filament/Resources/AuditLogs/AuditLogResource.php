<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\AuditLog;
use App\Support\Concerns\AuthorizesModule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AuditLogResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = AuditLog::class;

    protected static string $moduleCode = 'audit_logs';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $modelLabel = 'audit entry';

    protected static ?string $pluralModelLabel = 'audit entries';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        // Jejak audit tidak pernah diubah dari layar, jadi tidak ada form.
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('user_name')
                    ->label('Oleh')
                    ->placeholder('Sistem')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Kejadian')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => AuditLog::EVENTS[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'deleted' => 'danger',
                        'updated' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('auditable_type')
                    ->label('Jenis data')
                    ->state(fn (AuditLog $record): string => $record->auditable_type
                        ? class_basename($record->auditable_type)
                        : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('auditable_label')
                    ->label('Record')
                    ->placeholder('-')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('changed')
                    ->label('Kolom yang berubah')
                    ->state(fn (AuditLog $record): string => collect(array_keys($record->new_values ?? $record->old_values ?? []))
                        ->join(', ') ?: '-')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')
                    ->label('Alamat IP')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Kejadian')
                    ->options(AuditLog::EVENTS),
                SelectFilter::make('user_id')
                    ->label('Pengguna')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('rentang_waktu')
                    ->schema([
                        DatePicker::make('dari')->label('Dari tanggal'),
                        DatePicker::make('sampai')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['dari'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['sampai'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date))),
            ])
            ->emptyStateHeading('Belum ada jejak yang tercatat')
            ->emptyStateDescription('Setiap penambahan, perubahan, dan penghapusan data akan muncul di sini beserta pelakunya.');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
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
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
