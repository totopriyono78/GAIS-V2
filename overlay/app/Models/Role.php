<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use Auditable;

    public const DATA_SCOPES = [
        'all' => 'Semua data',
        'department' => 'Data departemennya saja',
        'own' => 'Data miliknya saja',
    ];

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_system',
        'data_scope',
        'is_active',
    ];

    protected array $auditExclude = [];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Mengganti seluruh izin role dengan daftar ini. Dipakai layar Role.
     */
    public function syncPermissionKeys(array $keys): void
    {
        $ids = Permission::query()->whereIn('key', $keys)->pluck('id')->all();

        $this->permissions()->sync($ids);
    }

    /**
     * Menambahkan izin tanpa mencabut yang sudah ada. Dipakai seeder saat modul
     * tahap baru muncul, supaya penyesuaian yang dibuat admin tidak hilang.
     */
    public function addPermissionKeys(array $keys): void
    {
        $ids = Permission::query()->whereIn('key', $keys)->pluck('id')->all();

        $this->permissions()->syncWithoutDetaching($ids);
    }

    public function getAuditLabel(): string
    {
        return $this->name;
    }
}
