<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\NumberSequence;
use RuntimeException;

/**
 * Pembentuk kode aset dengan pola yang lazim dipakai BUMN di Indonesia:
 *
 *     [Kode Departemen] - [Kode Akun COA] - [Tahun Perolehan] - [Nomor Urut]
 *     contoh: FIN-1201-2026-0001
 *
 * Nomor urut dihitung terpisah untuk setiap kombinasi departemen, akun, dan
 * tahun. Jadi komputer milik Finance tahun 2026 punya hitungan sendiri, terpisah
 * dari kendaraan milik Finance di tahun yang sama maupun dari komputer milik
 * departemen lain.
 */
class AssetCodeGenerator
{
    public const SEPARATOR = '-';

    public const PADDING = 4;

    public static function next(Asset $asset): string
    {
        $department = $asset->department ?: Department::query()->find($asset->department_id);
        $category = $asset->category ?: AssetCategory::query()->find($asset->asset_category_id);

        if ($department === null) {
            throw new RuntimeException(
                'Departemen pemakai harus dipilih dulu, karena kodenya jadi segmen pertama kode aset.'
            );
        }

        if ($category === null) {
            throw new RuntimeException(
                'Kategori aset harus dipilih dulu, karena nomor akunnya jadi segmen kedua kode aset.'
            );
        }

        if (blank($category->account_asset)) {
            throw new RuntimeException(
                "Kategori {$category->name} belum punya nomor akun COA. Isi dulu di menu Kategori aset, "
                .'karena nomor akun itu jadi segmen kedua kode aset.'
            );
        }

        $year = $asset->acquisition_date
            ? $asset->acquisition_date->format('Y')
            : now()->format('Y');

        return NumberGenerator::next(
            self::ensureSequence($department->code, $category->account_asset)->code,
            $year,
        );
    }

    /**
     * Satu baris urutan per kombinasi departemen dan akun, dibuat saat pertama dibutuhkan.
     */
    public static function ensureSequence(string $departmentCode, string $accountCode): NumberSequence
    {
        $code = 'asset.'.$departmentCode.'.'.$accountCode;

        return NumberSequence::query()->updateOrCreate(
            ['code' => $code],
            [
                'name' => 'Kode aset '.$departmentCode.' akun '.$accountCode,
                'prefix' => $departmentCode.self::SEPARATOR.$accountCode,
                'separator' => self::SEPARATOR,
                'period_format' => 'Y',
                'padding' => self::PADDING,
            ],
        );
    }

    /**
     * Contoh kode untuk ditampilkan di layar, tanpa memakai nomor urut.
     */
    public static function example(?string $departmentCode, ?string $accountCode, ?string $year = null): string
    {
        return implode(self::SEPARATOR, [
            $departmentCode ?: 'DEPT',
            $accountCode ?: 'AKUN',
            $year ?: now()->format('Y'),
            str_pad('1', self::PADDING, '0', STR_PAD_LEFT),
        ]);
    }
}
