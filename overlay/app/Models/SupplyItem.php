<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu jenis barang habis pakai: ATK, kertas, tinta, kebersihan, pantry.
 *
 * Barang habis pakai sengaja tidak disimpan di tabel aset. Aset dicatat satu
 * baris satu barang karena dilacak sampai umur ekonomisnya, sedangkan barang
 * habis pakai dicatat sebagai jumlah dan langsung dibebankan saat dipakai.
 * Menggabungkan keduanya akan membuat 500 pulpen jadi 500 baris aset.
 *
 * Stok tidak disimpan sebagai kolom. Stok adalah jumlah seluruh mutasi barang
 * ini, dan itu membuat angka stok mustahil bertentangan dengan riwayatnya.
 */
class SupplyItem extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'supply_item';

    public const CATEGORIES = [
        'alat_tulis' => 'Alat tulis',
        'kertas' => 'Kertas dan cetak',
        'tinta' => 'Tinta dan toner',
        'kebersihan' => 'Kebersihan',
        'pantry' => 'Pantry',
        'listrik' => 'Listrik dan lampu',
        'lainnya' => 'Lainnya',
    ];

    public const UNITS = [
        'pcs' => 'Pcs',
        'box' => 'Box',
        'pak' => 'Pak',
        'rim' => 'Rim',
        'lusin' => 'Lusin',
        'botol' => 'Botol',
        'kaleng' => 'Kaleng',
        'roll' => 'Roll',
        'set' => 'Set',
        'kg' => 'Kilogram',
        'liter' => 'Liter',
    ];

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'minimum_stock',
        'location_id',
        'last_price',
        'account_expense',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'minimum_stock' => 'integer',
            'last_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupplyItem $item) {
            if (blank($item->code)) {
                $item->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }
        });
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SupplyTransaction::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function unitLabel(): string
    {
        return self::UNITS[$this->unit] ?? $this->unit;
    }

    /**
     * Stok sekarang, dihitung dari seluruh mutasi. Dipakai di halaman satu barang.
     * Untuk tabel daftar, pakai kolom agregat supaya cukup satu kueri untuk semua baris.
     */
    public function currentStock(): int
    {
        return (int) $this->transactions()->sum('quantity');
    }

    /**
     * Stok seandainya satu mutasi tertentu tidak ada. Dipakai saat memeriksa apakah
     * sebuah mutasi baru atau mutasi yang diubah akan membuat stok jadi minus.
     */
    public function stockExcluding(?int $transactionId): int
    {
        $query = $this->transactions();

        if ($transactionId !== null) {
            $query->whereKeyNot($transactionId);
        }

        return (int) $query->sum('quantity');
    }

    /**
     * Tiga keadaan yang dipakai di layar. Urutannya penting: habis diperiksa lebih
     * dulu, karena stok nol juga selalu berada di bawah batas minimum.
     */
    public function stockState(?int $stock = null): string
    {
        $stock ??= $this->currentStock();

        return match (true) {
            $stock <= 0 => 'habis',
            $stock <= $this->minimum_stock => 'menipis',
            default => 'aman',
        };
    }

    public function stockStateLabel(?int $stock = null): string
    {
        return match ($this->stockState($stock)) {
            'habis' => 'Habis',
            'menipis' => 'Perlu dipesan',
            default => 'Aman',
        };
    }

    public function stockStateColor(?int $stock = null): string
    {
        return match ($this->stockState($stock)) {
            'habis' => 'danger',
            'menipis' => 'warning',
            default => 'success',
        };
    }

    public function formatQuantity(int $quantity): string
    {
        return number_format($quantity, 0, ',', '.').' '.$this->unit;
    }

    /**
     * Barang yang stoknya sudah sampai atau di bawah batas minimum.
     *
     * Ditulis sebagai subkueri, bukan HAVING, supaya masih bisa digabung dengan
     * penyaring lain dan dengan pengurutan tabel tanpa saling mengganggu.
     */
    public function scopeNeedsRestock(Builder $query): Builder
    {
        return $query->whereRaw(
            '(select coalesce(sum(quantity), 0) from supply_transactions'
            .' where supply_transactions.supply_item_id = supply_items.id) <= supply_items.minimum_stock'
        );
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->whereRaw(
            '(select coalesce(sum(quantity), 0) from supply_transactions'
            .' where supply_transactions.supply_item_id = supply_items.id) <= 0'
        );
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
