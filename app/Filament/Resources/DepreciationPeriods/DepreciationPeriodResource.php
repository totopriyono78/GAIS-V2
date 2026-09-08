<?php

namespace App\Filament\Resources\DepreciationPeriods;

use App\Filament\Resources\DepreciationPeriods\Pages\ListDepreciationPeriods;
use App\Filament\Resources\DepreciationPeriods\Pages\ViewDepreciationPeriod;
use App\Models\DepreciationPeriod;
use App\Services\PenyusutanAset;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Periode;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Penutupan penyusutan bulanan.
 *
 * Satu baris di sini berarti satu bulan yang sudah selesai dihitung dan angkanya sudah
 * dibekukan. Tidak ada tombol tambah, karena periode tidak dibuat tangan: ia lahir dari
 * tindakan menutup bulan yang urutannya sudah ditentukan sistem.
 */
class DepreciationPeriodResource extends Resource
{
    use AuthorizesModule;

    protected static ?string $model = DepreciationPeriod::class;

    protected static string $moduleCode = 'depreciation_periods';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $navigationLabel = 'Depreciation';

    protected static ?string $modelLabel = 'depreciation period';

    protected static ?string $pluralModelLabel = 'depreciation periods';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'period';

    // Periode tidak pernah dibuat atau diubah tangan. Yang ada hanya menutup dan
    // membuka kembali, dan keduanya punya izinnya sendiri.
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

    public static function canClose(): bool
    {
        return static::allows('close');
    }

