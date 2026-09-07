<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Kendaraan dinas, yaitu keterangan tambahan di atas satu aset.
 *
 * Nama, kategori, lokasi, penanggung jawab, harga perolehan, penyusutan, riwayat
 * perbaikan, dan pelepasan semuanya tetap tinggal di aset. Yang ada di sini hanya hal
 * yang khas kendaraan: nomor polisi, nomor rangka dan mesin, bahan bakar, odometer,
 * dan cara kendaraan itu dipakai.
 */
class Vehicle extends Model
{
    use Auditable;

    public const TYPES = [
        'mobil_penumpang' => 'Mobil penumpang',
        'mobil_operasional' => 'Mobil operasional',
        'pikap' => 'Pikap',
        'truk' => 'Truk',
        'motor' => 'Sepeda motor',
        'lainnya' => 'Lainnya',
    ];

    public const USAGE_MODES = [
        'pool' => 'Pool, dipesan bergantian',
        'pegangan' => 'Pegangan, melekat pada satu orang',
        'operasional' => 'Operasional, untuk kirim barang dan keperluan lapangan',
    ];

    public const FUEL_TYPES = [
        'bensin' => 'Bensin',
        'solar' => 'Solar',
        'listrik' => 'Listrik',
        'hybrid' => 'Hibrida',
    ];

    public const TRANSMISSIONS = [
        'manual' => 'Manual',
        'matik' => 'Matik',
    ];

    protected $fillable = [
        'asset_id',
        'plate_number',
        'vehicle_type',
        'usage_mode',
        'chassis_number',
        'engine_number',
        'color',
        'production_year',
        'fuel_type',
        'transmission',
        'seat_capacity',
        'payload_kg',
        'default_driver_employee_id',
        'last_odometer_km',
        'last_odometer_date',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'production_year' => 'integer',
            'seat_capacity' => 'integer',
            'payload_kg' => 'integer',
            'last_odometer_km' => 'integer',
            'last_odometer_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Nomor polisi ditulis orang dengan spasi yang berubah ubah: "B 1234 XYZ",
        // "B1234XYZ", "b 1234 xyz". Dirapikan sekali di sini supaya pencarian dan
        // kunci unik bekerja pada bentuk yang sama, bukan pada kebiasaan pengetik.
        static::saving(function (Vehicle $kendaraan) {
            if (filled($kendaraan->plate_number)) {
                $kendaraan->plate_number = self::rapikanNomorPolisi($kendaraan->plate_number);
            }
        });
    }

    public static function rapikanNomorPolisi(string $nomor): string
    {
        $bersih = preg_replace('/[^A-Za-z0-9]/', '', $nomor) ?? $nomor;
        $bersih = strtoupper($bersih);

        // Bentuk baku pelat Indonesia: huruf wilayah, angka, huruf seri.
        if (preg_match('/^([A-Z]{1,2})(\d{1,4})([A-Z]{0,3})$/', $bersih, $bagian)) {
            return trim($bagian[1].' '.$bagian[2].' '.$bagian[3]);
        }

        // Nomor yang tidak mengikuti pola itu dibiarkan apa adanya, hanya dibesarkan
        // hurufnya. Menolaknya berarti menolak pelat dinas dan pelat khusus yang memang ada.
        return strtoupper(trim($nomor));
    }

