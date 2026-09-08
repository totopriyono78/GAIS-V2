<?php

namespace App\Filament\Resources\SupplyOpnames\RelationManagers;

use App\Filament\Resources\SupplyTransactions\SupplyTransactionResource;
use App\Models\SupplyOpname;
use App\Models\SupplyOpnameLine;
use App\Support\Concerns\DetectsTableFilters;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Lembar hitungan: satu baris per barang yang dihitung.
 *
 * Barisnya tidak bisa ditambah maupun dihapus dari sini, dan itu disengaja. Daftar ini lahir
 * dari cakupan sesi lewat tombol Build Target List, dan membiarkan orang menambah barang di
 * tengah penghitungan berarti membiarkan cakupan yang tertulis di kepala sesi berbeda dari
 * apa yang benar benar dihitung. Yang bisa dilakukan di sini hanya satu: mencatat berapa yang
 * benar benar ada di rak.
 *
 * Tiga kolom angka sengaja ditampilkan berdampingan. Catatan adalah stok menurut buku saat
 * ini, Hitungan fisik adalah apa yang dilihat orang, dan Selisih adalah beda keduanya. Selisih
 * ditampilkan sendiri, bukan diserahkan ke kepala pembacanya, karena pengurangan di kepala
 * sambil berdiri di depan rak adalah tempat kesalahan opname bermula.
 */
class LinesRelationManager extends RelationManager
{
    use DetectsTableFilters;

    protected static string $relationship = 'lines';

