<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu barang yang dihitung dalam satu sesi opname.
 *
 * Ada tiga angka di baris ini dan ketiganya berbeda arti, jadi layak diingat baik baik:
 *
 * - `system_quantity`: stok menurut catatan **saat daftar disusun**, dibekukan sekali.
 * - `stokSekarang()`: stok menurut catatan **saat ini**, dijumlahkan ulang dari mutasi.
 * - `counted_quantity`: apa yang benar benar ada di rak, diketik orang di gudang.
 *
 * Selisih yang menjadi koreksi adalah hitungan fisik dikurangi stok sekarang, bukan dikurangi
 * angka beku. Angka beku dipakai untuk hal lain: kalau ia sudah tidak sama dengan stok
 * sekarang, berarti barangnya sempat bergerak setelah daftar disusun, dan lembar hitungannya
 * patut dicurigai. Keadaan itu disebutkan di layar, tidak diperbaiki diam diam.
 */
class SupplyOpnameLine extends Model
{
    use Auditable;

    protected $fillable = [
        'supply_opname_id',
        'supply_item_id',
        'item_code',
        'item_name',
        'unit',
        'system_quantity',
        'counted_quantity',
        'checked',
        'notes',
        'supply_transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'system_quantity' => 'integer',
            'counted_quantity' => 'integer',
            'checked' => 'boolean',
        ];
    }

    public function opname(): BelongsTo
    {
        return $this->belongsTo(SupplyOpname::class, 'supply_opname_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SupplyItem::class, 'supply_item_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(SupplyTransaction::class, 'supply_transaction_id');
    }

    /** Stok menurut catatan saat ini, dijumlahkan ulang dari mutasi. */
    public function stokSekarang(): int
    {
        return $this->item?->currentStock() ?? 0;
    }

    /**
     * Selisih yang akan menjadi koreksi: hitungan fisik dikurangi stok menurut catatan
     * sekarang. Positif berarti barangnya lebih banyak daripada catatan.
     */
    public function selisih(): int
    {
        if ($this->counted_quantity === null) {
            return 0;
        }

        return $this->counted_quantity - $this->stokSekarang();
    }

    /**
     * Apakah stok barang ini sempat bergerak setelah daftar opname disusun, dan pergerakan
     * itu masih perlu diwaspadai orang.
     *
     * Dua keadaan sengaja membuatnya diam meskipun angkanya memang berbeda. Sesi yang
     * penyesuaiannya sudah diterapkan selalu memenuhi syarat aritmatikanya, karena koreksi
     * yang lahir dari sesi inilah yang menggeser stoknya, jadi lembar hitungan yang baru saja
     * selesai akan mengabarkan bahwa dirinya sendiri patut dicurigai. Sesi yang dibatalkan
     * tidak akan pernah dipakai hitungannya, jadi peringatan untuk berhati hati terhadap
     * hitungan itu tidak punya siapa siapa untuk diperingatkan. Syaratnya sendiri ada di
     * SupplyOpname::pergerakanPerluDiwaspadai().
     */
    public function stokBergerak(): bool
    {
        if (! $this->opname?->pergerakanPerluDiwaspadai()) {
            return false;
        }

        return $this->stokSekarang() !== $this->system_quantity;
    }

    public function formatJumlah(int $jumlah): string
    {
        return $this->item?->formatQuantity($jumlah)
            ?? number_format($jumlah, 0, ',', '.').' '.$this->unit;
    }

    public function catatanLabel(): string
    {
        return $this->formatJumlah($this->stokSekarang());
    }

    public function hitunganLabel(): string
    {
        if (! $this->checked || $this->counted_quantity === null) {
            return 'Belum dihitung';
        }

        return $this->formatJumlah($this->counted_quantity);
    }

    public function selisihLabel(): string
    {
        if (! $this->checked || $this->counted_quantity === null) {
            return 'Belum dihitung';
        }

        $selisih = $this->selisih();

        return match (true) {
            $selisih === 0 => 'Cocok',
            $selisih > 0 => 'Lebih '.$this->formatJumlah($selisih),
            default => 'Kurang '.$this->formatJumlah(abs($selisih)),
        };
    }

    public function selisihColor(): ?string
    {
        if (! $this->checked || $this->counted_quantity === null) {
            return 'gray';
        }

        return match (true) {
            $this->selisih() === 0 => 'success',
            $this->selisih() > 0 => 'warning',
            default => 'danger',
        };
    }

    /**
     * Keterangan pergerakan stok selama penghitungan berjalan, atau null kalau tidak ada.
     * Ditampilkan sebagai keterangan di bawah kolom catatan.
     */
    public function pergerakanLabel(): ?string
    {
        if (! $this->stokBergerak()) {
            return null;
        }

        $selisih = $this->stokSekarang() - $this->system_quantity;

        return $selisih > 0
            ? 'Bertambah '.$this->formatJumlah($selisih).' sejak daftar disusun'
            : 'Berkurang '.$this->formatJumlah(abs($selisih)).' sejak daftar disusun';
    }

    public function getAuditLabel(): string
    {
        return ($this->opname?->code ?? 'opname').' '.$this->item_name;
    }
}
