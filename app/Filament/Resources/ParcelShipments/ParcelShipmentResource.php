<?php

namespace App\Filament\Resources\ParcelShipments;

use App\Filament\Resources\ParcelShipments\Pages\ListParcelShipments;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ParcelShipment;
use App\Models\Vendor;
use App\Support\Concerns\AuthorizesModule;
use App\Support\Concerns\DetectsTableFilters;
use App\Support\Rupiah;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * Permintaan pengiriman paket dan realisasinya.
 *
 * Dua langkah, dan keduanya dikerjakan orang yang berbeda pada waktu yang berbeda. Yang
 * meminta menyebutkan tujuan, isi, dan perkiraan biayanya. Yang mengirim, biasanya staf GA
 * yang mengantar ke gerai ekspedisi, mencatat kurir, nomor resi, dan biaya sebenarnya dari
 * kertas resi yang ia bawa pulang.
 */
class ParcelShipmentResource extends Resource
{
    use AuthorizesModule;
    use DetectsTableFilters;

    protected static ?string $model = ParcelShipment::class;

    protected static string $moduleCode = 'parcel_shipments';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Correspondence';

    protected static ?string $navigationLabel = 'Parcel Shipments';

    protected static ?string $modelLabel = 'parcel shipment';

    protected static ?string $pluralModelLabel = 'parcel shipments';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->description('Yang diisi di sini adalah permintaannya. Kurir, nomor resi, dan biaya sebenarnya dicatat belakangan lewat Record Shipment, setelah paketnya benar benar berangkat.')
                ->columns(2)
                ->schema([
                    DatePicker::make('request_date')
                        ->label('Tanggal permintaan')
                        ->native(false)
                        ->displayFormat('d M Y')
                        ->default(now())
                        ->required(),

                    Select::make('requester_employee_id')
                        ->label('Yang meminta')
                        ->options(fn (): array => Employee::query()
                            ->where('is_active', true)
                            ->orderBy('full_name')
                            ->get()
                            ->mapWithKeys(fn (Employee $karyawan) => [
                                $karyawan->id => $karyawan->full_name.' ('.$karyawan->nip.')',
                            ])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->placeholder('Tidak dicatat'),

                    Select::make('department_id')
                        ->label('Departemen yang dibebani')
                        ->options(fn (): array => Department::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Wajib diisi, karena biaya kiriman ini nanti memotong pagu anggaran departemen tersebut.'),

                    TextInput::make('estimated_cost')
                        ->label('Perkiraan biaya')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->placeholder('Boleh dikosongkan')
                        ->helperText('Dipakai membandingkan dengan biaya sebenarnya nanti. Boleh dikosongkan kalau memang belum tahu.'),

                    TextInput::make('recipient_name')
                        ->label('Nama penerima')
                        ->required()
                        ->maxLength(200),

                    TextInput::make('recipient_phone')
                        ->label('Telepon penerima')
                        ->tel()
                        ->maxLength(30)
                        ->placeholder('Boleh dikosongkan')
                        ->helperText('Hampir semua ekspedisi menolak kiriman tanpa nomor yang bisa dihubungi.'),

                    Textarea::make('recipient_address')
                        ->label('Alamat tujuan')
                        ->rows(3)
                        ->required()
                        ->maxLength(1000)
                        ->columnSpanFull(),

                    TextInput::make('contents')
                        ->label('Isi paket')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Misalnya dokumen kontrak, atau contoh produk'),

                    TextInput::make('weight_kg')
                        ->label('Berat')
                        ->numeric()
                        ->minValue(0)
                        ->suffix('kg')
                        ->placeholder('Boleh dikosongkan'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('Misalnya harus sampai sebelum tanggal tertentu')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Nomor')
                    ->fontFamily('mono')
                    ->description(fn (ParcelShipment $record): ?string => $record->tracking_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recipient_name')
                    ->label('Tujuan')
                    ->description(fn (ParcelShipment $record): string => $record->contents)
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('department.name')
                    ->label('Dibebani')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Keadaan')
                    ->badge()
                    ->state(fn (ParcelShipment $record): string => $record->statusLabel())
                    ->color(fn (ParcelShipment $record): string => $record->statusColor())
                    ->description(fn (ParcelShipment $record): ?string => $record->isDikirim()
                        ? $record->kurirLabel()
                        : null)
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Biaya')
                    ->state(fn (ParcelShipment $record): string => $record->totalLabel())
                    ->description(fn (ParcelShipment $record): ?string => $record->selisihLabel())
                    ->color(fn (ParcelShipment $record): ?string => $record->selisihColor())
                    ->alignEnd(),
                TextColumn::make('request_date')
                    ->label('Diminta')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('shipped_date')
                    ->label('Dikirim')
                    ->date('d M Y')
                    ->placeholder('Belum')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('requester.full_name')
                    ->label('Yang meminta')
                    ->placeholder('Tidak dicatat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('berat')
                    ->label('Berat')
                    ->state(fn (ParcelShipment $record): string => $record->beratLabel())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('perkiraan')
                    ->label('Perkiraan biaya')
                    ->state(fn (ParcelShipment $record): string => $record->perkiraanLabel())
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('request_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['department', 'vendor', 'requester']))
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('status')
                    ->label('Keadaan')
                    ->options(ParcelShipment::STATUSES),
                SelectFilter::make('department_id')
                    ->label('Departemen yang dibebani')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('menunggu')
                    ->label('Belum dikirim')
                    ->query(fn (Builder $query): Builder => $query->menunggu()),
            ])
            ->recordActions([
                static::kirimAction(),
                static::batalkanAction(),
                EditAction::make()
                    ->iconButton()
                    ->visible(fn (ParcelShipment $record): bool => ! $record->isDibatalkan()),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (ParcelShipment $record): bool => ! $record->isDikirim())
                    ->modalDescription('Pengiriman yang sudah berangkat tidak bisa dihapus, karena biayanya sudah masuk realisasi anggaran departemen.'),
            ])
            ->emptyStateHeading(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada pengiriman yang cocok'
                : 'Belum ada permintaan pengiriman')
            ->emptyStateDescription(fn ($livewire): string => static::adaPenyaringAktif($livewire)
                ? 'Tidak ada pengiriman yang memenuhi penyaring atau kata kunci yang sedang dipakai. Longgarkan penyaringnya, atau bersihkan semuanya untuk melihat seluruh pengiriman lagi.'
                : 'Catat permintaan kirim paket di sini, lalu isi kurir dan biayanya setelah paketnya berangkat. Biaya yang tercatat langsung memotong pagu anggaran departemen yang dibebani.');
    }

    /**
     * Mencatat realisasi pengiriman.
     *
     * Terpisah dari mengubah permintaan, karena keduanya dikerjakan pada waktu yang berbeda
     * dan yang satu tidak boleh menggeser yang lain. Perkiraan biaya yang tertulis di
     * permintaan tetap utuh setelah biaya sebenarnya dicatat, dan selisihnya justru yang
     * dibaca departemen.
     */
    public static function kirimAction(): Action
    {
        return Action::make('kirim')
            ->label('Record Shipment')
            ->icon('heroicon-o-paper-airplane')
            ->iconButton()
            ->color('primary')
            ->visible(fn (ParcelShipment $record): bool => $record->isDiminta() && static::allows('update'))
            ->modalHeading(fn (ParcelShipment $record): string => 'Record Shipment for '.$record->code)
            ->modalDescription(fn (ParcelShipment $record): string => 'Paket ke '.$record->recipient_name
                .'. Salin angkanya dari resi, termasuk diskon dan pajaknya kalau ada. '
                .'Setelah tersimpan, biayanya masuk ke realisasi anggaran '
                .($record->department?->name ?? 'departemen yang dibebani').'.')
            ->modalSubmitActionLabel('Save Shipment')
            ->fillForm(fn (ParcelShipment $record): array => [
                'shipped_date' => now()->toDateString(),
                'shipping_cost' => $record->estimated_cost,
            ])
            ->schema([
                Select::make('vendor_id')
                    ->label('Kurir atau ekspedisi')
                    ->options(fn (): array => Vendor::query()
                        ->where('is_active', true)
                        ->whereIn('type', ['jasa', 'keduanya'])
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Diambil dari daftar rekanan, jadi tagihan bulanan dari kurir yang sama bisa dicatat lewat modul tagihan rekanan.'),
                TextInput::make('tracking_number')
                    ->label('Nomor resi')
                    ->maxLength(100)
                    ->placeholder('Boleh dikosongkan kalau resinya belum keluar'),
                DatePicker::make('shipped_date')
                    ->label('Tanggal kirim')
                    ->native(false)
                    ->displayFormat('d M Y')
                    ->required()
                    ->maxDate(now())
                    ->helperText('Tanggal ini yang menentukan biaya kiriman ini masuk ke tahun anggaran yang mana.'),
                TextInput::make('shipping_cost')
                    ->label('Ongkos kirim')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('insurance_cost')
                    ->label('Asuransi')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Kosongkan kalau tidak diasuransikan'),
                TextInput::make('packing_cost')
                    ->label('Pengemasan')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Kosongkan kalau tidak ada'),
                TextInput::make('discount_amount')
                    ->label('Diskon')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Kosongkan kalau tidak ada')
                    ->helperText('Mengurangi total sebelum pajak dihitung.'),
                TextInput::make('tax_amount')
                    ->label('Pajak')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->placeholder('Kosongkan kalau tidak ada'),
            ])
            ->action(function (ParcelShipment $record, array $data, Action $action): void {
                if (! $record->isDiminta()) {
                    Notification::make()
                        ->warning()
                        ->title('Keadaannya sudah berubah')
                        ->body('Pengiriman ini tidak lagi menunggu dikirim. Muat ulang halamannya untuk melihat keadaan terbaru.')
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                $record->forceFill([
                    'vendor_id' => $data['vendor_id'],
                    'tracking_number' => $data['tracking_number'] ?? null,
                    'shipped_date' => $data['shipped_date'],
                    'shipping_cost' => $data['shipping_cost'],
                    'insurance_cost' => $data['insurance_cost'] ?? null,
                    'packing_cost' => $data['packing_cost'] ?? null,
                    'discount_amount' => $data['discount_amount'] ?? null,
                    'tax_amount' => $data['tax_amount'] ?? null,
                    'status' => 'dikirim',
                ])->save();

                // Relasi kurirnya dimuat ulang sebelum kalimatnya disusun, sama seperti
                // pencatatan kehadiran di kiriman Q.
                $record->load(['vendor', 'department']);

                Notification::make()
                    ->success()
                    ->title($record->code.' tercatat dikirim')
                    ->body('Lewat '.$record->kurirLabel().', total '.Rupiah::penuh($record->totalBiaya())
                        .'. '.($record->selisihLabel() ?? 'Tidak ada perkiraan untuk dibandingkan.'))
                    ->send();
            });
    }

    public static function batalkanAction(): Action
    {
        return Action::make('batalkan')
            ->label('Cancel Request')
            ->icon('heroicon-o-x-circle')
            ->iconButton()
            ->color('gray')
            ->visible(fn (ParcelShipment $record): bool => $record->isDiminta() && static::allows('update'))
            ->modalHeading(fn (ParcelShipment $record): string => 'Cancel '.$record->code)
            ->modalDescription('Permintaannya tetap tersimpan sebagai catatan bahwa pernah ada rencana kirim. Tidak ada biaya yang masuk anggaran.')
            ->modalSubmitActionLabel('Cancel Request')
            ->schema([
                Textarea::make('cancel_reason')
                    ->label('Alasan dibatalkan')
                    ->rows(2)
                    ->required()
                    ->maxLength(500)
                    ->helperText('Misalnya dokumennya diantar sendiri, atau tujuannya berubah.'),
            ])
            ->action(function (ParcelShipment $record, array $data): void {
                $record->forceFill([
                    'status' => 'dibatalkan',
                    'cancel_reason' => $data['cancel_reason'],
                ])->save();

                Notification::make()->success()->title($record->code.' dibatalkan')->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParcelShipments::route('/'),
        ];
    }
}
