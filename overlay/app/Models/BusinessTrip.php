<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Satu perjalanan dinas.
 *
 * Satu surat bisa memberangkatkan beberapa orang, tetapi hanya satu orang yang bertanggung
 * jawab atas uangnya. Orang itu adalah `employee_id`, dan seluruh alur uang menempel padanya:
 * departemen yang dibebani dibekukan dari kartu karyawannya, atasan yang menyetujui dibaca
 * dari departemen itu, uang mukanya diserahkan kepadanya, dan namanya yang disebut saat sisa
 * uang muka dikembalikan. Yang lain tercatat di `participants` sebagai orang yang ikut
 * berangkat, tanpa membawa kewajiban uang apa pun.
 *
 * Tiga angka uang hidup di sini dan ketiganya berbeda arti:
 *
 * - `estimated_cost`: perkiraan saat mengajukan, dipakai atasan memutuskan.
 * - `advance_amount`: uang muka yang benar benar dibayarkan sebelum berangkat.
 * - `totalRealisasi()`: jumlah baris pertanggungjawaban, yaitu uang yang benar benar keluar.
 *
 * Yang ketiga tidak pernah disimpan sebagai kolom. Selisihnya terhadap uang muka juga tidak,
 * dan itu yang membuat kalimat kurang bayar atau sisa kembali tidak pernah bisa basi.
 */