    // ---------------------------------------------------------------- relasi

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function defaultDriver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'default_driver_employee_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class)->orderByDesc('expires_at');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(VehicleBooking::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(VehicleTrip::class);
    }

    public function refuelings(): HasMany
    {
        return $this->hasMany(VehicleRefueling::class);
    }

    // ---------------------------------------------------------------- dokumen

    /**
     * Dokumen yang berlaku sekarang untuk satu jenis, yaitu yang tanggal berakhirnya
     * paling jauh. Dihitung saat dibaca, bukan disimpan sebagai penanda, supaya tidak
     * ada baris yang diam diam tertinggal menyandang status yang salah.
     */
    public function dokumenBerlaku(string $jenis): ?VehicleDocument
    {
        return $this->documents
            ->where('type', $jenis)
            ->sortByDesc('expires_at')
            ->first();
    }

    /**
     * Dokumen berlaku untuk setiap jenis yang pernah dicatat pada kendaraan ini.
     *
     * @return array<string, VehicleDocument>
     */
    public function dokumenBerlakuSemua(): array
    {
        return $this->documents
            ->sortByDesc('expires_at')
            ->unique('type')
            ->keyBy('type')
            ->all();
    }

    /**
     * Tanggal jatuh tempo terdekat di antara seluruh dokumen yang berlaku.
     *
     * Yang dipakai hanya dokumen berlaku per jenis, bukan seluruh baris. Tanpa itu,
     * pajak tahun lalu yang sudah diperpanjang akan selamanya muncul sebagai terlambat.
     */
    public function jatuhTempoTerdekat(): ?VehicleDocument
    {
        $berlaku = collect($this->dokumenBerlakuSemua());

        return $berlaku->sortBy('expires_at')->first();
    }

    public function keadaanLabel(): string
    {
        $dokumen = $this->jatuhTempoTerdekat();

        if ($dokumen === null) {
            return 'Belum ada dokumen yang dicatat';
        }

        return $dokumen->jenisLabel().' '.strtolower($dokumen->keteranganWaktu());
    }

    public function keadaanColor(): string
    {
        $dokumen = $this->jatuhTempoTerdekat();

        return $dokumen?->keadaanColor() ?? 'gray';
    }

    // ---------------------------------------------------------------- tampilan

    public function typeLabel(): string
    {
        return self::TYPES[$this->vehicle_type] ?? $this->vehicle_type;
    }

    public function usageLabel(): string
    {
        return self::USAGE_MODES[$this->usage_mode] ?? $this->usage_mode;
    }

    /** Kalimat pendek untuk kolom tabel: pemakaian tanpa penjelasannya. */
    public function usageLabelSingkat(): string
    {
        return match ($this->usage_mode) {
            'pool' => 'Pool',
            'pegangan' => 'Pegangan',
            'operasional' => 'Operasional',
            default => $this->usage_mode,
        };
    }

    public function fuelLabel(): string
    {
        return self::FUEL_TYPES[$this->fuel_type] ?? 'Tidak dicatat';
    }

    public function namaLengkap(): string
    {
        $bagian = array_filter([
            $this->asset?->brand,
            $this->asset?->model,
            $this->production_year,
        ]);

        return $bagian === [] ? ($this->asset?->name ?? 'Kendaraan') : implode(' ', $bagian);
    }

    public function odometerLabel(): string
    {
        if (blank($this->last_odometer_km)) {
            return 'Belum dicatat';
        }

        return number_format($this->last_odometer_km, 0, ',', '.').' km'
            .($this->last_odometer_date ? ' per '.$this->last_odometer_date->translatedFormat('d M Y') : '');
    }

    /**
     * Konsumsi rata rata kendaraan ini, dari seluruh penggal antar pengisian penuh yang
     * lengkap. Null kalau belum ada satu pun penggal yang bisa dihitung, dan itu keadaan
     * yang harus dikatakan apa adanya, bukan diganti angka nol.
     */
    public function konsumsiRataRata(): ?float
    {
        $penggal = $this->refuelings()
            ->penuh()
            ->orderBy('odometer_km')
            ->get()
            ->map(fn (VehicleRefueling $pengisian): ?float => $pengisian->konsumsiKmPerLiter())
            ->filter()
            ->values();

        if ($penggal->isEmpty()) {
            return null;
        }

        return round($penggal->avg(), 2);
    }

    public function konsumsiLabel(): string
    {
        $konsumsi = $this->konsumsiRataRata();

        if ($konsumsi === null) {
            return 'Belum bisa dihitung';
        }

        return number_format($konsumsi, 2, ',', '.').' km per liter';
    }

    public function pickerLabel(): string
    {
        return $this->plate_number.' '.$this->namaLengkap();
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Kendaraan yang punya dokumen berlaku jatuh tempo dalam sekian hari, termasuk yang
     * sudah lewat.
     *
     * Penyaringnya bekerja pada tanggal berakhir terjauh per jenis, bukan pada tiap
     * baris, supaya kendaraan yang pajaknya sudah diperpanjang tidak ikut terjaring
     * gara gara baris tahun lalu.
     */
    public function scopeDokumenJatuhTempo(Builder $query, int $hari = 30): Builder
    {
        $batas = Carbon::now()->addDays($hari)->endOfDay();

        return $query->whereHas('documents', function (Builder $dokumen) use ($batas): void {
            $dokumen->whereRaw(
                'vehicle_documents.expires_at = ('
                .' select max(d2.expires_at) from vehicle_documents d2'
                .' where d2.vehicle_id = vehicle_documents.vehicle_id'
                .' and d2.type = vehicle_documents.type)'
            )->where('expires_at', '<=', $batas);
        });
    }

    public function getAuditLabel(): string
    {
        return $this->plate_number.' '.$this->namaLengkap();
    }
}
