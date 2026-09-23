<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan siapa membuka dan mengunduh dokumen.
 *
 * Untuk audit, mencatat siapa mengubah sering tidak cukup; yang ditanyakan
 * justru siapa saja yang pernah membuka dokumen sensitif.
 *
 * Tabelnya dipartisi per tahun dan kunci utamanya gabungan id dan accessed_at,
 * jadi Eloquent tidak bisa memperlakukannya sebagai model berkunci tunggal.
 * Karena itu barisnya hanya ditulis dan dibaca, tidak pernah diubah, dan
 * timestamps dimatikan.
 */
class DocumentAccessLog extends Model
{
    public $timestamps = false;

    protected $table = 'document_access_log';

    protected $fillable = [
        'document_id',
        'version_id',
        'user_id',
        'action',
        'ip_address',
        'accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'accessed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
