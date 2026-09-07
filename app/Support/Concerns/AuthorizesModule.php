<?php

namespace App\Support\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Dipasang di setiap Filament Resource. Resource menyebut kode modulnya lewat
 * properti $moduleCode, dan izin dibaca dari tabel permissions. Karena Filament
 * menyembunyikan item navigasi ketika canViewAny() bernilai salah, menu selalu
 * mengikuti izin dan tidak pernah menunjuk ke halaman yang tidak boleh dibuka.
 */
trait AuthorizesModule
{
    public static function moduleCode(): string
    {
        return static::$moduleCode;
    }

    protected static function allows(string $action): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return $user->hasPermission(static::moduleCode().'.'.$action);
    }

    public static function canViewAny(): bool
    {
        return static::allows('read');
    }

    public static function canView(Model $record): bool
    {
        return static::allows('read');
    }

    public static function canCreate(): bool
    {
        return static::allows('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::allows('update');
    }

    public static function canDelete(Model $record): bool
    {
        return static::allows('delete');
    }

    public static function canDeleteAny(): bool
    {
        return static::allows('delete');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
