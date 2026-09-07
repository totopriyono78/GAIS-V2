<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Satu sesi pemeriksaan fisik aset.
 *
 * Alur yang dipakai: susun daftar target, mulai pemeriksaan, catat temuan
 * satu per satu, selesaikan, lalu terapkan penyesuaian. Penyesuaian dipisah
 * dari penyelesaian karena mengubah data induk aset harus jadi tindakan
 * yang disengaja, bukan efek samping menutup sesi.
 */
class StockOpname extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'stock_opname';

    public const STATUSES = [
        'draft' => 'Draf',
        'berjalan' => 'Sedang berjalan',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected $fillable = [
        'code',
        'name',
        'scope_location_id',
        'scope_department_id',
        'scope_asset_category_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'adjusted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (StockOpname $opname) {
            if (blank($opname->code)) {
                $opname->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }
        });
    }

    public function lines(): HasMany
    {
        return $this->hasMany(StockOpnameLine::class);
    }

    /*
     * Nama ketiga relasi di bawah sengaja tidak diawali kata scope, walaupun
     * kolomnya bernama scope_*. Eloquent memperlakukan setiap metode berawalan
     * scope sebagai local query scope, sehingga scopeLocation() akan bentrok
     * dengan mekanisme itu.
     */
    public function targetLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'scope_location_id');
    }

    public function targetDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'scope_department_id');
    }

    public function targetCategory(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'scope_asset_category_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isRunning(): bool
    {
        return $this->status === 'berjalan';
    }

    public function isFinished(): bool
    {
        return $this->status === 'selesai';
    }

    public function isAdjusted(): bool
    {
        return $this->adjusted_at !== null;
    }

    /**
     * Ringkasan cakupan dalam satu kalimat, untuk ditampilkan di layar.
     */
    public function describeScope(): string
    {
        $bagian = [];

        if ($this->scope_location_id !== null) {
            $bagian[] = 'lokasi '.($this->targetLocation?->name ?? '?').' beserta isinya';
        }

        if ($this->scope_department_id !== null) {
            $bagian[] = 'departemen '.($this->targetDepartment?->name ?? '?');
        }

        if ($this->scope_asset_category_id !== null) {
            $bagian[] = 'kategori '.($this->targetCategory?->name ?? '?');
        }

        return $bagian === [] ? 'Seluruh aset' : 'Terbatas pada '.implode(', ', $bagian);
    }

    /**
     * Kueri aset yang masuk cakupan sesi ini.
     */
    public function targetQuery(): Builder
    {
        $query = Asset::query()->where('status', '!=', 'dilepas');

        if ($this->scope_location_id !== null) {
            $query->whereIn('location_id', $this->locationBranchIds($this->scope_location_id));
        }

        if ($this->scope_department_id !== null) {
            $query->where('department_id', $this->scope_department_id);
        }

        if ($this->scope_asset_category_id !== null) {
            $query->where('asset_category_id', $this->scope_asset_category_id);
        }

        return $query;
    }

    /**
     * Lokasi yang dipilih beserta seluruh keturunannya. Memilih satu lantai
     * berarti seluruh ruangan di dalamnya ikut diperiksa.
     */
    protected function locationBranchIds(int $rootId): array
    {
        $ids = [$rootId];
        $level = [$rootId];

        for ($kedalaman = 0; $kedalaman < 5 && $level !== []; $kedalaman++) {
            $level = Location::query()->whereIn('parent_id', $level)->pluck('id')->all();
            $ids = array_merge($ids, $level);
        }

        return $ids;
    }

    /**
     * Menyusun daftar target dari cakupan. Baris lama dihapus lebih dulu supaya
     * daftarnya benar benar mencerminkan keadaan saat disusun.
     */
    public function generateLines(): int
    {
        return DB::transaction(function () {
            $this->lines()->delete();

            $jumlah = 0;

            $this->targetQuery()
                ->with('location')
                ->orderBy('code')
                ->chunkById(200, function ($assets) use (&$jumlah) {
                    $baris = [];

                    foreach ($assets as $asset) {
                        $baris[] = [
                            'stock_opname_id' => $this->id,
                            'asset_id' => $asset->id,
                            'asset_code' => $asset->code,
                            'asset_name' => $asset->name,
                            'expected_location_id' => $asset->location_id,
                            'expected_condition' => $asset->condition,
                            'expected_status' => $asset->status,
                            'checked' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if ($baris !== []) {
                        StockOpnameLine::query()->insert($baris);
                        $jumlah += count($baris);
                    }
                });

            return $jumlah;
        });
    }

    /**
     * Menerapkan temuan ke data induk aset.
     *
     * Yang diubah hanya lokasi dan kondisi, dua hal yang memang diperiksa di
     * lapangan. Aset yang tidak ditemukan sengaja tidak diubah statusnya, karena
     * tidak ketemu saat opname belum tentu berarti hilang, dan mengubahnya
     * otomatis akan menyembunyikan masalah yang justru harus ditindaklanjuti.
     *
     * @return array{lokasi: int, kondisi: int, tidak_ditemukan: int}
     */
    public function applyAdjustments(): array
    {
        $hasil = ['lokasi' => 0, 'kondisi' => 0, 'tidak_ditemukan' => 0];

        DB::transaction(function () use (&$hasil) {
            foreach ($this->lines()->with('asset')->get() as $line) {
                if (! $line->checked) {
                    continue;
                }

                if ($line->found === false) {
                    $hasil['tidak_ditemukan']++;

                    continue;
                }

                $asset = $line->asset;

                if ($asset === null) {
                    continue;
                }

                $berubah = false;

                if ($line->found_location_id !== null && $line->found_location_id !== $asset->location_id) {
                    $asset->location_id = $line->found_location_id;
                    $hasil['lokasi']++;
                    $berubah = true;
                }

                if ($line->found_condition !== null && $line->found_condition !== $asset->condition) {
                    $asset->condition = $line->found_condition;
                    $hasil['kondisi']++;
                    $berubah = true;
                }

                if ($berubah) {
                    $asset->save();
                }
            }

            $this->forceFill([
                'adjusted_at' => now(),
                'adjusted_by_user_id' => Auth::id(),
            ])->save();
        });

        return $hasil;
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
