<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Folder virtual: kombinasi filter yang disimpan dengan nama sendiri.
 *
 * Pemakai hampir pasti meminta struktur folder, karena itu yang mereka kenal,
 * dan permintaannya akan datang berulang meski sudah dijawab dengan kategori.
 * Ini jawaban yang tidak merusak modelnya. Di layar terasa seperti folder,
 * tetapi satu dokumen boleh muncul di banyak filter tanpa pernah digandakan,
 * dan tidak ada salinan yang bisa menyimpang dari aslinya.
 */
class DocumentSavedFilter extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'filters',
        'user_id',
        'is_shared',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'is_shared' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Milik sendiri, ditambah yang dibagikan orang lain. */
    public function scopeUntuk(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('is_shared', true);
        })->orderBy('sort_order')->orderBy('name');
    }

    public function getAuditLabel(): string
    {
        return $this->name;
    }
}
