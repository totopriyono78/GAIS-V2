<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use Auditable;

    /**
     * Semua aksi yang dikenal sistem. Modul memilih sebagian dari daftar ini
     * lewat kolom available_actions, sehingga matriks izin tidak menampilkan
     * aksi yang tidak punya arti untuk modul tersebut.
     */
    public const ACTIONS = [
        'read' => 'Lihat',
        // Tanpa izin ini, orang hanya melihat barisnya sendiri. Dipakai modul yang
        // dibuka untuk semua karyawan, bukan hanya untuk tim GA.
        'read_all' => 'Lihat milik semua orang',
        'create' => 'Tambah',
        'update' => 'Ubah',
        'delete' => 'Hapus',
        'export' => 'Ekspor',
        'print' => 'Cetak',
        'approve' => 'Setujui',
        'accept' => 'Terima dan tugaskan',
        'assign' => 'Tentukan pelaksana',
        // Dipisah dari approve, karena yang menyetujui tagihan dan yang mengeluarkan
        // uangnya memang dua orang yang berbeda di hampir semua perusahaan.
        'pay' => 'Tandai sudah dibayar',
        'close' => 'Tutup periode',
        'reopen' => 'Buka kembali periode',
    ];

    protected $fillable = [
        'code',
        'name',
        'description',
        'group',
        'icon',
        'sort',
        'available_actions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'available_actions' => 'array',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Registri modul adalah sumber kebenaran daftar izin. Setiap kali modul
        // disimpan, baris izinnya disamakan supaya matriks role tidak pernah
        // menampilkan aksi yang tidak lagi disediakan modul.
        static::saved(fn (Module $module) => $module->syncPermissions());
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    public function syncPermissions(): void
    {
        $actions = $this->available_actions ?? [];

        foreach ($actions as $action) {
            Permission::query()->updateOrCreate(
                ['key' => $this->code.'.'.$action],
                [
                    'module_id' => $this->id,
                    'action' => $action,
                    'name' => self::actionLabel($action).' '.$this->name,
                ],
            );
        }

        Permission::query()
            ->where('module_id', $this->id)
            ->whereNotIn('action', $actions)
            ->delete();
    }

    public static function actionLabel(string $action): string
    {
        return self::ACTIONS[$action] ?? ucfirst($action);
    }

    public function getAuditLabel(): string
    {
        return $this->name;
    }
}
