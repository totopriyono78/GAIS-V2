<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use App\Support\Concerns\HasPermissions;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use Auditable;
    use HasFactory;
    use HasPermissions;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'is_active',
        'clearance_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected array $auditExclude = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'clearance_level' => 'integer',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * Departemen pengguna, diambil dari data karyawannya.
     *
     * Tidak disimpan sebagai kolom tersendiri di users. Satu orang punya satu
     * departemen, dan tempatnya sudah ada di tabel employees; menyalinnya ke
     * users berarti dua tempat yang harus dijaga tetap sama, dan cepat atau
     * lambat keduanya akan berbeda.
     */
    public function departmentId(): ?int
    {
        return $this->employee?->department_id;
    }

    /**
     * Tingkat kewenangan melihat dokumen rahasia.
     *
     * Dimensi yang berdiri sendiri dari izin: seseorang bisa punya izin
     * documents.read dan tetap tidak boleh membuka dokumen sangat rahasia.
     * Angkanya sejajar dengan enum Confidentiality, 0 sampai 3.
     *
     * Kalau nanti diturunkan dari jabatan, method ini yang diubah, dan kolom
     * clearance_level boleh dilepas tanpa menyentuh satu pun pemanggilnya.
     */
    public function clearanceLevel(): int
    {
        if ($this->is_super_admin) {
            return 3;
        }

        return (int) ($this->clearance_level ?? 1);
    }

    /**
     * Cakupan data terluas dari seluruh peran aktif yang dimiliki.
     *
     * Diambil yang terluas, bukan yang tersempit, karena peran diberikan untuk
     * menambah kemampuan. Orang yang sudah menjadi Manajer GA tidak kehilangan
     * jangkauannya hanya karena ia juga terdaftar sebagai karyawan biasa.
     */
    public function dataScope(): string
    {
        if ($this->is_super_admin) {
            return 'all';
        }

        $urutan = ['own' => 0, 'department' => 1, 'location' => 1, 'all' => 2];

        $terluas = 'own';

        foreach ($this->roles()->where('roles.is_active', true)->pluck('data_scope') as $scope) {
            $scope = $scope ?: 'own';

            if (($urutan[$scope] ?? 0) > ($urutan[$terluas] ?? 0)) {
                $terluas = $scope;
            }
        }

        return $terluas;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function getAuditLabel(): string
    {
        return $this->name;
    }
}
