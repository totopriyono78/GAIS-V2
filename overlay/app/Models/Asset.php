<?php

namespace App\Models;

use App\Services\AssetCodeGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use Auditable;

    public const TYPES = [
        'bergerak' => 'Aset bergerak',
        'tetap' => 'Aset tetap',
    ];

    public const SOURCES = [
        'pembelian' => 'Pembelian',
        'proyek' => 'Dari proyek',
        'hibah' => 'Hibah',
        'sewa' => 'Sewa',
        'data_lama' => 'Data lama, sumber tidak tercatat',
    ];

    /**
     * Sewa dikeluarkan dari pilihan sumber perolehan sejak kepemilikan punya kolom
     * sendiri. Nilainya tetap ada di daftar di atas supaya aset lama yang terlanjur
     * memakainya masih punya label yang terbaca, bukan kode mentah di layar.
     */
    public const LEGACY_SOURCES = ['sewa'];

    public const OWNERSHIPS = [
        'milik' => 'Milik perusahaan',
        'sewa' => 'Sewa',
    ];

    public const ACQUISITION_CONDITIONS = [
        'baru' => 'Baru',
        'bekas' => 'Bekas',
    ];

    public const STATUSES = [
        'aktif' => 'Dipakai',
        'dipinjam' => 'Dipinjam',
        'perbaikan' => 'Sedang diperbaiki',
        'tidak_dipakai' => 'Tidak dipakai',
        'dilepas' => 'Sudah dilepas',
    ];

    public const CONDITIONS = [
        'baik' => 'Baik',
        'perlu_perbaikan' => 'Perlu perbaikan',
        'rusak' => 'Rusak',
    ];

    protected $fillable = [
        'code',
        'name',
        'asset_category_id',
        'location_id',
        'custodian_employee_id',
        'department_id',
        'asset_type',
        'brand',
        'model',
        'serial_number',
        'acquisition_date',
        'acquisition_source',
        'acquisition_condition',
        'project_name',
        'ownership_type',
        'lease_start_date',
        'lease_end_date',
        'lessor',
        'lease_contract_number',
        'acquisition_cost',
        'residual_value',
        'useful_life_months',
        'depreciation_method',
        'opening_accumulated_depreciation',
        'opening_depreciation_note',
        'status',
        'condition',
        'warranty_until',
        'has_warranty',
        'warranty_from',
        'land_certificate_number',
        'building_certificate_number',
        'notes',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'warranty_until' => 'date',
            'warranty_from' => 'date',
            'has_warranty' => 'boolean',
            'lease_start_date' => 'date',
            'lease_end_date' => 'date',
            'acquisition_cost' => 'decimal:2',
            'residual_value' => 'decimal:2',
            'useful_life_months' => 'integer',
            'opening_accumulated_depreciation' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            if (blank($asset->code)) {
                $asset->code = static::generateCode($asset);
            }
        });
    }

    /**
     * Kode dibentuk dari kode departemen, nomor akun COA kategori, tahun perolehan,
     * dan nomor urut, contohnya FIN-1201-2026-0001. Tahun diambil dari tanggal
     * perolehan supaya aset lama yang diimpor tetap memakai tahunnya sendiri,
     * bukan tahun saat diimpor.
     */
    public static function generateCode(Asset $asset): string
    {
        return AssetCodeGenerator::next($asset);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'custodian_employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class);
    }

    public function opnameLines(): HasMany
    {
        return $this->hasMany(StockOpnameLine::class);
    }

    /** Perintah kerja pemeliharaan aset ini, terbaru lebih dulu. */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class)->orderByDesc('reported_date');
    }

    /** Jadwal pemeliharaan preventif yang melekat pada aset ini. */
    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    /**
     * Total biaya pemeliharaan yang sudah benar benar keluar. Pekerjaan yang dibatalkan
     * atau belum selesai tidak ikut, karena uangnya belum tentu keluar.
     */
    public function biayaPemeliharaan(): float
    {
        return \App\Services\BiayaPemeliharaan::untukAset($this);
    }

    /**
     * Beban penyusutan aset ini bulan per bulan, terbaru lebih dulu. Hanya berisi
     * periode yang sudah ditutup, karena hanya itu yang punya angka resmi.
     */
    public function depreciationEntries(): HasMany
    {
        return $this->hasMany(DepreciationEntry::class)->orderByDesc('period');
    }

    public function disposal(): HasOne
    {
        return $this->hasOne(AssetDisposal::class);
    }

    /**
     * Keterangan kendaraan, kalau aset ini memang kendaraan.
     *
     * Kosong untuk hampir semua aset, dan itu wajar. Kendaraan adalah aset dengan
     * keterangan tambahan, bukan jenis aset yang berdiri sendiri.
     */
    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function isLeased(): bool
    {
        return $this->ownership_type === 'sewa';
    }

    public function ownershipLabel(): string
    {
        return self::OWNERSHIPS[$this->ownership_type] ?? $this->ownership_type;
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->acquisition_source] ?? (string) $this->acquisition_source;
    }

    /**
     * Sisa hari sampai garansi habis. Negatif berarti sudah lewat, null berarti
     * memang tidak bergaransi atau tanggalnya belum diisi.
     */
    public function warrantyDaysLeft(): ?int
    {
        if (! $this->has_warranty || $this->warranty_until === null) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->warranty_until->startOfDay(), false);
    }

    public function leaseDaysLeft(): ?int
    {
        if (! $this->isLeased() || $this->lease_end_date === null) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->lease_end_date->startOfDay(), false);
    }

    public function isDisposed(): bool
    {
        return $this->status === 'dilepas';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function conditionLabel(): string
    {
        return self::CONDITIONS[$this->condition] ?? $this->condition;
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
