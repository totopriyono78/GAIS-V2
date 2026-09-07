<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Jadwal pemeliharaan preventif: satu pekerjaan yang berulang pada satu aset.
 *
 * Jadwal ini hanya menyimpan aturannya, yaitu pekerjaan apa, tiap berapa bulan, dan
 * biasanya oleh siapa. Yang menyimpan kejadiannya adalah MaintenanceVisit: satu baris per
 * jatuh tempo, yang ditutup dengan Sudah dikerjakan atau Dilewati. Pemisahan itu yang
 * membuat pertanyaan "servis kuartal lalu sudah dikerjakan vendor atau belum" bisa dijawab
 * dari riwayat, bukan disimpulkan dari ada tidaknya perintah kerja.
 *
 * Kolom last_done_date dan next_due_date di sini adalah ringkasan dari kunjungan, disimpan
 * supaya basis data bisa mengurutkan dan menyaring ribuan jadwal tanpa membuka riwayatnya
 * satu per satu. Keduanya tidak pernah diisi tangan dari luar model ini.
 */
class MaintenanceSchedule extends Model
{
    use Auditable;

    /** Interval yang sering dipakai, ditawarkan sebagai pilihan cepat. */
    public const COMMON_INTERVALS = [
        1 => 'Tiap bulan',
        3 => 'Tiap 3 bulan',
        6 => 'Tiap 6 bulan',
        12 => 'Tiap tahun',
        24 => 'Tiap 2 tahun',
    ];

    protected $fillable = [
        'asset_id',
        'name',
        'tasks',
        'interval_months',
        'last_done_date',
        'next_due_date',
        'vendor_id',
        'technician_employee_id',
        'estimated_cost',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'last_done_date' => 'date',
            'next_due_date' => 'date',
            'interval_months' => 'integer',
            'estimated_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        /*
         * Jatuh tempo dihitung di model, bukan di formulir, supaya jalur apa pun yang
         * menyentuh jadwal ini menghasilkan tanggal yang sama: layar, pembuatan massal,
         * penyelesaian perintah kerja, maupun perintah artisan.
         */
        static::saving(function (MaintenanceSchedule $jadwal) {
            if ($jadwal->isDirty(['interval_months', 'last_done_date']) || blank($jadwal->next_due_date)) {
                $jadwal->hitungUlangJatuhTempo();
            }
        });

        // Jadwal tanpa kunjungan terbuka adalah jadwal yang tidak akan pernah muncul di
        // daftar kerja siapa pun. Kunjungan pertama dibuat bersamaan dengan jadwalnya.
        static::created(function (MaintenanceSchedule $jadwal) {
            $jadwal->pastikanAdaKunjunganTerbuka();
        });

        /*
         * Mengubah interval atau tanggal terakhir dikerjakan menggeser jatuh tempo, dan
         * kunjungan yang masih terbuka harus ikut bergeser. Kunjungan yang sudah ditutup
         * tidak disentuh: tanggal jatuh tempo yang dulu berlaku adalah bagian dari
         * riwayat, dan riwayat tidak berubah karena aturannya hari ini berubah.
         */
        static::updated(function (MaintenanceSchedule $jadwal) {
            if (! $jadwal->wasChanged(['interval_months', 'last_done_date', 'next_due_date'])) {
                return;
            }

            $terbuka = $jadwal->kunjunganTerbuka();

            if ($terbuka && filled($jadwal->next_due_date)
                && ! $terbuka->due_date->equalTo($jadwal->next_due_date)) {
                $terbuka->forceFill(['due_date' => $jadwal->next_due_date])->save();
            }
        });
    }

    public function visits(): HasMany
    {
        return $this->hasMany(MaintenanceVisit::class)->orderByDesc('due_date');
    }

    /**
     * Kunjungan yang belum ditutup. Selalu tepat satu, kecuali jadwalnya baru dibuat.
     *
     * reorder() dipanggil karena relasi visits() sudah membawa urutan terbaru di atas,
     * dan menambahkan orderBy di belakangnya hanya akan menjadi urutan kedua. Kalau
     * suatu saat ada dua kunjungan terbuka karena data lama, yang diambil harus yang
     * paling tua, bukan yang paling baru.
     */
    public function kunjunganTerbuka(): ?MaintenanceVisit
    {
        return $this->visits()->reorder()->terbuka()->orderBy('due_date')->first();
    }

    /**
     * Membuat kunjungan terbuka kalau belum ada. Dipanggil saat jadwal dibuat dan setiap
     * kali satu kunjungan ditutup, jadi tidak pernah ada jadwal berjalan yang tidak punya
     * satu pun tanggal di depannya.
     */
    public function pastikanAdaKunjunganTerbuka(?Carbon $jatuhTempo = null): ?MaintenanceVisit
    {
        if (! $this->is_active) {
            return null;
        }

        if ($ada = $this->kunjunganTerbuka()) {
            return $ada;
        }

        $tanggal = $jatuhTempo
            ?? ($this->next_due_date ? $this->next_due_date->copy() : Carbon::today());

        return $this->visits()->create([
            'asset_id' => $this->asset_id,
            'sequence' => ((int) $this->visits()->max('sequence')) + 1,
            'due_date' => $tanggal->toDateString(),
            'status' => MaintenanceVisit::OPEN_STATUS,
            'vendor_id' => $this->vendor_id,
            'technician_employee_id' => $this->technician_employee_id,
        ]);
    }

