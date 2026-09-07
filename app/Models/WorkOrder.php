<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Perintah kerja pemeliharaan.
 *
 * Statusnya empat: dibuka, dikerjakan, selesai, dibatalkan. Perpindahan status bukan
 * sekadar mengganti label, melainkan punya akibat, dan akibat itu diletakkan di model
 * supaya jalur apa pun menghasilkan hal yang sama:
 *
 * - Diselesaikan: tanggal selesai terisi, jadwal preventif asalnya maju, dan kondisi
 *   fisik aset ikut diperbarui kalau petugas menyebutkannya.
 * - Dibatalkan: tidak menyentuh apa apa selain dirinya sendiri, karena pekerjaan yang
 *   dibatalkan memang tidak pernah terjadi.
 */
class WorkOrder extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'work_order';

    public const TYPES = [
        'preventif' => 'Preventif',
        'korektif' => 'Korektif',
    ];

    public const PRIORITIES = [
        'rendah' => 'Rendah',
        'normal' => 'Normal',
        'tinggi' => 'Tinggi',
        'mendesak' => 'Mendesak',
    ];

    public const STATUSES = [
        'dibuka' => 'Dibuka',
        'dikerjakan' => 'Sedang dikerjakan',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang berarti pekerjaannya masih menggantung. */
    public const OPEN_STATUSES = ['dibuka', 'dikerjakan'];

    protected $fillable = [
        'code',
        'asset_id',
        'type',
        'maintenance_schedule_id',
        'maintenance_visit_id',
        'service_request_id',
        'priority',
        'status',
        'reported_date',
        'reported_by_employee_id',
        'problem',
        'scheduled_date',
        'vendor_id',
        'technician_employee_id',
        'completed_date',
        'work_done',
        'cost',
        'condition_after',
        'cancel_reason',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'reported_date' => 'date',
            'scheduled_date' => 'date',
            'completed_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WorkOrder $wo) {
            if (blank($wo->code)) {
                $wo->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($wo->created_by_user_id)) {
                $wo->created_by_user_id = Auth::id();
            }
        });

        /*
         * Akibat penyelesaian dipasang di model, bukan di tombolnya. Perintah kerja bisa
         * diselesaikan dari layar daftar, dari layar ubah, atau nanti dari impor, dan
         * ketiganya harus memajukan jadwal serta memperbarui kondisi aset dengan cara
         * yang persis sama.
         */
        static::saved(function (WorkOrder $wo) {
            if ($wo->status !== 'selesai' || blank($wo->completed_date)) {
                return;
            }

            /*
             * Perintah kerja preventif menutup kunjungannya, dan kunjungan itu yang
             * menggeser jadwalnya. Rantainya sengaja begitu, bukan langsung ke jadwal,
             * supaya riwayat kunjungan selalu ikut terisi apa pun jalur penyelesaiannya.
             */
            $kunjungan = $wo->maintenanceVisit;

            if ($kunjungan && $kunjungan->isOpen()) {
                $kunjungan->forceFill([
                    'status' => 'dikerjakan',
                    'completed_date' => $wo->completed_date,
                    'vendor_id' => $wo->vendor_id ?: $kunjungan->vendor_id,
                    'technician_employee_id' => $wo->technician_employee_id ?: $kunjungan->technician_employee_id,
                    'result' => $wo->work_done,
                    'cost' => $wo->cost,
                ])->save();
            } elseif ($kunjungan === null) {
                // Perintah kerja preventif lama yang menempel pada jadwal tanpa kunjungan.
                $wo->maintenanceSchedule?->catatSelesai($wo->completed_date);
            }

            if (filled($wo->condition_after) && $wo->asset && $wo->asset->condition !== $wo->condition_after) {
                $wo->asset->forceFill(['condition' => $wo->condition_after])->save();
            }
        });

        /*
         * Tiket yang melahirkan perintah kerja ini ikut bergerak, baik saat pekerjaannya
         * selesai maupun saat dibatalkan. Dipisah dari penutup di atas karena pembatalan
         * juga harus sampai ke tiketnya: pekerjaan yang tidak jadi dikerjakan bukan berarti
         * masalah pelapornya ikut hilang, jadi tiketnya kembali menunggu tim GA.
         */
        static::saved(function (WorkOrder $wo) {
            if (in_array($wo->status, ['selesai', 'dibatalkan'], true)) {
                $wo->serviceRequest?->ikutiPerintahKerja($wo);
            }
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class);
    }

    public function maintenanceVisit(): BelongsTo
    {
        return $this->belongsTo(MaintenanceVisit::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'technician_employee_id');
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reported_by_employee_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WorkOrderAttachment::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'dibuka' => 'warning',
            'dikerjakan' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'gray',
            default => 'gray',
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'mendesak' => 'danger',
            'tinggi' => 'warning',
            'normal' => 'gray',
            default => 'gray',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    public function isDone(): bool
    {
        return $this->status === 'selesai';
    }

    /**
     * Siapa yang mengerjakan, sebagai satu kalimat. Vendor lebih dulu karena kalau
     * keduanya terisi, artinya teknisi internal mendampingi vendor, bukan sebaliknya.
     */
    public function pelaksana(): string
    {
        $nama = [];

        if ($this->vendor) {
            $nama[] = $this->vendor->name;
        }

        if ($this->technician) {
            $nama[] = $this->technician->full_name;
        }

        return $nama === [] ? 'Belum ditugaskan' : implode(', ', $nama);
    }

    /**
     * Berapa lama pekerjaan ini menggantung, dalam hari. Dihitung dari tanggal lapor
     * sampai selesai, atau sampai hari ini kalau belum selesai.
     */
    public function umurHari(): int
    {
        $akhir = $this->completed_date ?: now();

        return (int) $this->reported_date->diffInDays($akhir, false);
    }

    /*
     * Kedua penyaring di bawah menyebut nama tabelnya lewat qualifyColumn().
     *
     * Tanpa itu, penyaring ini meledak begitu dipakai bersama join ke tabel yang juga
     * punya kolom status, dan tabel aset memang punya. Gejalanya muncul jauh dari
     * sebabnya: laporan anggaran gagal terbuka dengan pesan "column reference status is
     * ambiguous", padahal yang salah ada di model perintah kerja.
     */
    public function scopeTerbuka(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::OPEN_STATUSES);
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'selesai');
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.($this->asset?->code ?? '');
    }
}
