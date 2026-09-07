<?php

namespace App\Models;

use App\Services\OdometerKendaraan;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu kali pengisian bahan bakar.
 *
 * Konsumsi kilometer per liter hanya dihitung antara dua pengisian penuh, karena hanya
 * di dua titik itu isi tangkinya diketahui sama. Pengisian setengah tangki tetap
 * dicatat dan liternya tetap ikut dijumlahkan, tetapi tidak pernah menjadi titik ukur.
 *
 * Perhitungan yang mengabaikan aturan itu akan menghasilkan angka yang kelihatan masuk
 * akal tetapi salah, dan angka semacam itu lebih berbahaya daripada tidak ada angka.
 */
class VehicleRefueling extends Model
{
    use Auditable;

    protected $fillable = [
        'vehicle_id',
        'filled_at',
        'odometer_km',
        'liters',
        'cost',
        'station',
        'fuel_type',
        'is_full_tank',
        'driver_employee_id',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'filled_at' => 'date',
            'odometer_km' => 'integer',
            'liters' => 'decimal:2',
            'cost' => 'decimal:2',
            'is_full_tank' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VehicleRefueling $pengisian) {
            if (blank($pengisian->created_by_user_id)) {
                $pengisian->created_by_user_id = Auth::id();
            }
        });

        static::saved(function (VehicleRefueling $pengisian) {
            if ($pengisian->vehicle) {
                app(OdometerKendaraan::class)->majukan(
                    $pengisian->vehicle,
                    $pengisian->odometer_km,
                    $pengisian->filled_at,
                );
            }
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'driver_employee_id');
    }

    // ---------------------------------------------------------------- konsumsi

    /**
     * Pengisian penuh sebelum pengisian ini, pada kendaraan yang sama.
     */
    public function penuhSebelumnya(): ?self
    {
        if (blank($this->vehicle_id)) {
            return null;
        }

        return static::query()
            ->where('vehicle_id', $this->vehicle_id)
            ->where('is_full_tank', true)
            ->where('odometer_km', '<', $this->odometer_km)
            ->orderByDesc('odometer_km')
            ->first();
    }

    /**
     * Kilometer per liter untuk penggal antara pengisian penuh sebelumnya dan pengisian
     * ini, atau null kalau penggalnya belum lengkap.
     *
     * Liter yang dibagi adalah seluruh liter yang masuk setelah pengisian penuh
     * sebelumnya sampai pengisian ini, termasuk pengisian setengah tangki di antaranya,
     * karena semua bahan bakar itulah yang dipakai menempuh jaraknya.
     */
    public function konsumsiKmPerLiter(): ?float
    {
        if (! $this->is_full_tank) {
            return null;
        }

        $sebelumnya = $this->penuhSebelumnya();

        if ($sebelumnya === null) {
            return null;
        }

        $jarak = $this->odometer_km - $sebelumnya->odometer_km;

        $liter = (float) static::query()
            ->where('vehicle_id', $this->vehicle_id)
            ->where('odometer_km', '>', $sebelumnya->odometer_km)
            ->where('odometer_km', '<=', $this->odometer_km)
            ->sum('liters');

        if ($jarak <= 0 || $liter <= 0) {
            return null;
        }

        return round($jarak / $liter, 2);
    }

    /**
     * Konsumsi sebagai kalimat, lengkap dengan alasan kalau belum bisa dihitung.
     * Menampilkan strip kosong hanya membuat orang mengira datanya hilang.
     */
    public function konsumsiLabel(): string
    {
        if (! $this->is_full_tank) {
            return 'Bukan tangki penuh';
        }

        if ($this->penuhSebelumnya() === null) {
            return 'Belum ada pengisian penuh sebelumnya';
        }

        $konsumsi = $this->konsumsiKmPerLiter();

        if ($konsumsi === null) {
            return 'Odometer atau liternya tidak masuk akal';
        }

        return number_format($konsumsi, 2, ',', '.').' km per liter';
    }

    public function hargaPerLiter(): ?float
    {
        if (blank($this->cost) || (float) $this->liters <= 0) {
            return null;
        }

        return round((float) $this->cost / (float) $this->liters, 0);
    }

    public function litersLabel(): string
    {
        return number_format((float) $this->liters, 2, ',', '.').' liter';
    }

    public function scopePenuh(Builder $query): Builder
    {
        return $query->where('is_full_tank', true);
    }

    public function getAuditLabel(): string
    {
        return ($this->vehicle?->plate_number ?? '').' '.$this->litersLabel();
    }
}
