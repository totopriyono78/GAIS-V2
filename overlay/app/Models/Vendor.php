<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Rekanan yang mengerjakan pemeliharaan atau memasok barang.
 */
class Vendor extends Model
{
    use Auditable;

    public const TYPES = [
        'jasa' => 'Jasa',
        'barang' => 'Barang',
        'keduanya' => 'Jasa dan barang',
    ];

    protected $fillable = [
        'code',
        'name',
        'type',
        'specialization',
        'contact_person',
        'phone',
        'email',
        'address',
        'tax_number',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(VendorBill::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /** Label untuk daftar pilihan: kode lebih dulu, seperti lokasi dan departemen. */
    public function pickerLabel(): string
    {
        return $this->code.' '.$this->name;
    }

    /**
     * Vendor jasa dan vendor yang mengerjakan keduanya. Vendor barang tidak muncul di
     * pemilih perintah kerja, karena mereka memang tidak mengerjakan pemeliharaan.
     */
    public function scopeDoingServices(Builder $query): Builder
    {
        return $query->whereIn('type', ['jasa', 'keduanya']);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
