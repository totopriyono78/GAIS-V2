<?php

namespace App\Support\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Dipasang di model User. Menggabungkan izin dari semua role aktif milik pengguna,
 * lalu menerapkan override per pengguna di atasnya.
 */
trait HasPermissions
{
    protected ?array $resolvedPermissionKeys = null;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissionOverrides(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withPivot('granted');
    }

    public function permissionKeys(): array
    {
        if ($this->resolvedPermissionKeys !== null) {
            return $this->resolvedPermissionKeys;
        }

        if ($this->is_super_admin) {
            return $this->resolvedPermissionKeys = Permission::query()->pluck('key')->all();
        }

        $keys = $this->roles()
            ->where('roles.is_active', true)
            ->with('permissions:id,key')
            ->get()
            ->flatMap(fn (Role $role) => $role->permissions->pluck('key'))
            ->unique()
            ->values()
            ->all();

        $keys = array_flip($keys);

        foreach ($this->permissionOverrides()->get() as $permission) {
            if ($permission->pivot->granted) {
                $keys[$permission->key] = true;
            } else {
                unset($keys[$permission->key]);
            }
        }

        return $this->resolvedPermissionKeys = array_keys($keys);
    }

    public function forgetPermissionCache(): void
    {
        $this->resolvedPermissionKeys = null;
    }

    public function hasPermission(string $key): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        return in_array($key, $this->permissionKeys(), true);
    }

    public function hasAnyPermission(array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->hasPermission($key)) {
                return true;
            }
        }

        return false;
    }

    public function hasRole(string $code): bool
    {
        return $this->roles()->where('code', $code)->exists();
    }
}
