<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Berapa banyak satu barang datang pada satu kali penerimaan.
 *
 * Baris ini menunjuk baris pesanan, bukan barangnya langsung. Bedanya penting: harga yang
 * dipakai menilai penerimaan adalah harga yang disepakati pada pesanan itu, bukan harga
 * terakhir di kartu barang, dan bukan pula harga pesanan lain untuk barang yang sama.
 */
class SupplyReceiptLine extends Model
{
    use Auditable;

    protected $fillable = [
        'supply_receipt_id',
        'supply_purchase_line_id',
        'quantity',
        'supply_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(SupplyReceipt::class, 'supply_receipt_id');
    }

    public function purchaseLine(): BelongsTo
    {
        return $this->belongsTo(SupplyPurchaseLine::class, 'supply_purchase_line_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SupplyTransaction::class, 'supply_transaction_id');
    }

    public function subtotal(): float
    {
        return round($this->quantity * (float) ($this->purchaseLine?->unit_price ?? 0), 2);
    }

    public function getAuditLabel(): string
    {
        return ($this->receipt?->code ?? 'penerimaan').' '
            .($this->purchaseLine?->item?->name ?? 'barang');
    }
}
