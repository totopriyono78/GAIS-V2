<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Petugas kebersihan dan keamanan, karyawan perusahaan maupun tenaga rekanan.
 *
 * Nama orangnya punya dua sumber yang tidak pernah dipakai bersamaan. Kalau ia karyawan,
 * namanya milik kartu karyawan dan dibaca dari sana. Kalau ia tenaga rekanan, namanya
 * diketik di kolom `name`. Yang membaca cukup memanggil `namaLengkap()` dan tidak perlu
 * tahu ia yang mana.
 */
class ServiceStaff extends Model
{
    use Auditable;

    protected $table = 'service_staff';

    public const KINDS = [
        'kebersihan' => 'Kebersihan',
        'keamanan' => 'Keamanan',
        'keduanya' => 'Kebersihan dan keamanan',
    ];

    protected $fillable = [
        'employee_id',
        'name',
        'vendor_id',
        'kind',
        'phone',
        'start_date',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        /*
         * Satu baris hanya boleh berbentuk salah satu: karyawan, atau tenaga rekanan dengan
         * nama ketikan. Penjaganya ditaruh di model, bukan di formulir, dan bekerja dengan
         * melihat kolom mana yang baru saja berubah.
         *
         * Alasannya bukan selera. Formulirnya menyembunyikan sisi yang tidak dipakai, dan
         * Filament tidak mengirimkan kolom yang sedang tersembunyi saat menyimpan, jadi
         * kolom itu tetap memakai isi lamanya di basis data. Penjaga yang hanya melihat isi
         * akhir karenanya tidak bisa membedakan "karyawan yang sengaja dipertahankan" dari
         * "karyawan lama yang tertinggal", dan pilihan apa pun yang ia ambil akan salah pada
         * separuh kejadian. Yang berubahlah yang menyatakan maksud orangnya.
         *
         * Ketahuan saat pemeriksaan kiriman P: mengubah petugas dari karyawan menjadi tenaga
         * rekanan tersimpan sebagai bukan apa apa. Nama baru diketik, Simpan ditekan, tidak
         * ada pesan galat, dan barisnya tetap menyebut nama karyawan yang lama.
         */
        static::saving(function (ServiceStaff $petugas) {
            // Nama yang baru diketik berarti orangnya tenaga rekanan.
            if ($petugas->isDirty('name') && filled($petugas->name)) {
                $petugas->employee_id = null;
            }

            // Karyawan yang baru ditunjuk berarti namanya milik kartu karyawan.
            if ($petugas->isDirty('employee_id') && $petugas->employee_id !== null) {
                $petugas->name = null;
                $petugas->vendor_id = null;
            }

            // Penutup untuk jalur di luar formulir, misalnya impor atau konsol.
            if ($petugas->employee_id !== null) {
                $petugas->name = null;
                $petugas->vendor_id = null;
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(ServiceArea::class);
    }

    // ---------------------------------------------------------------- keadaan

    /** Karyawan perusahaan, bukan tenaga rekanan. */
    public function isKaryawan(): bool
    {
        return $this->employee_id !== null;
    }

    public function namaLengkap(): string
    {
        if ($this->isKaryawan()) {
            return $this->employee?->full_name ?? 'Karyawan tidak ditemukan';
        }

        return $this->name ?? 'Tanpa nama';
    }

    /** Dari mana orangnya berasal, dalam satu kalimat siap baca. */
    public function asalLabel(): string
    {
        if ($this->isKaryawan()) {
            $departemen = $this->employee?->department?->name;

            return $departemen !== null
                ? 'Karyawan, '.$departemen
                : 'Karyawan perusahaan';
        }

        return $this->vendor !== null
            ? 'Tenaga dari '.$this->vendor->name
            : 'Tenaga luar, tanpa rekanan penyedia';
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? $this->kind;
    }

    /** Nomor telepon yang bisa dihubungi, dari kartu karyawan kalau ada. */
    public function teleponLabel(): ?string
    {
        return $this->phone
            ?? ($this->isKaryawan() ? $this->employee?->phone : null);
    }

    /** Petugas yang mengerjakan kebersihan, termasuk yang merangkap keduanya. */
    public function scopeKebersihan(Builder $query): Builder
    {
        return $query->whereIn('kind', ['kebersihan', 'keduanya']);
    }

    /** Petugas yang mengerjakan keamanan, termasuk yang merangkap keduanya. */
    public function scopeKeamanan(Builder $query): Builder
    {
        return $query->whereIn('kind', ['keamanan', 'keduanya']);
    }

    public function getAuditLabel(): string
    {
        return $this->namaLengkap();
    }
}
