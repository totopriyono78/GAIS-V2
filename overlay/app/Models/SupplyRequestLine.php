<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu jenis barang di dalam satu permintaan.
 *
 * Dua kolom jumlah, dan keduanya punya pemilik yang berbeda. quantity_requested ditulis
 * pemohon dan berhenti bisa diubah begitu permintaan diajukan. quantity_issued ditulis tim
 * GA saat barangnya keluar dari lemari, dan sebelum disetel nilainya null, bukan nol.
 *
 * Perbedaan antara null dan nol di sini bukan kerewelan. Null berarti tim GA belum menyentuh
 * baris ini, jadi jumlah serahnya masih mengikuti yang diminta. Nol berarti tim GA sudah
 * memutuskan barang ini tidak diserahkan sama sekali. Menyamakan keduanya membuat permintaan
 * yang belum diproses terlihat seperti permintaan yang ditolak barangnya.
 */
class SupplyRequestLine extends Model
{
    use Auditable;

    protected $fillable = [
        'supply_request_id',
        'supply_item_id',
        'quantity_requested',
        'quantity_issued',
        'notes',
        'supply_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity_requested' => 'integer',
            'quantity_issued' => 'integer',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class, 'supply_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SupplyItem::class, 'supply_item_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SupplyTransaction::class, 'supply_transaction_id');
    }

    /**
     * Jumlah yang akan diserahkan kalau tombol serah ditekan sekarang: yang sudah disetel
     * tim GA, atau yang diminta pemohon kalau tim GA belum menyentuhnya.
     */
    public function jumlahUntukDiserahkan(): int
    {
        return $this->quantity_issued ?? $this->quantity_requested;
    }

    public function dimintaLabel(): string
    {
        return $this->item?->formatQuantity($this->quantity_requested)
            ?? number_format($this->quantity_requested, 0, ',', '.');
    }

    public function diserahkanLabel(): string
    {
        if ($this->quantity_issued === null) {
            return $this->request?->status === 'diserahkan'
                ? 'Tidak tercatat'
                : 'Belum diserahkan';
        }

        return $this->item?->formatQuantity($this->quantity_issued)
            ?? number_format($this->quantity_issued, 0, ',', '.');
    }

    /**
     * Stok barang ini sekarang, dengan keterangan cukup atau tidak untuk jumlah yang akan
     * diserahkan. Dibaca tim GA sambil memutuskan berapa yang bisa keluar hari ini.
     */
    public function stokLabel(): string
    {
        $item = $this->item;

        if ($item === null) {
            return 'Barangnya sudah dihapus';
        }

        $tersedia = $item->currentStock();
        $keterangan = $item->formatQuantity(max($tersedia, 0));

        if ($this->request?->status === 'diserahkan') {
            return 'Sisa '.$keterangan;
        }

        return $tersedia >= $this->jumlahUntukDiserahkan()
            ? 'Tersedia '.$keterangan
            : 'Kurang, tersedia '.$keterangan;
    }

    public function stokCukup(): bool
    {
        $item = $this->item;

        return $item !== null && $item->currentStock() >= $this->jumlahUntukDiserahkan();
    }

    public function getAuditLabel(): string
    {
        return ($this->request?->code ?? 'permintaan').' '.($this->item?->name ?? 'barang');
    }
}
