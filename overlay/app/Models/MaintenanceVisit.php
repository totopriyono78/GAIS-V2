<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Satu jatuh tempo pemeliharaan preventif, beserta apa yang terjadi padanya.
 *
 * Tiga status saja, dan ketiganya adalah fakta yang berbeda:
 *
 * - Dijadwalkan: sudah waktunya atau akan waktunya, belum ada yang terjadi.
 * - Dikerjakan: vendor atau teknisi datang, ada tanggalnya, ada hasilnya.
 * - Dilewati: sengaja tidak dikerjakan, dan alasannya wajib ditulis.
 *
 * Yang ketiga itu yang paling sering hilang di sistem lain. Tanpa Dilewati, satu satunya
 * cara melewatkan servis adalah dengan tidak melakukan apa apa, dan setahun kemudian
 * tidak ada yang bisa membedakan keputusan yang diambil sadar dari kelalaian.
 */
class MaintenanceVisit extends Model
{
    use Auditable;

    public const STATUSES = [
        'dijadwalkan' => 'Dijadwalkan',
        'dikerjakan' => 'Sudah dikerjakan',
        'dilewati' => 'Dilewati',
    ];

    /** Status yang berarti kunjungan ini masih menunggu keputusan. */
    public const OPEN_STATUS = 'dijadwalkan';

    protected $fillable = [
        'maintenance_schedule_id',
        'asset_id',
        'sequence',
        'due_date',
        'status',
        'completed_date',
        'vendor_id',
        'technician_employee_id',
        'result',
        'cost',
        'skip_reason',
        'closed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_date' => 'date',
            'sequence' => 'integer',
            'cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (MaintenanceVisit $kunjungan) {
            if ($kunjungan->status !== self::OPEN_STATUS && blank($kunjungan->closed_by_user_id)) {
                $kunjungan->closed_by_user_id = Auth::id();
            }
        });

        /*
         * Menutup satu kunjungan selalu melahirkan kunjungan berikutnya dan memperbarui
         * ringkasan di jadwalnya. Diletakkan di model, bukan di tombol, supaya jalur mana
         * pun menghasilkan hal yang sama: tombol di layar, penyelesaian perintah kerja,
         * maupun perintah artisan nanti.
         */
        static::saved(function (MaintenanceVisit $kunjungan) {
            if ($kunjungan->status === self::OPEN_STATUS) {
                return;
            }

            $kunjungan->maintenanceSchedule?->lanjutkanSetelah($kunjungan);
        });
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'technician_employee_id');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::OPEN_STATUS;
    }

    public function isDone(): bool
    {
        return $this->status === 'dikerjakan';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'dikerjakan' => 'success',
            'dilewati' => 'gray',
            default => $this->terlambat() ? 'danger' : 'warning',
        };
    }

    /** Sisa hari sampai jatuh tempo. Negatif berarti sudah lewat. */
    public function sisaHari(): int
    {
        return (int) Carbon::today()->diffInDays($this->due_date, false);
    }

    public function terlambat(): bool
    {
        return $this->isOpen() && $this->sisaHari() < 0;
    }

    /**
     * Keterangan satu baris untuk daftar riwayat. Kunjungan yang sudah selesai menyebut
     * seberapa jauh dari jadwalnya, karena itu yang dilihat orang saat menilai apakah
     * vendor datang tepat waktu.
     */
    public function keteranganWaktu(): string
    {
        if ($this->isOpen()) {
            $sisa = $this->sisaHari();

            return match (true) {
                $sisa < 0 => 'Lewat '.abs($sisa).' hari',
                $sisa === 0 => 'Jatuh tempo hari ini',
                default => 'Tinggal '.$sisa.' hari',
            };
        }

        if ($this->status === 'dilewati') {
            return 'Tidak dikerjakan';
        }

        if (blank($this->completed_date)) {
            return 'Tanggal selesai tidak tercatat';
        }

        $selisih = (int) $this->due_date->diffInDays($this->completed_date, false);

        return match (true) {
            $selisih > 0 => 'Terlambat '.$selisih.' hari dari jadwal',
            $selisih < 0 => 'Lebih cepat '.abs($selisih).' hari dari jadwal',
            default => 'Tepat pada jadwal',
        };
    }

    public function pelaksana(): string
    {
        $nama = [];

        if ($this->vendor) {
            $nama[] = $this->vendor->name;
        }

        if ($this->technician) {
            $nama[] = $this->technician->full_name;
        }

        return $nama === [] ? 'Tidak tercatat' : implode(', ', $nama);
    }

    /*
     * Nama kolom disebut lengkap dengan tabelnya, karena penyaring ini bisa dipakai
     * bersama join ke tabel aset yang juga punya kolom status dan due_date sendiri.
     * Alasan yang sama dengan penyaring di WorkOrder.
     */
    public function scopeTerbuka(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), self::OPEN_STATUS);
    }

    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->terbuka()->whereDate($query->qualifyColumn('due_date'), '<', Carbon::today());
    }

    public function scopeJatuhTempo(Builder $query, int $dalamHari = 0): Builder
    {
        return $query->terbuka()->whereDate($query->qualifyColumn('due_date'), '<=', Carbon::today()->addDays($dalamHari));
    }

    public function getAuditLabel(): string
    {
        return 'Kunjungan '.$this->sequence.' '.($this->maintenanceSchedule?->name ?? '')
            .' ('.($this->asset?->code ?? '').')';
    }
}
