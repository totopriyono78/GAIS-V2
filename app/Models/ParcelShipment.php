<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu paket yang dikirim keluar.
 *
 * Total biayanya tidak pernah disimpan. Ia dijumlahkan dari lima rincian setiap kali dibaca,
 * dengan rumus yang hanya ada di satu tempat, yaitu di totalBiaya(). Diskon mengurangi, pajak
 * menambah, dan urutan itu penting: pajak di Indonesia dihitung setelah diskon, bukan sebelum.
 */
class ParcelShipment extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'parcel_shipment';

    public const STATUSES = [
        'diminta' => 'Menunggu dikirim',
        'dikirim' => 'Sudah dikirim',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected $fillable = [
        'code',
        'request_date',
        'requester_employee_id',
        'department_id',
        'recipient_name',
        'recipient_address',
        'recipient_phone',
        'contents',
        'weight_kg',
        'estimated_cost',
        'status',
        'vendor_id',
        'tracking_number',
        'shipped_date',
        'shipping_cost',
        'insurance_cost',
        'packing_cost',
        'discount_amount',
        'tax_amount',
        'notes',
        'cancel_reason',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'shipped_date' => 'date',
            'weight_kg' => 'decimal:2',
            'estimated_cost' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'insurance_cost' => 'decimal:2',
            'packing_cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ParcelShipment $kiriman) {
            if (blank($kiriman->code)) {
                $kiriman->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($kiriman->created_by_user_id)) {
                $kiriman->created_by_user_id = Auth::id();
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function isDiminta(): bool
    {
        return $this->status === 'diminta';
    }

    public function isDikirim(): bool
    {
        return $this->status === 'dikirim';
    }

    public function isDibatalkan(): bool
    {
        return $this->status === 'dibatalkan';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'diminta' => 'warning',
            'dikirim' => 'success',
            default => 'gray',
        };
    }

    // ---------------------------------------------------------------- angka

    /**
     * Total biaya sebenarnya. Satu satunya tempat rumusnya ditulis.
     *
     * Diskon mengurangi lebih dulu, pajak menambah setelahnya, karena begitulah urutan yang
     * tertulis di resi ekspedisi di Indonesia. Membalik urutannya menghasilkan angka yang
     * berbeda dan tidak akan pernah cocok dengan kertas yang dipegang orang.
     */
    public function totalBiaya(): float
    {
        $sebelumPajak = (float) $this->shipping_cost
            + (float) $this->insurance_cost
            + (float) $this->packing_cost
            - (float) $this->discount_amount;

        return round($sebelumPajak + (float) $this->tax_amount, 2);
    }

    public function totalLabel(): string
    {
        return $this->adaRincianBiaya() ? Rupiah::penuh($this->totalBiaya()) : 'Belum dicatat';
    }

    public function adaRincianBiaya(): bool
    {
        return $this->shipping_cost !== null
            || $this->insurance_cost !== null
            || $this->packing_cost !== null
            || $this->tax_amount !== null;
    }

    public function perkiraanLabel(): string
    {
        return $this->estimated_cost === null
            ? 'Tidak diperkirakan'
            : Rupiah::penuh((float) $this->estimated_cost);
    }

    /**
     * Selisih biaya sebenarnya terhadap perkiraannya, dalam satu kalimat.
     *
     * Yang dibandingkan hanya pengiriman yang sudah berangkat dan memang punya perkiraan.
     * Membandingkan yang belum berangkat berarti membandingkan angka dengan nol, dan itu
     * selalu menghasilkan kabar buruk yang tidak benar.
     */
    public function selisihLabel(): ?string
    {
        if (! $this->isDikirim() || $this->estimated_cost === null || ! $this->adaRincianBiaya()) {
            return null;
        }

        $selisih = round($this->totalBiaya() - (float) $this->estimated_cost, 2);

        if (abs($selisih) < 0.01) {
            return 'Tepat sesuai perkiraan.';
        }

        return $selisih > 0
            ? 'Lebih mahal '.Rupiah::penuh($selisih).' daripada perkiraannya.'
            : 'Lebih murah '.Rupiah::penuh(abs($selisih)).' daripada perkiraannya.';
    }

    public function selisihColor(): ?string
    {
        if (! $this->isDikirim() || $this->estimated_cost === null || ! $this->adaRincianBiaya()) {
            return null;
        }

        return $this->totalBiaya() > (float) $this->estimated_cost ? 'warning' : 'success';
    }

    public function beratLabel(): string
    {
        return $this->weight_kg === null
            ? 'Tidak ditimbang'
            : rtrim(rtrim(number_format((float) $this->weight_kg, 2, ',', '.'), '0'), ',').' kg';
    }

    public function kurirLabel(): string
    {
        return $this->vendor?->name ?? 'Belum ditentukan';
    }

    /** Pengiriman yang sudah diminta tetapi paketnya belum berangkat. */
    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->where('status', 'diminta');
    }

    public function getAuditLabel(): string
    {
        return $this->code.' ke '.$this->recipient_name;
    }
}