    /**
     * Dipanggil setelah satu kunjungan ditutup: ringkasan jadwal diperbarui, lalu
     * kunjungan berikutnya dibuat.
     *
     * Titik hitung jatuh tempo berikutnya sengaja berbeda menurut cara kunjungan ini
     * ditutup. Kunjungan yang dikerjakan menghitung dari tanggal pengerjaannya, supaya
     * aset selalu mendapat satu interval penuh masa layanan meski vendor datang telat.
     * Kunjungan yang dilewati menghitung dari tanggal jatuh temponya, supaya siklusnya
     * tetap menempel di kalender dan tidak ikut mundur karena satu kali tidak dikerjakan.
     */
    public function lanjutkanSetelah(MaintenanceVisit $kunjungan): void
    {
        if ($kunjungan->isDone() && filled($kunjungan->completed_date)) {
            $this->forceFill(['last_done_date' => $kunjungan->completed_date])->save();
            $dasar = $kunjungan->completed_date->copy();
        } else {
            $dasar = $kunjungan->due_date->copy();
        }

        $berikutnya = $dasar->addMonthsNoOverflow($this->interval_months);

        $this->forceFill(['next_due_date' => $berikutnya->toDateString()])->save();

        $this->refresh()->pastikanAdaKunjunganTerbuka($berikutnya);
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

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class)->orderByDesc('reported_date');
    }

    /**
     * Jatuh tempo berikutnya = terakhir dikerjakan ditambah intervalnya. Jadwal yang
     * belum pernah dikerjakan jatuh tempo hari ini, bukan tidak pernah: pekerjaan yang
     * belum pernah dilakukan sama sekali justru yang paling perlu muncul di daftar.
     */
    public function hitungUlangJatuhTempo(): void
    {
        if ($this->interval_months <= 0) {
            $this->next_due_date = null;

            return;
        }

        $this->next_due_date = $this->last_done_date
            ? $this->last_done_date->copy()->addMonthsNoOverflow($this->interval_months)
            : Carbon::today();
    }

    /**
     * Dipanggil saat perintah kerja preventif diselesaikan. Tanggal terakhir dikerjakan
     * hanya maju, tidak pernah mundur, supaya perintah kerja lama yang baru diselesaikan
     * belakangan tidak menarik jadwalnya kembali ke masa lalu.
     */
    public function catatSelesai(Carbon|string $tanggal): bool
    {
        $tanggal = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);

        if ($this->last_done_date && $this->last_done_date->gte($tanggal)) {
            return false;
        }

        $this->last_done_date = $tanggal;
        $this->hitungUlangJatuhTempo();

        return $this->save();
    }

    public function intervalLabel(): string
    {
        return self::COMMON_INTERVALS[$this->interval_months]
            ?? 'Tiap '.$this->interval_months.' bulan';
    }

    /** Sisa hari sampai jatuh tempo. Negatif berarti sudah lewat. */
    public function sisaHari(): ?int
    {
        if (blank($this->next_due_date)) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->next_due_date, false);
    }

    /**
     * Keadaan jadwal sebagai satu kata, dipakai untuk badan dan penyaring. Dihitung dari
     * tanggal, bukan disimpan, karena keadaannya berubah sendiri seiring hari berjalan.
     */
    public function keadaan(): string
    {
        if (! $this->is_active) {
            return 'nonaktif';
        }

        $sisa = $this->sisaHari();

        return match (true) {
            $sisa === null => 'belum_dijadwalkan',
            $sisa < 0 => 'lewat',
            $sisa <= 30 => 'segera',
            default => 'aman',
        };
    }

    public function keadaanLabel(): string
    {
        $sisa = $this->sisaHari();

        return match ($this->keadaan()) {
            'nonaktif' => 'Tidak dipakai',
            'belum_dijadwalkan' => 'Belum dijadwalkan',
            'lewat' => 'Lewat '.abs((int) $sisa).' hari',
            'segera' => $sisa === 0 ? 'Jatuh tempo hari ini' : 'Tinggal '.$sisa.' hari',
            default => 'Aman',
        };
    }

    public function keadaanColor(): string
    {
        return match ($this->keadaan()) {
            'lewat' => 'danger',
            'segera' => 'warning',
            'aman' => 'success',
            default => 'gray',
        };
    }

    /** Ada perintah kerja yang belum selesai untuk jadwal ini. */
    public function sedangDikerjakan(): bool
    {
        return $this->workOrders()
            ->whereIn('status', ['dibuka', 'dikerjakan'])
            ->exists();
    }

    /** Berapa kunjungan yang benar benar dikerjakan, dipakai di layar riwayat. */
    public function jumlahDikerjakan(): int
    {
        return $this->visits()->where('status', 'dikerjakan')->count();
    }

    public function jumlahDilewati(): int
    {
        return $this->visits()->where('status', 'dilewati')->count();
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeJatuhTempo(Builder $query, int $dalamHari = 0): Builder
    {
        return $query->aktif()
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', Carbon::today()->addDays($dalamHari));
    }

    public function scopeTerlewat(Builder $query): Builder
    {
        return $query->aktif()
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<', Carbon::today());
    }

    public function getAuditLabel(): string
    {
        return $this->name.' ('.($this->asset?->code ?? '').')';
    }
}
