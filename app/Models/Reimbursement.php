<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Penggantian biaya karyawan.
 *
 * Alurnya empat langkah, dan tiap langkah dikerjakan orang yang berbeda:
 *
 *   Karyawan mengumpulkan struk  ->  Atasan menyetujui  ->  Tim GA memeriksa  ->  Dibayar
 *
 * Dua persetujuan, sesuai keputusan pemilik proyek pada 7 September 2026, dan tanpa batas
 * nominal: pengajuan Rp 50.000 melewati jalur yang sama dengan pengajuan Rp 5.000.000.
 * Keduanya menjawab pertanyaan yang berbeda. Atasan menjawab "benar ini keperluan kerja",
 * dan tim GA menjawab "struknya ada dan angkanya cocok". Menggabungkan keduanya berarti
 * salah satu pertanyaan itu tidak pernah ditanyakan.
 *
 * Langkah kedua bisa dilewati, sama seperti permintaan perbaikan, dan alasannya ditulis ke
 * kolomnya sendiri supaya lompatan itu terbaca di layar: departemen yang belum punya kepala,
 * dan pemohon yang justru kepala departemen itu sendiri. Tanpa jalan keluar itu, pengajuan
 * kepala departemen akan tersangkut selamanya menunggu tanda tangannya sendiri.
 *
 * Nilainya tidak pernah disimpan, melainkan dijumlahkan dari struknya.
 */
