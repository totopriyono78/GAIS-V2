<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu kali barang datang atas satu pembelian.
 *
 * Dokumen ini tidak punya layar sendiri di menu, dan itu disengaja. Penerimaan tidak pernah
 * dicari lepas dari pesanannya, jadi ia lahir dari tombol di halaman pembelian dan dibaca
 * sebagai riwayat di halaman yang sama. Menambahkan menu tersendiri hanya akan melahirkan
 * daftar yang tidak pernah dibuka orang.
 */
class SupplyReceipt extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'supply_receipt';

    protected $fillable = [
        'code',
        'supply_purchase_id',
        'receipt_date',
        'delivery_note_number',
        'notes',
        'received_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupplyReceipt $penerimaan) {
            if (blank($penerimaan->code)) {
                $penerimaan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(SupplyPurchase::class, 'supply_purchase_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SupplyReceiptLine::class);
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    /** Nilai barang yang datang pada penerimaan ini, memakai harga pesanannya. */
    public function total(): float
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->with('purchaseLine')->get();

        return round($baris->sum(
            fn (SupplyReceiptLine $b): float => $b->quantity * (float) ($b->purchaseLine?->unit_price ?? 0)
        ), 2);
    }

    public function totalLabel(): string
    {
        return Rupiah::penuh($this->total());
    }

    public function jenisLabel(): string
    {
        $jumlah = $this->relationLoaded('lines') ? $this->lines->count() : $this->lines()->count();

        return $jumlah.' jenis barang';
    }

    /** Isi penerimaan sebagai satu kalimat, dipakai di kolom tabel riwayat. */
    public function isiLabel(): string
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->with('purchaseLine.item')->get();

        if ($baris->isEmpty()) {
            return 'Tidak ada barang yang tercatat';
        }

        return $baris
            ->map(fn (SupplyReceiptLine $b): string => ($b->purchaseLine?->item?->name ?? 'Barang yang sudah dihapus')
                .' '.($b->purchaseLine?->formatJumlah($b->quantity) ?? $b->quantity))
            ->implode(', ');
    }

    public function getAuditLabel(): string
    {
        return $this->code.' atas '.($this->purchase?->code ?? 'pembelian');
    }
}
