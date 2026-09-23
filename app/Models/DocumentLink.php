<?php

namespace App\Models;

use App\Enums\LinkRelation;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Tautan satu dokumen ke satu catatan di modul mana pun.
 *
 * Inilah yang membuat modul dokumen menjadi lemari bersama, bukan lemari
 * kesekian. Modul yang memakainya tidak perlu tabel lampiran sendiri, dan bagi
 * pemakainya layar tetap menampilkan daftar lampiran seperti biasa.
 */
class DocumentLink extends Model
{
    use Auditable;

    protected $fillable = [
        'document_id',
        'linkable_type',
        'linkable_id',
        'relation',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'relation' => LinkRelation::class,
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function getAuditLabel(): string
    {
        return ($this->document?->title ?? 'Dokumen').' pada '.class_basename($this->linkable_type).' #'.$this->linkable_id;
    }
}
