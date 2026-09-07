<?php

namespace App\Filament\Widgets;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

/**
 * Semua angka di widget ini dihitung langsung dari basis data. Tidak ada angka contoh.
 */
class RingkasanTahapSatu extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    /*
     * Widget dasbor dimuat bersama halamannya, bukan lewat permintaan susulan.
     * Isinya hanya beberapa kueri agregat, jadi satu permintaan lebih cepat
     * daripada enam permintaan sekaligus, dan tulisan "Loading..." tidak sempat
     * berkedip. Ini juga yang membuat dasbor tetap terbuka penuh di server
     * bawaan PHP, yang hanya melayani satu permintaan pada satu waktu.
     */
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyPermission([
            'employees.read',
            'users.read',
            'roles.read',
            'audit_logs.read',
        ]) ?? false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        if ($user?->hasPermission('employees.read')) {
            $stats[] = Stat::make('Karyawan aktif', (string) Employee::query()->where('is_active', true)->count())
                ->description('Tercatat di data induk')
                ->color('primary');
        }

        if ($user?->hasPermission('users.read')) {
            $stats[] = Stat::make('Akun aktif', (string) User::query()->where('is_active', true)->count())
                ->description('Bisa masuk ke aplikasi')
                ->color('primary');
        }

        if ($user?->hasPermission('roles.read')) {
            $stats[] = Stat::make('Role aktif', (string) Role::query()->where('is_active', true)->count())
                ->description('Menentukan menu dan aksi')
                ->color('primary');
        }

        if ($user?->hasPermission('audit_logs.read')) {
            $stats[] = Stat::make('Perubahan hari ini', (string) AuditLog::query()->whereDate('created_at', today())->count())
                ->description('Tercatat di jejak audit')
                ->color('gray');
        }

        return $stats;
    }
}
