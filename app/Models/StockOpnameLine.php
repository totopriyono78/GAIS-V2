<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu baris pemeriksaan: satu aset di dalam satu sesi opname.
 *
 * Hasil pemeriksaan tidak disimpan sebagai kolom tersendiri, melainkan
 * disimpulkan dari perbandingan antara yang tercatat dan yang ditemukan.
 * Dengan begitu tidak mungkin ada baris yang kesimpulannya bertentangan
 * dengan datanya sendiri.
 */
class StockOpnameLine extends Model
{
    public const RESULTS = [
        'belum_diperiksa' => 'Belum diperiksa',
        'sesuai' => 'Sesuai catatan',
        'pindah_lokasi' => 'Pindah lokasi',
        'kondisi_berubah' => 'Kondisi berubah',
        'pindah_dan_berubah' => 'Pindah lokasi dan kondisi berubah',
        'tidak_ditemukan' => 'Tidak ditemukan',
    ];

    protected $fillable = [
        'stock_opname_id',
        'asset_id',
        'asset_code',
        'asset_name',
        'expected_location_id',
        'expected_condition',
        'expected_status',
        'checked',
        'found',
        'found_location_id',
        'found_condition',
        'notes',
        'checked_at',
        'checked_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'checked' => 'boolean',
            'found' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function opname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function expectedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'expected_location_id');
    }

    public function foundLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'found_location_id');
    }

    public function result(): string
    {
        if (! $this->checked) {
            return 'belum_diperiksa';
        }

        if ($this->found === false) {
            return 'tidak_ditemukan';
        }

        $pindah = $this->found_location_id !== null
            && $this->found_location_id !== $this->expected_location_id;

        $berubah = $this->found_condition !== null
            && $this->found_condition !== $this->expected_condition;

        return match (true) {
            $pindah && $berubah => 'pindah_dan_berubah',
            $pindah => 'pindah_lokasi',
            $berubah => 'kondisi_berubah',
            default => 'sesuai',
        };
    }

    public function resultLabel(): string
    {
        return self::RESULTS[$this->result()] ?? $this->result();
    }

    public function resultColor(): string
    {
        return match ($this->result()) {
            'sesuai' => 'success',
            'tidak_ditemukan' => 'danger',
            'belum_diperiksa' => 'gray',
            default => 'warning',
        };
    }

    public function hasDifference(): bool
    {
        return ! in_array($this->result(), ['belum_diperiksa', 'sesuai'], true);
    }

    /**
     * Menandai baris sebagai sudah diperiksa dan sesuai catatan, tanpa mengetik apa pun.
     * Ini jalur tercepat untuk barang yang memang ada di tempatnya dan kondisinya sama.
     */
    public function markAsMatching(): void
    {
        $this->forceFill([
            'checked' => true,
            'found' => true,
            'found_location_id' => $this->expected_location_id,
            'found_condition' => $this->expected_condition,
            'checked_at' => now(),
            'checked_by_user_id' => Auth::id(),
        ])->save();
    }

    public function resetCheck(): void
    {
        $this->forceFill([
            'checked' => false,
            'found' => null,
            'found_location_id' => null,
            'found_condition' => null,
            'notes' => null,
            'checked_at' => null,
            'checked_by_user_id' => null,
        ])->save();
    }
}
