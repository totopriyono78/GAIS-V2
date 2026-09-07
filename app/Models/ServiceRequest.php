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
 * Permintaan perbaikan dari karyawan.
 *
 * Alurnya empat langkah, dan tiap langkah dikerjakan orang yang berbeda:
 *
 *   Karyawan mengajukan  ->  Atasan menyetujui  ->  GA menerima  ->  Pekerjaannya selesai
 *
 * Langkah kedua bisa dilewati, dan itu disengaja. Tiga keadaan membuat persetujuan
 * mustahil atau merugikan, dan tanpa jalan keluar tiketnya akan tersangkut selamanya:
 * departemen yang belum punya kepala, pemohon yang justru kepala departemen itu sendiri,
 * dan pekerjaan mendesak seperti kebocoran atau listrik mati. Ketiganya melewati
 * persetujuan, tetapi alasannya ditulis ke kolomnya sendiri supaya terbaca di layar.
 * Yang ketiga bisa dimatikan dari layar Pengaturan.
 *
 * Langkah ketiga melahirkan perintah kerja korektif, dan sejak saat itu pekerjaannya
 * hidup di sana. Tiket ini berhenti menjadi pekerjaan dan berubah menjadi catatan:
 * siapa melapor kapan, siapa menyetujui, dan berapa lama menunggu.
 */