    public static function canReopen(): bool
    {
        return static::allows('reopen');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Period Closing')
                ->columns(3)
                ->schema([
                    TextEntry::make('period')
                        ->label('Periode')
                        ->state(fn (DepreciationPeriod $record): string => $record->label()),
                    TextEntry::make('closed_at')
                        ->label('Ditutup pada')
                        ->dateTime('d M Y H:i'),
                    TextEntry::make('closedByUser.name')
                        ->label('Ditutup oleh')
                        ->placeholder('Tidak tercatat'),
                    TextEntry::make('total_expense')
                        ->label('Total beban penyusutan')
                        ->state(fn (DepreciationPeriod $record): string => Rupiah::penuh((float) $record->total_expense)),
                    TextEntry::make('asset_count')
                        ->label('Jumlah aset')
                        ->state(fn (DepreciationPeriod $record): string => $record->asset_count.' aset'),
                    TextEntry::make('catch_up_count')
                        ->label('Termasuk beban susulan')
                        ->state(fn (DepreciationPeriod $record): string => $record->catch_up_count > 0
                            ? $record->catch_up_count.' aset mencakup lebih dari satu bulan'
                            : 'Tidak ada, semua mencakup satu bulan'),
                    TextEntry::make('notes')
                        ->label('Catatan')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period')
                    ->label('Periode')
                    ->state(fn (DepreciationPeriod $record): string => $record->label())
                    ->description(fn (DepreciationPeriod $record): string => $record->period)
                    ->sortable(),
                TextColumn::make('total_expense')
                    ->label('Total beban')
                    ->state(fn (DepreciationPeriod $record): string => Rupiah::penuh((float) $record->total_expense))
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('asset_count')
                    ->label('Aset')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('catch_up_count')
                    ->label('Beban susulan')
                    ->state(fn (DepreciationPeriod $record): string => $record->catch_up_count > 0
                        ? $record->catch_up_count.' aset'
                        : 'Tidak ada')
                    ->color(fn (DepreciationPeriod $record): string => $record->catch_up_count > 0 ? 'warning' : 'gray')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('closed_at')
                    ->label('Ditutup')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('closedByUser.name')
                    ->label('Oleh')
                    ->placeholder('Tidak tercatat')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('period', 'desc')
            ->recordActions([
                ViewAction::make()->iconButton(),
                static::bukaKembaliAction(),
            ])
            ->emptyStateHeading('Belum ada periode yang ditutup')
            ->emptyStateDescription('Penyusutan dihitung sekali sebulan lewat tombol Tutup periode di kanan atas. Sebelum menutup yang pertama, periksa dulu kebijakan penyusutan di menu Pengaturan.');
    }

    /**
     * Membuka kembali periode terakhir.
     *
     * Sengaja tidak memakai DeleteAction bawaan, karena yang terjadi di sini bukan
     * menghapus data melainkan membatalkan keputusan menutup buku, dan kalimat
     * konfirmasinya harus mengatakan itu.
     */
    /**
     * @param  ?string  $setelahnya  Alamat yang dituju setelah berhasil. Wajib diisi kalau
     *                               tombol ini dipasang di halaman periode itu sendiri,
     *                               karena barisnya ikut terhapus dan halamannya berubah
     *                               menjadi 404 begitu orang menyegarkan atau menekan apa pun.
     */
    public static function bukaKembaliAction(bool $iconOnly = true, ?string $setelahnya = null): Action
    {
        $aksi = Action::make('buka_kembali')
            ->label($iconOnly ? 'Buka kembali' : 'Buka kembali periode')
            ->icon('heroicon-o-lock-open')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn (DepreciationPeriod $record): string => 'Reopen '.$record->label())
            ->modalDescription(fn (DepreciationPeriod $record): string => 'Seluruh beban penyusutan periode ini dihapus, dan '.$record->label().' kembali menjadi periode yang belum dihitung. Akumulasi penyusutan tiap aset ikut mundur ke keadaan sebelum periode ini ditutup. Penghapusannya tercatat di jejak audit.')
            ->modalSubmitActionLabel('Reopen')
            ->visible(fn (DepreciationPeriod $record): bool => static::canReopen() && $record->canBeReopened())
            ->action(function (DepreciationPeriod $record, $livewire) use ($setelahnya): void {
                $label = $record->label();

                if (! app(PenyusutanAset::class)->bukaKembali($record)) {
                    Notification::make()
                        ->danger()
                        ->title('Periode ini tidak bisa dibuka kembali')
                        ->body('Sudah ada periode sesudahnya yang ditutup. Buka periode terbaru lebih dulu.')
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title($label.' dibuka kembali')
                    ->body('Angkanya sudah dihapus. Periode ini bisa ditutup ulang setelah datanya diperbaiki.')
                    ->send();

                if ($setelahnya !== null) {
                    $livewire->redirect($setelahnya);
                }
            });

        return $iconOnly ? $aksi->iconButton() : $aksi;
    }

    /**
     * Menutup periode berikutnya. Periodenya tidak dipilih orang, melainkan ditentukan
     * urutannya: bulan sesudah penutupan terakhir. Yang bisa diisi hanya catatannya.
     */
    public static function tutupAction(): Action
    {
        return Action::make('tutup')
            ->label(function (): string {
                $periode = app(PenyusutanAset::class)->periodeBerikutnya();

                return $periode === null ? 'Semua periode sudah ditutup' : 'Tutup '.Periode::label($periode);
            })
            ->icon('heroicon-o-lock-closed')
            ->color('primary')
            ->visible(fn (): bool => static::canClose())
            ->disabled(fn (): bool => app(PenyusutanAset::class)->periodeBerikutnya() === null)
            ->modalHeading(function (): string {
                $periode = app(PenyusutanAset::class)->periodeBerikutnya();

                return 'Close Depreciation '.Periode::label($periode);
            })
            ->modalDescription('Angka di bawah dihitung ulang saat kotak ini dibuka. Setelah ditutup, angkanya dibekukan dan tidak ikut berubah kalau data asetnya nanti diperbaiki.')
            ->modalSubmitActionLabel('Close Period')
            ->schema([
                Placeholder::make('ringkasan')
                    ->label('Yang akan dicatat')
                    ->content(function (): string {
                        $mesin = app(PenyusutanAset::class);
                        $periode = $mesin->periodeBerikutnya();

                        if ($periode === null) {
                            return 'Tidak ada periode yang bisa ditutup.';
                        }

                        $ringkas = $mesin->ringkasanPratinjau($periode);

                        if ($ringkas['jumlah_aset'] === 0) {
                            return 'Tidak ada aset yang disusutkan pada periode ini. Periode tetap bisa ditutup, dan hasilnya adalah periode kosong.';
                        }

                        $teks = $ringkas['jumlah_aset'].' aset, total beban '.Rupiah::penuh($ringkas['total']).'.';

                        if ($ringkas['jumlah_susulan'] > 0) {
                            $teks .= ' '.$ringkas['jumlah_susulan'].' di antaranya beban susulan yang mencakup lebih dari satu bulan, jadi totalnya lebih besar daripada bulan biasa.';
                        }

                        return $teks;
                    }),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Contoh: penutupan pertama, mencakup akumulasi sejak Januari.'),
            ])
            ->action(function (array $data): void {
                $mesin = app(PenyusutanAset::class);
                $periode = $mesin->periodeBerikutnya();

                if ($periode === null) {
                    Notification::make()
                        ->warning()
                        ->title('Tidak ada periode yang bisa ditutup')
                        ->body('Seluruh bulan sampai bulan berjalan sudah tertutup.')
                        ->send();

                    return;
                }

                $tutup = $mesin->tutup($periode, $data['notes'] ?? null);

                Notification::make()
                    ->success()
                    ->title($tutup->label().' ditutup')
                    ->body($tutup->asset_count.' aset, total beban '.Rupiah::penuh((float) $tutup->total_expense).'.')
                    ->send();
            });
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepreciationPeriods::route('/'),
            'view' => ViewDepreciationPeriod::route('/{record}'),
        ];
    }
}
