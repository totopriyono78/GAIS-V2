<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu jenis barang di dalam satu pembelian.
 *
 * Jumlah yang sudah diterima tidak disimpan sebagai kolom, melainkan dijumlahkan dari baris
 * penerimaan yang menunjuk baris ini. Pola yang sama dipakai stok sejak kiriman C dan nilai
 * tagihan sejak kiriman K, dan alasannya selalu sama: angka yang disimpan bisa melenceng dari
 * riwayatnya, angka yang dijumlahkan tidak bisa.
 */
class SupplyPurchaseLine extends Model
{
    use Auditable;

    protected $fillable = [
        'supply_purchase_id',
        'supply_item_id',
        'quantity_ordered',
        'unit_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'integer',
            'unit_price' => 'decimal:2',
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(SupplyPurchase::class, 'supply_purchase_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SupplyItem::class, 'supply_item_id');
    }

    public function receiptLines(): HasMany
    {
        return $this->hasMany(SupplyReceiptLine::class, 'supply_purchase_line_id');
    }

    public function subtotal(): float
    {
        return round($this->quantity_ordered * (float) $this->unit_price, 2);
    }

    public function jumlahDiterima(): int
    {
        return (int) ($this->relationLoaded('receiptLines')
            ? $this->receiptLines->sum('quantity')
            : $this->receiptLines()->sum('quantity'));
    }

    /** Berapa yang belum datang. Tidak pernah negatif, karena kelebihan terima ditolak. */
    public function sisa(): int
    {
        return max($this->quantity_ordered - $this->jumlahDiterima(), 0);
    }

    public function formatJumlah(int $jumlah): string
    {
        return $this->item?->formatQuantity($jumlah)
            ?? number_format($jumlah, 0, ',', '.');
    }

    public function dipesanLabel(): string
    {
        return $this->formatJumlah($this->quantity_ordered);
    }

    public function diterimaLabel(): string
    {
        $diterima = $this->jumlahDiterima();

        if ($diterima === 0) {
            return 'Belum datang';
        }

        return $this->formatJumlah($diterima);
    }

    public function sisaLabel(): string
    {
        $sisa = $this->sisa();

        return $sisa === 0 ? 'Lengkap' : 'Kurang '.$this->formatJumlah($sisa);
    }

    public function hargaLabel(): string
    {
        return Rupiah::penuh((float) $this->unit_price).' per '
            .($this->item?->unit ?? 'satuan');
    }

    public function subtotalLabel(): string
    {
        return Rupiah::penuh($this->subtotal());
    }

    public function getAuditLabel(): string
    {
        return ($this->purchase?->code ?? 'pembelian').' '.($this->item?->name ?? 'barang');
    }
}
