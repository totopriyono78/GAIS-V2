<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

/**
 * Satu baris buku stok: barang masuk, barang keluar, atau koreksi.
 *
 * Kolom quantity disimpan bertanda supaya stok cukup dihitung dengan satu
 * SUM. Layar tetap meminta angka positif, dan tanda dipasang di sini dari
 * jenis mutasinya, sehingga tidak ada tempat di aplikasi yang bisa memasukkan
 * angka bertanda salah.
 */
class SupplyTransaction extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'supply_transaction';

    public const TYPES = [
        'masuk' => 'Barang masuk',
        'keluar' => 'Barang keluar',
        'koreksi_tambah' => 'Koreksi tambah',
        'koreksi_kurang' => 'Koreksi kurang',
    ];

    /** Jenis yang menambah stok. Sisanya mengurangi. */
    public const ADDING_TYPES = ['masuk', 'koreksi_tambah'];

    protected $fillable = [
        'code',
        'supply_item_id',
        'type',
        'quantity',
        'unit_price',
        'transaction_date',
        'department_id',
        'employee_id',
        'supplier',
        'reference',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupplyTransaction $transaction) {
            if (blank($transaction->code)) {
                $transaction->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($transaction->created_by_user_id)) {
                $transaction->created_by_user_id = Auth::id();
            }
        });

        // Pertahanan terakhir. Layar sudah memeriksa lebih dulu dan memberi pesan
        // yang enak dibaca, tetapi perintah artisan dan impor data tidak lewat layar.
        static::saving(function (SupplyTransaction $transaction) {
            $transaction->quantity = self::signedQuantity($transaction->type, $transaction->quantity);

            $item = $transaction->item;

            if ($item === null) {
                return;
            }

            $hasil = $item->stockExcluding($transaction->exists ? $transaction->getKey() : null)
                + $transaction->quantity;

            if ($hasil < 0) {
                throw new RuntimeException(
                    "Mutasi ini membuat stok {$item->code} jadi minus. Stok tidak boleh kurang dari nol."
                );
            }
        });

        // Harga satuan terakhir ikut disimpan di barangnya, supaya nilai persediaan
        // bisa dihitung tanpa menelusuri mutasi satu per satu. Disimpan diam diam
        // karena harga itu salinan dari mutasi yang sudah punya jejak auditnya sendiri.
        static::saved(function (SupplyTransaction $transaction) {
            if ($transaction->type !== 'masuk' || blank($transaction->unit_price)) {
                return;
            }

            $transaction->item?->forceFill(['last_price' => $transaction->unit_price])->saveQuietly();
        });

        static::deleting(function (SupplyTransaction $transaction) {
            $item = $transaction->item;

            if ($item === null) {
                return;
            }

            if ($item->stockExcluding($transaction->getKey()) < 0) {
                throw new RuntimeException(
                    "Menghapus mutasi ini membuat stok {$item->code} jadi minus."
                );
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SupplyItem::class, 'supply_item_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public static function adds(?string $type): bool
    {
        return in_array($type, self::ADDING_TYPES, true);
    }

    /**
     * Mengubah angka yang diketik orang menjadi angka bertanda untuk disimpan.
     * Tandanya ditentukan jenis mutasi, bukan oleh yang mengetik.
     */
    public static function signedQuantity(?string $type, int|float|string|null $quantity): int
    {
        $angka = abs((int) $quantity);

        return self::adds($type) ? $angka : -$angka;
    }

    /**
     * Memeriksa apakah sebuah mutasi akan membuat stok minus, sebelum disimpan.
     *
     * Dipakai dari dua tempat: formulir mutasi dan tombol catat mutasi cepat di
     * daftar barang. Mengembalikan pesan kesalahan, atau null kalau tidak apa apa.
     */
    public static function stockProblem(
        ?int $itemId,
        ?string $type,
        int|float|string|null $quantity,
        ?int $ignoreTransactionId = null,
    ): ?string {
        if (blank($itemId) || blank($type) || blank($quantity)) {
            return null;
        }

        if (self::adds($type)) {
            return null;
        }

        $item = SupplyItem::query()->find($itemId);

        if ($item === null) {
            return null;
        }

        $tersedia = $item->stockExcluding($ignoreTransactionId);
        $diminta = abs((int) $quantity);

        if ($diminta <= $tersedia) {
            return null;
        }

        return 'Stok tidak cukup. Yang tersedia '.$item->formatQuantity($tersedia)
            .', yang diminta '.$item->formatQuantity($diminta).'.';
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function typeColor(): string
    {
        return match ($this->type) {
            'masuk' => 'success',
            'keluar' => 'primary',
            'koreksi_tambah', 'koreksi_kurang' => 'warning',
            default => 'gray',
        };
    }

    /** Jumlah apa adanya untuk dibaca orang, lengkap dengan tanda dan satuannya. */
    public function displayQuantity(): string
    {
        $tanda = $this->quantity >= 0 ? '+' : '-';
        $satuan = $this->item?->unit ?? '';

        return trim($tanda.number_format(abs($this->quantity), 0, ',', '.').' '.$satuan);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.($this->item?->name ?? '');
    }
}