class ServiceRequest extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'service_request';

    public const PRIORITIES = [
        'rendah' => 'Rendah',
        'normal' => 'Normal',
        'tinggi' => 'Tinggi',
        'mendesak' => 'Mendesak',
    ];

    public const STATUSES = [
        'diajukan' => 'Menunggu persetujuan',
        'disetujui' => 'Menunggu tim GA',
        'diterima' => 'Sedang dikerjakan',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang berarti tiket masih menunggu sesuatu terjadi. */
    public const OPEN_STATUSES = ['diajukan', 'disetujui', 'diterima'];

    protected $fillable = [
        'code',
        'requester_employee_id',
        'department_id',
        'service_request_category_id',
        'location_id',
        'asset_id',
        'title',
        'description',
        'priority',
        'status',
        'submitted_at',
        'approver_employee_id',
        'approved_at',
        'approval_note',
        'approval_skipped_reason',
        'accepted_by_user_id',
        'accepted_at',
        'sla_due_at',
        'work_order_id',
        'closed_at',
        'rejection_reason',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'accepted_at' => 'datetime',
            'sla_due_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceRequest $tiket) {
            if (blank($tiket->code)) {
                $tiket->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($tiket->created_by_user_id)) {
                $tiket->created_by_user_id = Auth::id();
            }

            if (blank($tiket->submitted_at)) {
                $tiket->submitted_at = now();
            }

            // Departemen dibekukan di tiket, bukan dibaca dari karyawannya nanti.
            // Karyawan bisa pindah departemen, dan tiket lama harus tetap menunjukkan
            // departemen mana yang dulu memintanya.
            if (blank($tiket->department_id)) {
                $tiket->department_id = $tiket->requester?->department_id;
            }

            $tiket->tentukanJalurPersetujuan();
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestCategory::class, 'service_request_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function acceptedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ServiceRequestAttachment::class);
    }

    // ---------------------------------------------------------------- alur

    /**
     * Siapa yang seharusnya menyetujui tiket ini: kepala departemen pemohon.
     *
     * Sistem ini tidak menyimpan atasan per karyawan, tetapi departemen sudah punya
     * kepalanya. Memakai yang sudah ada lebih jujur daripada mengarang hierarki baru
     * yang harus diisi ulang seluruhnya sebelum modul ini bisa dipakai.
     */
    public function calonPenyetuju(): ?Employee
    {
        return $this->department?->head
            ?? Department::find($this->department_id)?->head;
    }

    /**
     * Menentukan apakah tiket ini lewat persetujuan atau langsung ke tim GA, dan
     * menuliskan alasannya kalau dilewati. Dipanggil sekali saat tiket dibuat.
     */
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

    /**
     * Alasan tiket ini tidak perlu persetujuan, atau null kalau memang perlu.
     */
    public function alasanLewatPersetujuan(): ?string
    {
        if ($this->priority === 'mendesak' && Setting::get('layanan.mendesak_lewati_persetujuan', 'ya') !== 'tidak') {
            return 'Prioritas mendesak, langsung diteruskan ke tim GA';
        }

        $kepala = $this->calonPenyetuju();

        if ($kepala === null) {
            return 'Departemen pemohon belum punya kepala departemen';
        }

        if ((int) $kepala->getKey() === (int) $this->requester_employee_id) {
            return 'Pemohon adalah kepala departemennya sendiri';
        }

        return null;
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
        if (! in_array($this->status, ['diajukan', 'disetujui'], true)) {
            return false;
        }

        return $this->forceFill([
            'status' => 'dibatalkan',
            'closed_at' => now(),
        ])->save();
    }

    /**
     * Tim GA menerima tiket ini, dan satu perintah kerja korektif lahir untuk
     * mengerjakannya. Batas waktu dihitung sekali di sini dari target kategorinya,
     * bukan dihitung ulang saat dibaca, supaya mengubah target kategori besok tidak
     * menggeser janji yang sudah terlanjur dibuat pada tiket hari ini.
     */
    public function terima(array $penugasan = []): ?WorkOrder
    {
        if ($this->status !== 'disetujui') {
            return null;
        }

        $wo = WorkOrder::query()->create([
            'asset_id' => $this->asset_id,
            'service_request_id' => $this->getKey(),
            'type' => 'korektif',
            'priority' => $this->priority,
            'status' => 'dibuka',
            'reported_date' => ($this->submitted_at ?? now())->toDateString(),
            'reported_by_employee_id' => $this->requester_employee_id,
            'problem' => $this->title."\n\n".$this->description
                .($this->location ? "\n\nLokasi: ".$this->location->name : ''),
            'scheduled_date' => $penugasan['scheduled_date'] ?? null,
            'vendor_id' => $penugasan['vendor_id'] ?? null,
            'technician_employee_id' => $penugasan['technician_employee_id'] ?? null,
        ]);

        $jam = $this->category?->sla_hours;

        $this->forceFill([
            'status' => 'diterima',
            'accepted_by_user_id' => Auth::id(),
            'accepted_at' => now(),
            'sla_due_at' => $jam ? now()->addHours($jam) : null,
            'work_order_id' => $wo->getKey(),
        ])->save();

        return $wo;
    }

    /** Dipanggil dari WorkOrder saat pekerjaannya selesai atau dibatalkan. */
    public function ikutiPerintahKerja(WorkOrder $wo): void
    {
        if ($this->status !== 'diterima') {
            return;
        }

        if ($wo->isDone()) {
            $this->forceFill(['status' => 'selesai', 'closed_at' => now()])->save();

            return;
        }

        if ($wo->status === 'dibatalkan') {
            // Pekerjaannya tidak jadi dikerjakan, jadi tiketnya kembali menunggu tim GA,
            // bukan ikut mati. Yang melapor tetap punya masalah yang belum selesai.
            $this->forceFill([
                'status' => 'disetujui',
                'accepted_at' => null,
                'accepted_by_user_id' => null,
                'sla_due_at' => null,
                'work_order_id' => null,
            ])->save();
        }
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
            'diterima' => 'primary',
            'selesai' => 'success',
            'ditolak', 'dibatalkan' => 'gray',
            default => 'gray',
        };
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'mendesak' => 'danger',
            'tinggi' => 'warning',
            default => 'gray',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    /** Sudah lewat batas waktu dan belum selesai. */
    public function melewatiSla(): bool
    {
        return $this->status === 'diterima'
            && filled($this->sla_due_at)
            && $this->sla_due_at->isPast();
    }

    /**
     * Keterangan batas waktu sebagai kalimat. Tiket yang kategorinya belum punya target
     * mengatakannya apa adanya, bukan diam diam tampil seolah tepat waktu.
     */
    public function slaLabel(): string
    {
        if ($this->status !== 'diterima') {
            return match ($this->status) {
                'diajukan' => 'Belum dihitung, menunggu persetujuan',
                'disetujui' => 'Belum dihitung, menunggu diterima tim GA',
                'selesai' => 'Sudah selesai',
                default => 'Tidak berlaku',
            };
        }

        if (blank($this->sla_due_at)) {
            return 'Kategorinya belum punya target waktu';
        }

        $jam = (int) now()->diffInHours($this->sla_due_at, false);

        return $jam < 0
            ? 'Lewat '.abs($jam).' jam'
            : 'Sisa '.$jam.' jam';
    }

    /** Berapa lama tiket ini menunggu sebelum diterima tim GA. */
    public function lamaMenunggu(): string
    {
        $akhir = $this->accepted_at ?? ($this->closed_at ?? now());
        $jam = (int) ($this->submitted_at?->diffInHours($akhir, false) ?? 0);

        return $jam < 24 ? $jam.' jam' : intdiv($jam, 24).' hari';
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

    public function scopeMenungguGa(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeLewatSla(Builder $query): Builder
    {
        return $query->where('status', 'diterima')
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', Carbon::now());
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->title;
    }
}
