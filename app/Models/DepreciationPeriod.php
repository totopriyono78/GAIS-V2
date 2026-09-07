<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use App\Support\Periode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu bulan penyusutan yang sudah ditutup.
 *
 * Barisnya hanya ada untuk periode yang sudah ditutup. Tidak ada keadaan "sedang
 * berjalan" yang tersimpan, karena periode yang belum ditutup belum punya angka resmi:
 * angkanya masih ikut berubah kalau ada aset baru dicatat atau umur ekonomis diperbaiki.
 * Yang tersimpan hanyalah keputusan bahwa satu bulan sudah selesai.
 */
class DepreciationPeriod extends Model
{
    use Auditable;

    protected $fillable = [
        'period',
        'closed_at',
        'closed_by_user_id',
        'total_expense',
        'asset_count',
        'catch_up_count',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
            'total_expense' => 'decimal:2',
            'asset_count' => 'integer',
            'catch_up_count' => 'integer',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(DepreciationEntry::class);
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function label(): string
    {
        return Periode::label($this->period);
    }

    /**
     * Periode terakhir yang sudah ditutup, atau null kalau belum pernah ada penutupan.
     */
    public static function terakhir(): ?self
    {
        return static::query()->orderByDesc('period')->first();
    }

    /**
     * Hanya periode terakhir yang boleh dibuka kembali.
     *
     * Membuka periode di tengah akan membuat akumulasi periode sesudahnya menggantung
     * pada angka yang sudah tidak ada, dan tidak ada cara memperbaikinya selain menutup
     * ulang seluruh periode sesudahnya. Batasan ini yang membuat pembatalan aman untuk
     * salah tutup, tanpa membuka pintu bagi penulisan ulang buku lama.
     */
    public function canBeReopened(): bool
    {
        return $this->exists
            && ! static::query()->where('period', '>', $this->period)->exists();
    }

    public function getAuditLabel(): string
    {
        return 'Penyusutan '.$this->label();
    }
}
