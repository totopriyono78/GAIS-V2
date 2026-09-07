<?php

namespace App\Models;

use App\Support\Periode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Beban penyusutan satu aset pada satu bulan.
 *
 * Baris ini sengaja menyimpan juga keadaan aset saat dihitung: metodenya, umurnya,
 * nilai perolehannya, dan nilai sisanya. Kelihatannya berlebihan karena semuanya ada
 * di tabel aset. Tetapi tabel aset menyimpan keadaan sekarang, dan keadaan sekarang
 * bisa berubah: umur ekonomis diperbaiki, nilai perolehan dikoreksi setelah faktur
 * yang benar ketemu. Kalau angkanya tidak dibekukan di sini, laporan bulan lalu akan
 * ikut berubah sendiri, dan tidak akan ada yang bisa menjelaskan kenapa.
 */
class DepreciationEntry extends Model
{
    protected $fillable = [
        'depreciation_period_id',
        'asset_id',
        'period',
        'expense',
        'accumulated_after',
        'book_value_after',
        'method',
        'useful_life_months',
        'acquisition_cost',
        'residual_value',
        'months_covered',
        'covers_from',
    ];

    protected function casts(): array
    {
        return [
            'expense' => 'decimal:2',
            'accumulated_after' => 'decimal:2',
            'book_value_after' => 'decimal:2',
            'acquisition_cost' => 'decimal:2',
            'residual_value' => 'decimal:2',
            'useful_life_months' => 'integer',
            'months_covered' => 'integer',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(DepreciationPeriod::class, 'depreciation_period_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function isCatchUp(): bool
    {
        return $this->months_covered > 1;
    }

    /**
     * Keterangan cakupan beban. Beban biasa mencakup satu bulan dan tidak perlu
     * dijelaskan; beban susulan mencakup beberapa bulan dan wajib dijelaskan, karena
     * angkanya jauh lebih besar dari bulan bulan sesudahnya.
     */
    public function coverageLabel(): string
    {
        if (! $this->isCatchUp()) {
            return Periode::label($this->period);
        }

        return Periode::label($this->covers_from).' sampai '.Periode::label($this->period)
            .' ('.$this->months_covered.' bulan)';
    }

    public function methodLabel(): string
    {
        return AssetCategory::DEPRECIATION_METHODS[$this->method] ?? $this->method;
    }
}