class BusinessTrip extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'business_trip';

    public const STATUSES = [
        'diajukan' => 'Menunggu persetujuan',
        'disetujui' => 'Disetujui',
        'dipertanggungjawabkan' => 'Menunggu diperiksa',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    public const TRANSPORT_MODES = [
        'darat_dinas' => 'Kendaraan dinas',
        'darat_umum' => 'Kendaraan umum atau sewa',
        'kereta' => 'Kereta api',
        'pesawat' => 'Pesawat',
        'laut' => 'Kapal laut',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'code',
        'employee_id',
        'department_id',
        'destination',
        'purpose',
        'start_date',
        'end_date',
        'transport_mode',
        'estimated_cost',
        'status',
        'submitted_at',
        'approver_employee_id',
        'approved_at',
        'approval_note',
        'approval_skipped_reason',
        'rejection_reason',
        'advance_amount',
        'advance_paid_at',
        'advance_paid_by_user_id',
        'advance_note',
        'reported_at',
        'settled_at',
        'settled_by_user_id',
        'settlement_note',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'estimated_cost' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'advance_paid_at' => 'datetime',
            'reported_at' => 'datetime',
            'settled_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BusinessTrip $perjalanan) {
            if (blank($perjalanan->code)) {
                $perjalanan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($perjalanan->created_by_user_id)) {
                $perjalanan->created_by_user_id = Auth::id();
            }

            if (blank($perjalanan->submitted_at)) {
                $perjalanan->submitted_at = now();
            }

            // Departemen dibekukan di pengajuan, bukan dibaca dari karyawannya nanti.
            if (blank($perjalanan->department_id)) {
                $perjalanan->department_id = $perjalanan->employee?->department_id;
            }

            $perjalanan->tentukanJalurPersetujuan();
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    /**
     * Orang lain yang ikut berangkat.
     *
     * Penanggung jawab tidak wajib ada di sini. Ia sudah tercatat sebagai `employee_id`, dan
     * daftar rombongan yang dibaca layar selalu lewat semuaYangBerangkat(), yang menaruhnya
     * di depan lalu membuang nama kembar. Jadi kalaupun namanya ikut terpilih di formulir,
     * jumlah rombongan maupun daftarnya tetap benar.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'business_trip_participants')
            ->withTimestamps()
            ->orderBy('employees.full_name');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(BusinessTripExpense::class);
    }

    public function advancePaidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advance_paid_by_user_id');
    }

    public function settledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by_user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- persetujuan

    /**
     * Siapa yang seharusnya menyetujui: kepala departemen pemohon.
     *
     * Sistem ini tidak menyimpan atasan per karyawan, tetapi departemen sudah punya kepalanya.
     * Memakai yang sudah ada lebih jujur daripada mengarang hierarki baru yang harus diisi
     * seluruhnya sebelum modul ini bisa dipakai. Aturan yang sama dipakai sejak kiriman G.
     */
    public function calonPenyetuju(): ?Employee
    {
        return $this->department?->head ?? Department::find($this->department_id)?->head;
    }

    public function alasanLewatPersetujuan(): ?string
    {
        $kepala = $this->calonPenyetuju();

        if ($kepala === null) {
            return 'Departemen '.($this->department?->name ?? 'pemohon').' belum punya kepala departemen, jadi tidak ada yang bisa menyetujui.';
        }

        if ($kepala->getKey() === $this->employee_id) {
            return 'Pemohon adalah kepala departemennya sendiri, jadi tidak ada atasan yang menyetujui di dalam departemen.';
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
        $this->approval_skipped_reason = $alasan;
        $this->approved_at = now();
    }

    // ---------------------------------------------------------------- keadaan

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'diajukan' => 'warning',
            'disetujui' => 'info',
            'dipertanggungjawabkan' => 'warning',
            'selesai' => 'success',
            'ditolak', 'dibatalkan' => 'danger',
            default => 'gray',
        };
    }

    public function transportLabel(): string
    {
        return self::TRANSPORT_MODES[$this->transport_mode] ?? $this->transport_mode;
    }

    public function isDiajukan(): bool
    {
        return $this->status === 'diajukan';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDipertanggungjawabkan(): bool
    {
        return $this->status === 'dipertanggungjawabkan';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    /** Masih bisa disentuh, yaitu belum ditutup dan belum dibatalkan. */
    public function masihBerjalan(): bool
    {
        return in_array($this->status, ['diajukan', 'disetujui', 'dipertanggungjawabkan'], true);
    }

    public function uangMukaSudahDibayar(): bool
    {
        return $this->advance_paid_at !== null;
    }

    /** Rincian pertanggungjawaban masih boleh diubah. */
    public function rincianBisaDiubah(): bool
    {
        return in_array($this->status, ['disetujui', 'dipertanggungjawabkan'], true);
    }

    public function lamaHari(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function periodeLabel(): string
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d M Y').', sehari';
        }

        return $this->start_date->format('d M Y').' sampai '.$this->end_date->format('d M Y')
            .', '.$this->lamaHari().' hari';
    }

    // ---------------------------------------------------------------- rombongan

    /**
     * Seluruh orang yang berangkat, penanggung jawabnya lebih dulu.
     *
     * @return Collection<int, Employee>
     */
    public function semuaYangBerangkat(): Collection
    {
        $rombongan = collect();

        if ($this->employee !== null) {
            $rombongan->push($this->employee);
        }

        foreach ($this->participants as $peserta) {
            if ($peserta->getKey() !== $this->employee_id) {
                $rombongan->push($peserta);
            }
        }

        return $rombongan;
    }

    public function jumlahBerangkat(): int
    {
        return $this->semuaYangBerangkat()->count();
    }

    /** Satu baris untuk kolom tabel dan kotak dialog. */
    public function rombonganRingkas(): string
    {
        $jumlah = $this->jumlahBerangkat();

        return $jumlah <= 1
            ? 'Berangkat sendiri'
            : 'Berangkat '.$jumlah.' orang';
    }

    /**
     * Kalimat rombongan yang menyebut namanya satu per satu.
     *
     * Angka saja tidak cukup. Yang memeriksa struk perlu tahu nama siapa saja yang boleh
     * muncul di dalamnya, karena tiket atas nama orang yang tidak ikut berangkat adalah
     * hal pertama yang ditanyakan pemeriksa.
     */
    public function rombonganKalimat(): string
    {
        $rombongan = $this->semuaYangBerangkat();

        if ($rombongan->isEmpty()) {
            return 'Belum ada yang tercatat berangkat.';
        }

        $penanggungJawab = $this->employee?->full_name ?? 'Penanggung jawab';

        if ($rombongan->count() === 1) {
            return $penanggungJawab.' berangkat sendiri, sekaligus sebagai penanggung jawab biayanya.';
        }

        $lain = $rombongan->slice(1)
            ->map(fn (Employee $karyawan): string => $karyawan->full_name)
            ->all();

        return 'Berangkat '.$rombongan->count().' orang. '.$penanggungJawab
            .' sebagai penanggung jawab biaya dan pertanggungjawabannya, bersama '
            .implode(', ', $lain).'.';
    }

    /**
     * Ada peserta dari departemen lain.
     *
     * Perlu disebut di layar, karena seluruh biayanya tetap dibebankan ke departemen
     * penanggung jawab, bukan dibagi mengikuti departemen masing masing peserta.
     */
    public function adaPesertaLuarDepartemen(): bool
    {
        return $this->semuaYangBerangkat()
            ->contains(fn (Employee $karyawan): bool => $karyawan->department_id !== $this->department_id);
    }

    // ---------------------------------------------------------------- angka

    public function totalRealisasi(): float
    {
        return round((float) $this->expenses()->sum('amount'), 2);
    }

    public function uangMuka(): float
    {
        return round((float) $this->advance_amount, 2);
    }

    /**
     * Selisih uang muka terhadap realisasi.
     *
     * Positif berarti uang mukanya berlebih dan sisanya harus dikembalikan karyawan. Negatif
     * berarti karyawan menalangi kekurangannya dan perusahaan masih berutang kepadanya.
     */
    public function selisih(): float
    {
        return round($this->uangMuka() - $this->totalRealisasi(), 2);
    }

    public function realisasiLabel(): string
    {
        return $this->expenses()->exists()
            ? Rupiah::penuh($this->totalRealisasi())
            : 'Belum ada rincian';
    }

    public function uangMukaLabel(): string
    {
        return $this->advance_amount === null
            ? 'Tanpa uang muka'
            : Rupiah::penuh($this->uangMuka());
    }

    /**
     * Kalimat penyelesaian: kurang bayar, sisa dikembalikan, atau pas.
     *
     * Kalimatnya menyebut siapa berutang kepada siapa, bukan sekadar angka bertanda. Angka
     * bertanda memaksa pembacanya mengingat mana yang positif, dan setengah dari mereka akan
     * salah mengingatnya persis saat uangnya harus berpindah.
     */
    public function selisihKalimat(): string
    {
        if (! $this->expenses()->exists()) {
            return 'Belum ada rincian pertanggungjawaban, jadi belum ada yang bisa dibandingkan dengan uang mukanya.';
        }

        if ($this->advance_amount === null) {
            return 'Tanpa uang muka. Seluruh '.Rupiah::penuh($this->totalRealisasi())
                .' ditalangi '.($this->employee?->full_name ?? 'karyawan').' dan perlu diganti perusahaan.';
        }

        $selisih = $this->selisih();

        if (abs($selisih) < 0.01) {
            return 'Uang mukanya pas, tidak ada kurang bayar maupun sisa yang dikembalikan.';
        }

        return $selisih > 0
            ? 'Sisa uang muka '.Rupiah::penuh($selisih).' perlu dikembalikan '
                .($this->employee?->full_name ?? 'karyawan').' ke perusahaan.'
            : 'Kurang bayar '.Rupiah::penuh(abs($selisih)).'. Perusahaan masih perlu mengganti '
                .($this->employee?->full_name ?? 'karyawan').' sebesar itu.';
    }

    public function selisihColor(): ?string
    {
        if (! $this->expenses()->exists()) {
            return 'gray';
        }

        if ($this->advance_amount === null) {
            return 'warning';
        }

        return abs($this->selisih()) < 0.01 ? 'success' : 'warning';
    }

    /** Ringkasan satu baris untuk kolom tabel. */
    public function selisihRingkas(): string
    {
        if (! $this->expenses()->exists()) {
            return 'Belum ada rincian';
        }

        if ($this->advance_amount === null) {
            return 'Tanpa uang muka';
        }

        $selisih = $this->selisih();

        return match (true) {
            abs($selisih) < 0.01 => 'Pas',
            $selisih > 0 => 'Sisa '.Rupiah::penuh($selisih),
            default => 'Kurang '.Rupiah::penuh(abs($selisih)),
        };
    }

    // ---------------------------------------------------------------- alur

    public function alasanBelumBisaDipertanggungjawabkan(): ?string
    {
        if (! $this->isDisetujui()) {
            return 'Perjalanan ini tidak sedang menunggu pertanggungjawaban.';
        }

        if (! $this->expenses()->exists()) {
            return 'Belum ada satu pun rincian biaya. Tambahkan rinciannya lebih dulu di daftar di bawah, karena pertanggungjawaban tanpa rincian tidak bisa diperiksa siapa pun.';
        }

        return null;
    }

    public function scopeMenungguPersetujuan(Builder $query): Builder
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeMenungguUangMuka(Builder $query): Builder
    {
        return $query->where('status', 'disetujui')
            ->whereNotNull('advance_amount')
            ->whereNull('advance_paid_at');
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.($this->employee?->full_name ?? 'karyawan').' ke '.$this->destination;
    }
}
