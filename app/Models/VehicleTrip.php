<?php

namespace App\Models;

use App\Services\OdometerKendaraan;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu kali kendaraan keluar dan kembali.
 *
 * Jaraknya tidak pernah disimpan. Ia selalu selisih dua angka odometer, jadi tidak
 * mungkin ada baris yang jaraknya tidak cocok dengan odometernya sendiri.
 */
class VehicleTrip extends Model
{
    use Auditable;

    protected $fillable = [
        'vehicle_id',
        'vehicle_booking_id',
        'driver_employee_id',
        'departed_at',
        'returned_at',
        'start_odometer_km',
        'end_odometer_km',
        'destination',
        'purpose',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'departed_at' => 'datetime',
            'returned_at' => 'datetime',
            'start_odometer_km' => 'integer',
            'end_odometer_km' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VehicleTrip $perjalanan) {
            if (blank($perjalanan->created_by_user_id)) {
                $perjalanan->created_by_user_id = Auth::id();
            }
        });

        // Odometer kendaraan ikut maju setiap kali perjalanan disimpan dengan angka
        // akhir. Yang menjaganya satu tempat, bukan tersebar di tiap layar.
        static::saved(function (VehicleTrip $perjalanan) {
            if (filled($perjalanan->end_odometer_km) && $perjalanan->vehicle) {
                app(OdometerKendaraan::class)->majukan(
                    $perjalanan->vehicle,
                    $perjalanan->end_odometer_km,
                    $perjalanan->returned_at ?? $perjalanan->departed_at,
                );
            }

            // Pemesanan ikut selesai saat perjalanannya ditutup, supaya tim GA tidak
            // perlu menutup hal yang sama dua kali di dua layar.
            if (filled($perjalanan->end_odometer_km)) {
                $perjalanan->booking?->selesaikanDariPerjalanan();
            }
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(VehicleBooking::class, 'vehicle_booking_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'driver_employee_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- turunan

    public function selesai(): bool
    {
        return filled($this->end_odometer_km);
    }

    public function jarakKm(): ?int
    {
        if (! $this->selesai()) {
            return null;
        }

        return max($this->end_odometer_km - $this->start_odometer_km, 0);
    }

    public function jarakLabel(): string
    {
        $jarak = $this->jarakKm();

        if ($jarak === null) {
            return 'Belum kembali';
        }

        return number_format($jarak, 0, ',', '.').' km';
    }

    public function lamaLabel(): string
    {
        if (blank($this->returned_at)) {
            return 'Masih di jalan';
        }

        $menit = (int) $this->departed_at->diffInMinutes($this->returned_at, false);

        if ($menit < 60) {
            return max($menit, 0).' menit';
        }

        $jam = intdiv($menit, 60);
        $sisa = $menit % 60;

        return $sisa === 0 ? $jam.' jam' : $jam.' jam '.$sisa.' menit';
    }

    public function pengemudi(): string
    {
        return $this->driver?->full_name
            ?? $this->vehicle?->defaultDriver?->full_name
            ?? 'Tidak dicatat';
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeBerjalan(Builder $query): Builder
    {
        return $query->whereNull('end_odometer_km');
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->whereNotNull('end_odometer_km');
    }

    public function getAuditLabel(): string
    {
        return ($this->vehicle?->plate_number ?? '').' ke '.$this->destination;
    }
}
