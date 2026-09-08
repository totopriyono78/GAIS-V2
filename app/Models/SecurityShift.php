<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Satu petugas yang dijadwalkan jaga pada satu shift.
 *
 * Ada dua nama di baris ini dan keduanya berbeda arti:
 *
 * - `service_staff_id`: siapa yang **dijadwalkan**. Tidak berubah setelah jadwalnya terbit.
 * - `replacement_staff_id`: siapa yang **benar benar jaga**, kalau ternyata bukan orangnya.
 *
 * Yang membaca cukup memanggil `petugasBertugas()` dan tidak perlu tahu yang mana.
 */
class SecurityShift extends Model
{
    use Auditable;

    /*
     * Jam mulai dan selesai sengaja tidak ditulis di sini. Jam jaga adalah kesepakatan
     * perusahaan, bukan angka yang boleh dikarang, dan menuliskan "07:00 sampai 15:00" di
     * kode berarti setiap layar menampilkan jam yang belum tentu berlaku di kantor ini.
     * Jam sebenarnya dicatat lewat jam masuk dan jam pulang tiap baris.
     */
    public const SHIFTS = [
        'pagi' => 'Pagi',
        'siang' => 'Siang',
        'malam' => 'Malam',
    ];

    public const ATTENDANCES = [
        'belum' => 'Belum dicatat',
        'hadir' => 'Hadir',
        'terlambat' => 'Hadir terlambat',
        'tidak_hadir' => 'Tidak hadir',
        'digantikan' => 'Digantikan',
    ];

    protected $fillable = [
        'shift_date',
        'shift',
        'service_staff_id',
        'location_id',
        'attendance',
        'replacement_staff_id',
        'checked_in_at',
        'checked_out_at',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SecurityShift $jaga) {
            if (blank($jaga->created_by_user_id)) {
                $jaga->created_by_user_id = Auth::id();
            }
        });

        /*
         * Nama pengganti hanya berarti pada kehadiran yang memang digantikan. Kalau
         * kehadirannya kemudian diubah menjadi hadir, nama pengganti yang tertinggal akan
         * membuat rekap menghitung satu penggantian yang tidak pernah terjadi.
         */
        static::saving(function (SecurityShift $jaga) {
            if ($jaga->attendance !== 'digantikan') {
                $jaga->replacement_staff_id = null;
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function staff(): BelongsTo
    {
        return $this->belongsTo(ServiceStaff::class, 'service_staff_id');
    }

    public function replacement(): BelongsTo
    {
        return $this->belongsTo(ServiceStaff::class, 'replacement_staff_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class);
    }

    // ---------------------------------------------------------------- keadaan

    public function shiftLabel(): string
    {
        return self::SHIFTS[$this->shift] ?? $this->shift;
    }

    public function attendanceLabel(): string
    {
        return self::ATTENDANCES[$this->attendance] ?? $this->attendance;
    }

    public function attendanceColor(): string
    {
        return match ($this->attendance) {
            'hadir' => 'success',
            'terlambat' => 'warning',
            'tidak_hadir' => 'danger',
            'digantikan' => 'info',
            default => 'gray',
        };
    }

    public function sudahDicatat(): bool
    {
        return $this->attendance !== 'belum';
    }

    /** Nama petugas yang dijadwalkan. */
    public function petugasDijadwalkan(): string
    {
        return $this->staff?->namaLengkap() ?? 'Petugas sudah dihapus';
    }

    /** Nama petugas yang benar benar jaga, yaitu penggantinya kalau ada. */
    public function petugasBertugas(): ?string
    {
        if ($this->attendance === 'tidak_hadir') {
            return null;
        }

        if ($this->attendance === 'digantikan') {
            return $this->replacement?->namaLengkap() ?? 'Pengganti belum dicatat';
        }

        return $this->petugasDijadwalkan();
    }

    /** Keterangan kehadiran dalam satu kalimat siap baca. */
    public function kehadiranKalimat(): string
    {
        return match ($this->attendance) {
            'belum' => 'Kehadirannya belum dicatat.',
            'hadir' => $this->petugasDijadwalkan().' jaga sesuai jadwal.',
            'terlambat' => $this->petugasDijadwalkan().' jaga, tetapi datang terlambat.',
            'tidak_hadir' => $this->petugasDijadwalkan().' tidak hadir, dan posnya kosong.',
            'digantikan' => $this->petugasDijadwalkan().' digantikan '
                .($this->replacement?->namaLengkap() ?? 'orang yang belum dicatat namanya').'.',
            default => $this->attendanceLabel(),
        };
    }

    public function jamLabel(): string
    {
        if ($this->checked_in_at === null && $this->checked_out_at === null) {
            return 'Tidak dicatat';
        }

        $masuk = $this->checked_in_at?->format('H:i') ?? 'tidak dicatat';
        $pulang = $this->checked_out_at?->format('H:i') ?? 'belum pulang';

        return $masuk.' sampai '.$pulang;
    }

    /** Shift yang kehadirannya belum dicatat padahal tanggalnya sudah lewat. */
    public function scopeTertinggal(Builder $query): Builder
    {
        return $query->where('attendance', 'belum')
            ->whereDate('shift_date', '<', now()->toDateString());
    }

    public function getAuditLabel(): string
    {
        return $this->shift_date?->format('d M Y').' '.$this->shiftLabel().' '.$this->petugasDijadwalkan();
    }
}
