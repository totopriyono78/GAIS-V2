<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use Auditable;

    public const DEPRECIATION_METHODS = [
        'garis_lurus' => 'Garis lurus',
        'saldo_menurun' => 'Saldo menurun ganda',
        'tidak_disusutkan' => 'Tidak disusutkan',
    ];

    /**
     * Kelompok harta berwujud beserta masa manfaatnya dalam bulan.
     * Sumber angka: PMK 72 Tahun 2023, harta bukan bangunan kelompok 1 sampai 4
     * masing masing 4, 8, 16, dan 20 tahun, bangunan permanen 20 tahun dan
     * bangunan tidak permanen 10 tahun.
     */
    public const TAX_GROUPS = [
        'kelompok_1' => ['label' => 'Kelompok 1, bukan bangunan, 4 tahun', 'months' => 48],
        'kelompok_2' => ['label' => 'Kelompok 2, bukan bangunan, 8 tahun', 'months' => 96],
        'kelompok_3' => ['label' => 'Kelompok 3, bukan bangunan, 16 tahun', 'months' => 192],
        'kelompok_4' => ['label' => 'Kelompok 4, bukan bangunan, 20 tahun', 'months' => 240],
        'bangunan_permanen' => ['label' => 'Bangunan permanen, 20 tahun', 'months' => 240],
        'bangunan_tidak_permanen' => ['label' => 'Bangunan tidak permanen, 10 tahun', 'months' => 120],
        'tidak_disusutkan' => ['label' => 'Tidak disusutkan', 'months' => null],
    ];

    protected $fillable = [
        'code',
        'name',
        'description',
        'useful_life_months',
        'depreciation_method',
        'requires_certificate',
        'tax_group',
        'residual_percent',
        'account_asset',
        'account_accumulated',
        'account_expense',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_certificate' => 'boolean',
            'useful_life_months' => 'integer',
            'residual_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (AssetCategory $category) {
            // Masa manfaat diturunkan dari kelompok pajak kalau belum diisi manual,
            // supaya angkanya punya dasar dan tidak dikira tebakan.
            if (blank($category->useful_life_months) && filled($category->tax_group)) {
                $category->useful_life_months = self::TAX_GROUPS[$category->tax_group]['months'] ?? null;
            }

            if ($category->tax_group === 'tidak_disusutkan') {
                $category->depreciation_method = 'tidak_disusutkan';
                $category->useful_life_months = null;
            }
        });
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Label untuk semua daftar pilihan kategori: nomor akun COA lebih dulu, lalu nama.
     * Contohnya 1202 - Bangunan Permanen. Nomor akun ditaruh di depan karena itu yang
     * dipakai tim finance untuk mencocokkan, dan karena nomor akun juga menjadi segmen
     * kedua kode aset, jadi urutannya sama dengan yang tercetak di label barang.
     */
    public function pickerLabel(): string
    {
        $akun = trim((string) $this->account_asset);

        return $akun === '' ? $this->name : $akun.' - '.$this->name;
    }

    /**
     * @return array<string, string>
     */
    public static function taxGroupOptions(): array
    {
        return array_map(fn (array $group): string => $group['label'], self::TAX_GROUPS);
    }

    public function taxGroupLabel(): string
    {
        return self::TAX_GROUPS[$this->tax_group]['label'] ?? 'Belum ditentukan';
    }

    /**
     * Kategori baru bisa dipakai untuk mencatat aset setelah nomor akunnya diisi,
     * karena nomor akun itu yang jadi segmen kedua kode aset.
     */
    public function isUsable(): bool
    {
        return $this->is_active && filled($this->account_asset);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