    protected static ?string $title = 'Count Sheet';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_name')
                    ->label('Barang')
                    ->description(fn (SupplyOpnameLine $record): string => $record->item_code)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('catatan')
                    ->label('Menurut catatan')
                    ->state(fn (SupplyOpnameLine $record): string => $record->catatanLabel())
                    ->description(fn (SupplyOpnameLine $record): ?string => $record->pergerakanLabel())
                    ->color(fn (SupplyOpnameLine $record): ?string => $record->stokBergerak() ? 'warning' : null)
                    ->alignEnd(),
                TextColumn::make('counted_quantity')
                    ->label('Hitungan fisik')
                    ->state(fn (SupplyOpnameLine $record): string => $record->hitunganLabel())
                    ->alignEnd(),
                TextColumn::make('selisih')
                    ->label('Selisih')
                    ->badge()
                    ->state(fn (SupplyOpnameLine $record): string => $record->selisihLabel())
                    ->color(fn (SupplyOpnameLine $record): ?string => $record->selisihColor())
                    ->alignEnd(),
                TextColumn::make('notes')
                    ->label('Keterangan')
                    ->placeholder('Tidak ada')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('transaction.code')
                    ->label('Mutasi koreksi')
                    ->fontFamily('mono')
                    ->placeholder('Belum ada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('item_name')
            ->modifyQueryUsing(fn ($query) => $query->with(['item', 'transaction', 'opname']))
            ->persistFiltersInSession()
            ->filters([
                Filter::make('belum_dihitung')
                    ->label('Belum dihitung')
                    ->query(fn (Builder $query): Builder => $query->where('checked', false)),
                Filter::make('selisih')
                    ->label('Hanya yang selisih')
                    /*
                     * Dibandingkan dengan stok sekarang, bukan dengan angka beku saat daftar
                     * disusun, supaya penyaring ini menunjukkan hal yang sama persis dengan
                     * kolom Selisih di sebelahnya. Stok memang tidak pernah menjadi kolom,
                     * tetapi ia bisa dijumlahkan lewat subkueri, pola yang sama dengan
                     * penyaring barang perlu dipesan di daftar barang sejak kiriman C.
                     */
                    ->query(fn (Builder $query): Builder => $query->where('checked', true)
                        ->whereRaw(
                            'coalesce(counted_quantity, 0) <> (select coalesce(sum(quantity), 0)'
                            .' from supply_transactions'
                            .' where supply_transactions.supply_item_id = supply_opname_lines.supply_item_id)'
                        )),
            ])
            ->recordActions([
                Action::make('catat')
                    ->label('Record Count')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->color('primary')
                    ->visible(fn (): bool => $this->bisaDicatat())
                    ->modalHeading(fn (SupplyOpnameLine $record): string => 'Count '.$record->item_name)
                    ->modalDescription(fn (SupplyOpnameLine $record): string => 'Menurut catatan ada '
                        .$record->catatanLabel().'. Isi berapa yang benar benar ada di rak.'
                        .($record->stokBergerak()
                            ? ' Perhatikan, '.lcfirst($record->pergerakanLabel() ?? '').', jadi pastikan Anda menghitungnya setelah itu.'
                            : ''))
                    ->modalSubmitActionLabel('Save Count')
                    ->fillForm(fn (SupplyOpnameLine $record): array => [
                        'counted_quantity' => $record->counted_quantity,
                        'notes' => $record->notes,
                    ])
                    ->schema([
                        TextInput::make('counted_quantity')
                            ->label('Hitungan fisik')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->required()
                            ->helperText('Dalam satuan barangnya sendiri. Isi nol kalau raknya memang benar benar kosong.'),
                        TextInput::make('notes')
                            ->label('Keterangan')
                            ->maxLength(200)
                            ->placeholder('Boleh dikosongkan')
                            ->helperText('Contoh: dua box rusak kena air, atau ditemukan di lemari lain.'),
                    ])
                    ->action(function (SupplyOpnameLine $record, array $data): void {
                        $record->forceFill([
                            'counted_quantity' => (int) $data['counted_quantity'],
                            'notes' => $data['notes'] ?? null,
                            'checked' => true,
                        ])->save();

                        $this->dispatch('rincian-berubah');

                        Notification::make()
                            ->success()
                            ->title($record->item_name.' tercatat')
                            ->body($record->hitunganLabel().' di rak. '.$record->selisihLabel().'.')
                            ->send();
                    }),
                Action::make('batal_catat')
                    ->label('Clear Count')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->iconButton()
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (SupplyOpnameLine $record): bool => $this->bisaDicatat() && $record->checked)
                    ->modalHeading(fn (SupplyOpnameLine $record): string => 'Clear Count for '.$record->item_name)
                    ->modalDescription('Barisnya kembali berstatus belum dihitung, dan stoknya tidak akan disentuh saat penyesuaian diterapkan.')
                    ->modalSubmitActionLabel('Clear Count')
                    ->action(function (SupplyOpnameLine $record): void {
                        $record->forceFill([
                            'counted_quantity' => null,
                            'checked' => false,
                        ])->save();

                        $this->dispatch('rincian-berubah');

                        Notification::make()->success()->title($record->item_name.' kembali belum dihitung')->send();
                    }),
                Action::make('buka_mutasi')
                    ->label('Open Correction Movement')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconButton()
                    ->visible(fn (SupplyOpnameLine $record): bool => $record->supply_transaction_id !== null)
                    /*
                     * Alamatnya diminta ke Resource yang bersangkutan, bukan ditulis sebagai
                     * nama rute, supaya tautannya tidak putus diam diam kalau slug modul
                     * mutasi kelak berubah.
                     *
                     * Kuncinya `search`, bukan `tableSearch`. Nama propertinya di Livewire
                     * memang tableSearch, tetapi Filament menuliskannya ke alamat dengan nama
                     * pendek `search`, dan hanya nama pendek itu yang dibaca kembali saat
                     * halaman dibuka. Ditulis `tableSearch`, tombol ini tetap membuka daftar
                     * mutasi, hanya saja tanpa tersaring sama sekali, jadi orang yang menekannya
                     * mendarat di seluruh isi buku stok. Ketahuan saat pemeriksaan kiriman O.
                     */
                    ->url(fn (SupplyOpnameLine $record): ?string => filled($record->transaction?->code)
                        ? SupplyTransactionResource::getUrl('index', [
                            'search' => $record->transaction->code,
                        ])
                        : null)
                    ->openUrlInNewTab(),
            ])
            /*
             * Penyaring disimpan per sesi peramban, jadi penyaring yang dinyalakan pada satu
             * sesi masih menyala saat sesi lain dibuka. Tanpa pembedaan ini, lembar yang
             * sebenarnya berisi akan berbunyi kosong lalu menyuruh pembacanya menyusun ulang
             * daftar, dan menyusun ulang daftar menghapus seluruh hitungan yang sudah dicatat.
             * Ditambahkan pada kiriman P, setelah cacat yang sama ketahuan di lembar
             * pemeriksaan kebersihan.
             */
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada barang yang cocok'
                : 'Daftar hitungan masih kosong')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Lembar ini ada isinya, hanya saja tidak ada yang cocok dengan penyaring yang sedang menyala. Bersihkan penyaringnya untuk melihat seluruh barang lagi.'
                : $this->pesanKosong());
    }

    protected function bisaDicatat(): bool
    {
        $opname = $this->getOwnerRecord();

        return $opname instanceof SupplyOpname && $opname->temuanBisaDicatat();
    }

    protected function pesanKosong(): string
    {
        $opname = $this->getOwnerRecord();

        if (! $opname instanceof SupplyOpname) {
            return 'Belum ada barang yang masuk daftar hitungan.';
        }

        return match (true) {
            $opname->isDraft() => 'Daftar ini lahir dari cakupan sesi, bukan diketik satu per satu. Tekan Build Target List di atas untuk menyusunnya, dan stok menurut catatan akan ikut dibekukan sebagai pembanding.',
            $opname->status === 'dibatalkan' => 'Sesi ini dibatalkan sebelum daftarnya sempat disusun.',
            default => 'Sesi ini berjalan tanpa satu pun barang di daftarnya. Itu tidak seharusnya terjadi, dan sesi ini sebaiknya dibatalkan lalu dibuat ulang.',
        };
    }
}
