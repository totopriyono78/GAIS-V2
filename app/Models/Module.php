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
        // Menyerahkan barang dari gudang. Dipisah dari approve karena yang menyetujui
        // keperluannya adalah atasan pemohon, sedangkan yang membuka lemari dan
        // mengurangi stok adalah tim GA.
        'issue' => 'Serahkan barang',
        // Mencatat barang datang dari pemasok. Dipisah dari approve karena yang menyetujui
        // pesanannya adalah manajer, sedangkan yang menerima dan menghitung barangnya di
        // gudang adalah staf, dan keduanya menjawab pertanyaan yang berbeda.
        'receive' => 'Terima barang datang',
        // Menerapkan hasil penghitungan fisik ke buku stok. Dipisah dari update karena
        // menutup lembar hitungan dan menggeser angka gudang adalah dua keputusan yang
        // berbeda, dan yang kedua tidak boleh terjadi sebagai efek samping yang pertama.
        'adjust' => 'Terapkan penyesuaian stok',
        // Tanpa izin ini, seseorang hanya bisa mengajukan atas namanya sendiri. Dengan
        // izin ini, ia bisa mengajukan atas nama orang lain, dan itulah yang dipakai
        // perwakilan departemen yang mengumpulkan kebutuhan seluruh timnya.
        'request_for_others' => 'Ajukan atas nama orang lain',
        // Dipisah dari approve, karena menyetujui keperluannya dan memeriksa buktinya
        // adalah dua pertanyaan berbeda yang di banyak perusahaan ditanyakan dua orang.
        'verify' => 'Periksa bukti',
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
