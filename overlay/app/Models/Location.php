<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use Auditable;

    public const TYPES = [
        'gedung' => 'Gedung',
        'lantai' => 'Lantai',
        'ruangan' => 'Ruangan',
        'area' => 'Area',
    ];

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getFullPathAttribute(): string
    {
        $parts = [$this->name];
        $node = $this->parent;
        $guard = 0;

        while ($node !== null && $guard < 10) {
            array_unshift($parts, $node->name);
            $node = $node->parent;
            $guard++;
        }

        return implode(' / ', $parts);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