class Reimbursement extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'reimbursement';

    public const STATUSES = [
        'draft' => 'Draf',
        'diajukan' => 'Menunggu atasan',
        'diperiksa' => 'Menunggu tim GA',
        'disetujui' => 'Menunggu pembayaran',
        'dibayar' => 'Sudah diganti',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang nilainya ikut dihitung sebagai realisasi anggaran. */
    public const COUNTED_STATUSES = ['disetujui', 'dibayar'];

    /** Status yang nilainya sudah dikeluarkan karyawan tetapi belum disetujui. */
    public const PENDING_STATUSES = ['draft', 'diajukan', 'diperiksa'];

    /** Status yang berarti pengajuan masih menunggu sesuatu terjadi. */
    public const OPEN_STATUSES = ['draft', 'diajukan', 'diperiksa', 'disetujui'];

    protected $fillable = [
        'code',
        'employee_id',
        'department_id',
        'title',
        'notes',
        'status',
        'submitted_at',
        'approver_employee_id',
        'approved_at',
        'approval_note',
        'approval_skipped_reason',
        'verified_by_user_id',
        'verified_at',
        'rejection_reason',
        'rejected_stage',
        'paid_date',
        'payment_reference',
        'paid_by_user_id',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'verified_at' => 'datetime',
            'paid_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Reimbursement $pengajuan) {
            if (blank($pengajuan->code)) {
                $pengajuan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($pengajuan->created_by_user_id)) {
                $pengajuan->created_by_user_id = Auth::id();
            }

            // Departemen dibekukan di pengajuan, bukan dibaca dari karyawannya nanti.
            if (! array_key_exists('department_id', $pengajuan->getAttributes())) {
                $pengajuan->department_id = $pengajuan->employee?->department_id;
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ReimbursementLine::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    // ---------------------------------------------------------------- nilai

    public function total(): float
    {
        return round((float) $this->lines()->sum('amount'), 2);
    }

    public function totalLabel(): string
    {
        return $this->lines()->exists()
            ? Rupiah::penuh($this->total())
            : 'Belum ada struk';
    }

    /** Tahun anggaran yang dibebani, mengikuti tanggal struk paling awal. */
    public function tahunAnggaran(): ?int
    {
        $tanggal = $this->lines()->min('expense_date');

        return $tanggal ? (int) substr((string) $tanggal, 0, 4) : null;
    }

    /**
     * Keterangan rentang tanggal struk. Pengajuan yang strukya menyeberang tahun disebut
     * apa adanya, karena pembebanannya memang jatuh ke dua tahun anggaran yang berbeda.
     */
    public function rentangLabel(): string
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->get();

        if ($baris->isEmpty()) {
            return 'Belum ada struk';
        }

        $awal = $baris->min('expense_date');
        $akhir = $baris->max('expense_date');

        return $awal->equalTo($akhir)
            ? $awal->translatedFormat('d M Y')
            : $awal->translatedFormat('d M Y').' sampai '.$akhir->translatedFormat('d M Y');
    }

    public function menyeberangTahun(): bool
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->get();

        return $baris->isNotEmpty()
            && $baris->min('expense_date')->year !== $baris->max('expense_date')->year;
    }

    // ---------------------------------------------------------------- alur

    /** Siapa yang seharusnya menyetujui: kepala departemen pemohon. */
    public function calonPenyetuju(): ?Employee
    {
        return $this->department?->head
            ?? Department::find($this->department_id)?->head;
    }

    /** Alasan pengajuan ini tidak perlu persetujuan atasan, atau null kalau memang perlu. */
    public function alasanLewatPersetujuan(): ?string
    {
        if (blank($this->department_id)) {
            return 'Pengajuan ini tidak dibebankan ke departemen mana pun';
        }

        $kepala = $this->calonPenyetuju();

        if ($kepala === null) {
            return 'Departemen yang dibebani belum punya kepala departemen';
        }

        if ((int) $kepala->getKey() === (int) $this->employee_id) {
            return 'Pemohon adalah kepala departemennya sendiri';
        }

        return null;
    }

    /**
     * Pengajuan tanpa struk tidak bisa diajukan, dan struk tanpa bukti diperingatkan tetapi
     * tidak diblokir. Kadang struknya memang hilang, dan menutup jalannya berarti memaksa
     * orang mengarang berkas supaya bisa lanjut.
     */
    public function alasanBelumBisaDiajukan(): ?string
    {
        if ($this->status !== 'draft') {
            return 'Pengajuan ini sudah tidak berstatus draf.';
        }

        if (! $this->lines()->exists()) {
            return 'Belum ada struk yang dicatat. Tambahkan minimal satu baris supaya ada nilai yang disetujui.';
        }

        if ($this->total() <= 0) {
            return 'Jumlah seluruh struknya nol. Periksa lagi nilai tiap barisnya.';
        }

        return null;
    }

    /** Berapa struk yang belum punya foto bukti. */
    public function strukTanpaBukti(): int
    {
        return $this->lines()->whereNull('file_path')->count();
    }

    public function ajukan(): bool
    {
        if ($this->alasanBelumBisaDiajukan() !== null) {
            return false;
        }

        $alasan = $this->alasanLewatPersetujuan();

        return $this->forceFill([
            'status' => $alasan === null ? 'diajukan' : 'diperiksa',
            'submitted_at' => now(),
            'approver_employee_id' => $alasan === null ? $this->calonPenyetuju()?->getKey() : null,
            'approved_at' => $alasan === null ? null : now(),
            'approval_skipped_reason' => $alasan,
            'rejection_reason' => null,
            'rejected_stage' => null,
        ])->save();
    }

    public function setujuiAtasan(?string $catatan = null): bool
    {
        if ($this->status !== 'diajukan') {
            return false;
        }

        return $this->forceFill([
            'status' => 'diperiksa',
            'approved_at' => now(),
            'approval_note' => $catatan,
        ])->save();
    }

    public function verifikasi(): bool
    {
        if ($this->status !== 'diperiksa') {
            return false;
        }

        return $this->forceFill([
            'status' => 'disetujui',
            'verified_by_user_id' => Auth::id(),
            'verified_at' => now(),
        ])->save();
    }

    public function tolak(string $alasan, string $tahap): bool
    {
        if (! in_array($this->status, ['diajukan', 'diperiksa'], true)) {
            return false;
        }

        return $this->forceFill([
            'status' => 'ditolak',
            'rejection_reason' => $alasan,
            'rejected_stage' => $tahap,
        ])->save();
    }

    /**
     * Mengembalikan pengajuan yang ditolak ke draf.
     *
     * Alasan penolakannya sengaja tidak dihapus. Yang memperbaiki perlu membacanya sambil
     * mengubah struknya, dan tanpa jalan kembali ini orang akan membuat pengajuan kedua
     * untuk struk yang sama, yang berarti satu biaya terhitung dua kali.
     */
    public function kembalikanKeDraft(): bool
    {
        if ($this->status !== 'ditolak') {
            return false;
        }

        return $this->forceFill([
            'status' => 'draft',
            'submitted_at' => null,
            'approved_at' => null,
            'approval_note' => null,
            'approval_skipped_reason' => null,
        ])->save();
    }

    public function tandaiDibayar(string $tanggal, ?string $referensi = null): bool
    {
        if ($this->status !== 'disetujui') {
            return false;
        }

        return $this->forceFill([
            'status' => 'dibayar',
            'paid_date' => $tanggal,
            'payment_reference' => $referensi,
            'paid_by_user_id' => Auth::id(),
        ])->save();
    }

    public function batalkan(): bool
    {
        if (! in_array($this->status, self::OPEN_STATUSES, true)) {
            return false;
        }

        return $this->forceFill(['status' => 'dibatalkan'])->save();
    }

    /** Struk hanya boleh diubah selama pengajuan masih draf. */
    public function rincianBisaDiubah(): bool
    {
        return $this->status === 'draft';
    }

    // ---------------------------------------------------------------- tampilan

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'diajukan' => 'warning',
            'diperiksa' => 'warning',
            'disetujui' => 'info',
            'dibayar' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    /** Berapa lama pengajuan ini sudah menunggu sejak diajukan. */
    public function lamaMenunggu(): string
    {
        if (blank($this->submitted_at) || ! $this->isOpen()) {
            return 'Tidak sedang menunggu';
        }

        $hari = (int) $this->submitted_at->diffInDays(now());

        return match (true) {
            $hari < 1 => 'Diajukan hari ini',
            $hari === 1 => 'Menunggu 1 hari',
            default => 'Menunggu '.$hari.' hari',
        };
    }

    public function tahapLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Masih disusun pemohon',
            'diajukan' => 'Di meja '.($this->approver?->full_name ?? 'kepala departemen'),
            'diperiksa' => 'Di meja tim GA',
            'disetujui' => 'Menunggu ditransfer',
            'dibayar' => 'Selesai',
            'ditolak' => 'Ditolak '.($this->rejected_stage === 'ga' ? 'tim GA' : 'atasan'),
            default => 'Dibatalkan',
        };
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeTerbuka(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::OPEN_STATUSES);
    }

    public function scopeMenungguAtasan(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'diajukan');
    }

    public function scopeMenungguGa(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'diperiksa');
    }

    public function scopeMenungguPembayaran(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'disetujui');
    }

    /**
     * Kolom status disebut lengkap dengan nama tabelnya, karena penyaring ini akan dipakai
     * laporan yang menggabungkan tabel lain yang juga punya kolom status, dan PostgreSQL
     * menolak pertanyaan yang ambigu dengan galat yang jauh dari sebabnya.
     */
    public function scopeTerhitung(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::COUNTED_STATUSES);
    }

    public function scopeBelumDisetujui(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::PENDING_STATUSES);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->title;
    }
}
