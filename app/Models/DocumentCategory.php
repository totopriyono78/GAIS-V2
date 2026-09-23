<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tempat menaruh dokumen. Maksimal dua tingkat, dan itu disengaja.
 *
 * Kategori menjawab "ditaruh di mana", jenis dokumen menjawab "ini dokumen
 * apa". Keduanya terpisah. Sebuah kontrak vendor berjenis contract_vendor dan
 * bisa ditaruh di kategori Kontrak & Perjanjian, dan keduanya tetap benar.
 */
class DocumentCategory extends Model
{
    use Auditable;

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInduk(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Nama lengkap berikut induknya, untuk daftar pilihan yang datar. */
    public function namaLengkap(): string
    {
        return $this->parent_id === null
            ? $this->name
            : $this->parent->name.' / '.$this->name;
    }

    /**
     * Kategori yang masih dipakai dokumen tidak boleh dihapus, dan kategori
     * yang masih punya anak juga tidak. Dua duanya sudah dijaga foreign key
     * dengan restrictOnDelete, tetapi menanyakannya lebih dulu membuat layar
     * bisa menyembunyikan tombolnya alih alih menampilkan pesan galat.
     */
    public function bisaDihapus(): bool
    {
        return ! $this->documents()->exists() && ! $this->children()->exists();
    }

    public function getAuditLabel(): string
    {
        return $this->namaLengkap();
    }
}
