<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Pemesanan kendaraan.
 *
 * Alurnya lima langkah, dan tiap langkah dikerjakan orang yang berbeda:
 *
 *   Karyawan memesan -> Atasan menyetujui -> GA menugaskan kendaraan -> Berjalan -> Selesai
 *
 * Langkah kedua bisa dilewati dengan tiga alasan yang sama persis seperti pada
 * permintaan perbaikan, dan alasannya ditulis ke kolomnya sendiri supaya terbaca.
 * Alurnya sengaja dibuat kembar, karena orang yang memakainya sama.
 *
 * Kendaraan baru ditentukan di langkah ketiga. Pemohon tahu ia perlu mobil untuk
 * berempat ke Bekasi, bukan mobil yang mana, dan membiarkan pemohon memilih sendiri
 * adalah cara tercepat membuat dua orang memesan kendaraan yang sama.
 */
class VehicleBooking extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'vehicle_booking';

    public const STATUSES = [
        'diajukan' => 'Menunggu persetujuan',
        'disetujui' => 'Menunggu kendaraan',
        'ditugaskan' => 'Kendaraan siap',
        'berjalan' => 'Sedang berjalan',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang berarti pemesanan masih memesan tempat pada satu kendaraan. */
    public const ACTIVE_STATUSES = ['disetujui', 'ditugaskan', 'berjalan'];

    /** Status yang berarti pemesanan masih menunggu sesuatu terjadi. */
    public const OPEN_STATUSES = ['diajukan', 'disetujui', 'ditugaskan', 'berjalan'];

    protected $fillable = [
        'code',
        'requester_employee_id',
        'department_id',
        'destination',
        'purpose',
        'passenger_count',
        'needs_driver',
        'start_at',
        'end_at',
        'status',
        'vehicle_id',
        'driver_employee_id',
        'assigned_by_user_id',
        'assigned_at',
        'approver_employee_id',
        'approved_at',
        'approval_note',
        'approval_skipped_reason',
        'rejection_reason',
        'closed_at',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'assigned_at' => 'datetime',
            'approved_at' => 'datetime',
            'closed_at' => 'datetime',
            'passenger_count' => 'integer',
            'needs_driver' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VehicleBooking $pemesanan) {
            if (blank($pemesanan->code)) {
                $pemesanan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($pemesanan->created_by_user_id)) {
                $pemesanan->created_by_user_id = Auth::id();
            }

            if (blank($pemesanan->department_id)) {
                $pemesanan->department_id = $pemesanan->requester?->department_id;
            }

            $pemesanan->tentukanJalurPersetujuan();
        });
    }

    // ---------------------------------------------------------------- relasi

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'driver_employee_id');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(VehicleTrip::class);
    }

    // ---------------------------------------------------------------- persetujuan

    public function calonPenyetuju(): ?Employee
    {
        return $this->department?->head
            ?? Department::find($this->department_id)?->head;
    }

    public function alasanLewatPersetujuan(): ?string
    {
        $kepala = $this->calonPenyetuju();

        if ($kepala === null) {
            return 'Departemen pemohon belum punya kepala departemen';
        }

        if ((int) $kepala->getKey() === (int) $this->requester_employee_id) {
            return 'Pemohon adalah kepala departemennya sendiri';
        }

        return null;
    }

    public function tentukanJalurPersetujuan(): void
    {
        $alasan = $this->alasanLewatPersetujuan();

        if ($alasan === null) {
            $this->status = 'diajukan';
            $this->approver_employee_id = $this->calonPenyetuju()?->getKey();

            return;
        }

        $this->status = 'disetujui';
        $this->approved_at = now();
        $this->approval_skipped_reason = $alasan;
    }

    public function setujui(?string $catatan = null): bool
    {
        if ($this->status !== 'diajukan') {
            return false;
        }

        return $this->forceFill([
            'status' => 'disetujui',
            'approved_at' => now(),
            'approval_note' => $catatan,
        ])->save();
    }

    public function tolak(string $alasan): bool
    {
        if (! in_array($this->status, ['diajukan', 'disetujui'], true)) {
            return false;
        }

        return $this->forceFill([
            'status' => 'ditolak',
            'rejection_reason' => $alasan,
            'closed_at' => now(),
        ])->save();
    }

    public function batalkan(): bool
    {
        if (! in_array($this->status, ['diajukan', 'disetujui', 'ditugaskan'], true)) {
            return false;
        }

        return $this->forceFill([
            'status' => 'dibatalkan',
            'closed_at' => now(),
        ])->save();
    }

    // ---------------------------------------------------------------- penugasan

    /**
     * Pemesanan lain pada kendaraan yang sama yang waktunya bertumpuk dengan pemesanan
     * ini. Inilah satu satunya alasan modul pemesanan ini ada: dua orang yang mengira
     * mobil yang sama kosong pada jam yang sama.
     */
    public function bentrok(?int $vehicleId = null): ?self
    {
        $vehicleId ??= $this->vehicle_id;

        if (blank($vehicleId)) {
            return null;
        }

        return static::query()
            ->where('vehicle_id', $vehicleId)
            ->whereKeyNot($this->getKey() ?? 0)
            ->whereIn('status', self::ACTIVE_STATUSES)
            // Dua rentang bertumpuk kalau yang satu mulai sebelum yang lain berakhir,
            // dan berakhir sesudah yang lain mulai. Ditulis begini, bukan dengan
            // whereBetween, supaya pemesanan yang membungkus pemesanan lain ikut kena.
            ->where('start_at', '<', $this->end_at)
            ->where('end_at', '>', $this->start_at)
            ->first();
    }

    public function tugaskan(int $vehicleId, ?int $driverEmployeeId = null): bool
    {
        if (! in_array($this->status, ['disetujui', 'ditugaskan'], true)) {
            return false;
        }

        return $this->forceFill([
            'vehicle_id' => $vehicleId,
            'driver_employee_id' => $driverEmployeeId,
            'assigned_by_user_id' => Auth::id(),
            'assigned_at' => now(),
            'status' => 'ditugaskan',
        ])->save();
    }

    /** Dipanggil dari VehicleTrip saat perjalanannya ditutup. */
    public function selesaikanDariPerjalanan(): void
    {
        if (! in_array($this->status, ['ditugaskan', 'berjalan'], true)) {
            return;
        }

        $this->forceFill(['status' => 'selesai', 'closed_at' => now()])->save();
    }

    // ---------------------------------------------------------------- tampilan

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'diajukan' => 'warning',
            'disetujui' => 'info',
            'ditugaskan' => 'primary',
            'berjalan' => 'success',
            'selesai' => 'gray',
            'ditolak', 'dibatalkan' => 'gray',
            default => 'gray',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    public function jadwalLabel(): string
    {
        $mulai = $this->start_at->translatedFormat('d M Y, H:i');

        if ($this->start_at->isSameDay($this->end_at)) {
            return $mulai.' sampai '.$this->end_at->format('H:i');
        }

        return $mulai.' sampai '.$this->end_at->translatedFormat('d M Y, H:i');
    }

    public function lamaLabel(): string
    {
        $jam = (int) $this->start_at->diffInHours($this->end_at, false);

        return $jam < 24 ? max($jam, 0).' jam' : intdiv($jam, 24).' hari';
    }

    public function penumpangLabel(): string
    {
        if (blank($this->passenger_count)) {
            return 'Jumlah penumpang tidak dicatat';
        }

        return $this->passenger_count.' penumpang';
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeTerbuka(Builder $query): Builder
    {
        return $query->whereIn('status', self::OPEN_STATUSES);
    }

    public function scopeMenungguPersetujuan(Builder $query): Builder
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeMenungguKendaraan(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    /** Pemesanan yang jadwalnya sudah lewat tetapi belum ditutup perjalanannya. */
    public function scopeTerlambatDitutup(Builder $query): Builder
    {
        return $query->whereIn('status', ['ditugaskan', 'berjalan'])
            ->where('end_at', '<', Carbon::now());
    }

    public function getAuditLabel(): string
    {
        return $this->code.' ke '.$this->destination;
    }
}
