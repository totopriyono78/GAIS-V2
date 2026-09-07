<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Satu foto kendaraan.
 *
 * Foto tidak pernah kedaluwarsa, jadi tabel ini tidak punya masa berlaku seperti
 * dokumen. Yang dicatat adalah kapan diambil dan pada odometer berapa, karena dua hal
 * itu yang menentukan apakah sebuah lecet sudah ada sebelum kendaraan dipinjam.
 */
class VehiclePhoto extends Model
{
    use Auditable;

    public const TYPES = [
        'depan' => 'Tampak depan',
        'belakang' => 'Tampak belakang',
        'samping' => 'Tampak samping',
        'interior' => 'Interior',
        'mesin' => 'Ruang mesin',
        'kerusakan' => 'Kerusakan atau lecet',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'vehicle_id',
        'type',
        'name',
        'file_path',
        'original_name',
        'size_bytes',
        'taken_date',
        'odometer_km',
        'notes',
        'uploaded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'taken_date' => 'date',
            'odometer_km' => 'integer',
            'size_bytes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VehiclePhoto $foto) {
            if (blank($foto->uploaded_by_user_id)) {
                $foto->uploaded_by_user_id = Auth::id();
            }
        });

        static::saving(function (VehiclePhoto $foto) {
            if (! $foto->isDirty('file_path') && $foto->size_bytes !== null) {
                return;
            }

            $foto->size_bytes = (filled($foto->file_path) && Storage::disk('public')->exists($foto->file_path))
                ? Storage::disk('public')->size($foto->file_path)
                : null;
        });

        static::deleted(function (VehiclePhoto $foto) {
            if (filled($foto->file_path)) {
                Storage::disk('public')->delete($foto->file_path);
            }
        });
    }

    protected function originalName(): Attribute
    {
        return Attribute::set(function (mixed $nilai): ?string {
            if (is_array($nilai)) {
                $nilai = reset($nilai);
            }

            return blank($nilai) ? null : (string) $nilai;
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function jenisLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function url(): ?string
    {
        return filled($this->file_path) ? Storage::disk('public')->url($this->file_path) : null;
    }

    /**
     * Keterangan kapan foto diambil, digabung dengan odometernya kalau ada. Dua hal itu
     * selalu dibaca bersamaan saat membandingkan keadaan kendaraan antar waktu.
     */
    public function keteranganWaktu(): string
    {
        $tanggal = $this->taken_date?->translatedFormat('d F Y') ?? 'Tanggal tidak dicatat';

        if (blank($this->odometer_km)) {
            return $tanggal;
        }

        return $tanggal.', '.number_format($this->odometer_km, 0, ',', '.').' km';
    }

    public function sizeLabel(): string
    {
        $bytes = (int) $this->size_bytes;

        if ($bytes <= 0) {
            return 'Tidak diketahui';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return number_format($bytes / 1024 / 1024, 1, ',', '.').' MB';
    }

    public function getAuditLabel(): string
    {
        return $this->name.' ('.($this->vehicle?->plate_number ?? '').')';
    }
}
