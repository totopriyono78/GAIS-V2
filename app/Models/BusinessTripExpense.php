<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Satu pengeluaran dalam satu perjalanan dinas.
 */
class BusinessTripExpense extends Model
{
    use Auditable;

    public const CATEGORIES = [
        'transport' => 'Transportasi',
        'penginapan' => 'Penginapan',
        'uang_harian' => 'Uang harian',
        'konsumsi' => 'Konsumsi',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'business_trip_id',
        'expense_date',
        'category',
        'description',
        'amount',
        'file_path',
        'original_name',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(BusinessTrip::class, 'business_trip_id');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function amountLabel(): string
    {
        return Rupiah::penuh((float) $this->amount);
    }

    /**
     * Keterangan bukti. Yang tidak berbukti disebutkan apa adanya, bukan dibiarkan kosong,
     * karena baris tanpa bukti adalah baris yang paling sering ditanyakan pemeriksa.
     */
    public function buktiLabel(): string
    {
        return filled($this->file_path) ? 'Ada' : 'Tidak ada';
    }

    public function buktiUrl(): ?string
    {
        return filled($this->file_path)
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    public function getAuditLabel(): string
    {
        return ($this->trip?->code ?? 'perjalanan').' '.$this->description;
    }
}
