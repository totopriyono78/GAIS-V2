<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Permintaan pemakaian ATK.
 *
 * Alurnya tiga langkah dan tiga orang:
 *
 *   Karyawan meminta  ->  Atasan menyetujui  ->  Tim GA menyerahkan barangnya
 *
 * Langkah ketiga berbeda sifatnya dari alur mana pun yang sudah ada di aplikasi ini. Pada
 * penggantian biaya dan tagihan rekanan, langkah terakhir hanya menandai bahwa uangnya sudah
 * berpindah. Di sini langkah terakhir benar benar mengubah angka lain: stok berkurang, dan
 * sejak kiriman J stok yang berkurang itu langsung menjadi realisasi anggaran ATK departemen
 * pemohon.
 *
 * Karena itu penyerahan dikerjakan di dalam satu transaksi basis data, dan seluruh baris
 * diperiksa stoknya lebih dulu sebelum satu pun mutasi disimpan. Penyerahan yang berhenti di
 * tengah akan meninggalkan tiga barang keluar dari lima, dengan permintaan yang tetap
 * berstatus menunggu, dan tidak ada layar yang bisa menjelaskan keadaan itu kepada orang.
 *
 * Persetujuan atasan bisa dilewati, sama seperti permintaan perbaikan dan penggantian biaya,
 * dan alasannya ditulis ke kolomnya sendiri supaya lompatan itu terbaca di layar. Hanya ada
 * dua keadaan di sini, bukan tiga, karena departemen wajib diisi pada permintaan barang.
 */
class SupplyRequest extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'supply_request';

    public const STATUSES = [
        'draft' => 'Draf',
        'diajukan' => 'Menunggu atasan',
        'disetujui' => 'Menunggu diserahkan',
        'diserahkan' => 'Sudah diserahkan',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang berarti permintaan masih menunggu sesuatu terjadi. */
    public const OPEN_STATUSES = ['draft', 'diajukan', 'disetujui'];

    protected $fillable = [
        'code',
        'employee_id',
        'department_id',
        'purpose',
        'needed_date',
        'notes',
        'status',
        'submitted_at',
        'approver_employee_id',
        'approved_at',
        'approval_note',
        'approval_skipped_reason',
        'issued_by_user_id',
        'issued_at',
        'issue_note',
        'rejection_reason',
        'rejected_stage',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'needed_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'issued_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupplyRequest $permintaan) {
            if (blank($permintaan->code)) {
                $permintaan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($permintaan->created_by_user_id)) {
                $permintaan->created_by_user_id = Auth::id();
            }

            // Departemen dibekukan di permintaan, bukan dibaca dari karyawannya nanti.
            if (! array_key_exists('department_id', $permintaan->getAttributes())) {
                $permintaan->department_id = $permintaan->employee?->department_id;
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
        return $this->hasMany(SupplyRequestLine::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }

    public function issuedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    // ---------------------------------------------------------------- jumlah

    public function totalDiminta(): int
    {
        return (int) $this->lines()->sum('quantity_requested');
    }

    public function totalDiserahkan(): int
    {
        return (int) $this->lines()->sum('quantity_issued');
    }

    /**
     * Keterangan jumlah untuk dibaca orang.
     *
     * Sengaja menyebut jumlah jenis barang, bukan jumlah satuannya, karena satuannya
     * bercampur. Sepuluh pulpen ditambah dua rim kertas bukan dua belas apa pun, dan
     * menjumlahkannya menjadi satu angka hanya melahirkan angka yang tidak berarti.
     */
    public function jenisLabel(): string
    {
        $jumlah = $this->relationLoaded('lines')
            ? $this->lines->count()
            : $this->lines()->count();

        return $jumlah === 0 ? 'Belum ada barang' : $jumlah.' jenis barang';
    }

    /** Berapa baris yang diserahkan kurang dari yang diminta. */
    public function barisKurang(): int
    {
        return $this->lines()->whereColumn('quantity_issued', '<', 'quantity_requested')->count();
    }

    // ---------------------------------------------------------------- alur

    /** Siapa yang seharusnya menyetujui: kepala departemen yang dibebani. */
    public function calonPenyetuju(): ?Employee
    {
        return $this->department?->head
            ?? Department::find($this->department_id)?->head;
    }

    /**
     * Alasan permintaan ini tidak perlu persetujuan atasan, atau null kalau memang perlu.
     *
     * Hanya dua keadaan, berbeda dari penggantian biaya yang punya tiga, karena departemen
     * wajib diisi pada permintaan barang sehingga keadaan "tidak dibebankan ke departemen
     * mana pun" tidak pernah bisa terjadi di sini.
     */
    public function alasanLewatPersetujuan(): ?string
    {
        $kepala = $this->calonPenyetuju();

        if ($kepala === null) {
            return 'Departemen yang dibebani belum punya kepala departemen';
        }

        if ((int) $kepala->getKey() === (int) $this->employee_id) {
            return 'Pemohon adalah kepala departemennya sendiri';
        }

        return null;
    }

    public function alasanBelumBisaDiajukan(): ?string
    {
        if ($this->status !== 'draft') {
            return 'Permintaan ini sudah tidak berstatus draf.';
        }

        if (! $this->lines()->exists()) {
            return 'Belum ada barang yang diminta. Tambahkan minimal satu baris supaya ada yang bisa disetujui.';
        }

        if ($this->totalDiminta() <= 0) {
            return 'Seluruh barisnya berjumlah nol. Periksa lagi jumlah tiap barangnya.';
        }

        return null;
    }

    public function ajukan(): bool
    {
        if ($this->alasanBelumBisaDiajukan() !== null) {
            return false;
        }

        $alasan = $this->alasanLewatPersetujuan();

        return $this->forceFill([
            'status' => $alasan === null ? 'diajukan' : 'disetujui',
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
            'status' => 'disetujui',
            'approved_at' => now(),
            'approval_note' => $catatan,
        ])->save();
    }

    /**
     * Barang mana saja yang stoknya tidak cukup untuk diserahkan sekarang.
     *
     * Dipakai di dua tempat dengan maksud yang berbeda. Saat mengajukan, hasilnya hanya
     * diberitahukan supaya pemohon tahu barangnya mungkin belum ada, dan tidak memblokir
     * apa pun. Saat menyerahkan, hasilnya memblokir, karena mutasi yang membuat stok minus
     * memang ditolak model mutasinya sendiri.
     *
     * @return array<int, string> kalimat siap baca, satu per barang yang bermasalah
     */
    public function kekuranganStok(): array
    {
        $masalah = [];

        foreach ($this->lines()->with('item')->get() as $baris) {
            $item = $baris->item;

            if ($item === null) {
                continue;
            }

            $diminta = $baris->jumlahUntukDiserahkan();

            if ($diminta <= 0) {
                continue;
            }

            $tersedia = $item->currentStock();

            if ($diminta > $tersedia) {
                $masalah[] = $item->name.' diminta '.$item->formatQuantity($diminta)
                    .', stok tinggal '.$item->formatQuantity(max($tersedia, 0));
            }
        }

        return $masalah;
    }

    /**
     * Menyerahkan barangnya. Inilah satu satunya tempat stok berkurang karena permintaan.
     *
     * Seluruhnya dibungkus satu transaksi basis data. Kalau baris kelima gagal, empat mutasi
     * sebelumnya ikut dibatalkan, dan stok kembali seperti sebelum tombol ditekan. Tanpa itu,
     * kegagalan di tengah meninggalkan gudang yang catatannya tidak sama dengan isinya, dan
     * tidak ada layar yang bisa menjelaskan keadaan itu kepada orang.
     *
     * Baris yang jumlah serahnya nol tidak melahirkan mutasi apa pun. Ia tetap tersimpan
     * sebagai catatan permintaan yang tidak terpenuhi.
     */
    public function serahkan(?string $catatan = null): bool
    {
        if ($this->status !== 'disetujui') {
            return false;
        }

        if ($this->kekuranganStok() !== []) {
            return false;
        }

        DB::transaction(function () use ($catatan): void {
            foreach ($this->lines()->with('item')->get() as $baris) {
                $jumlah = $baris->jumlahUntukDiserahkan();

                if ($jumlah <= 0) {
                    $baris->forceFill(['quantity_issued' => 0])->save();

                    continue;
                }

                $mutasi = SupplyTransaction::query()->create([
                    'supply_item_id' => $baris->supply_item_id,
                    'type' => 'keluar',
                    'quantity' => $jumlah,
                    'transaction_date' => now()->toDateString(),
                    'department_id' => $this->department_id,
                    'employee_id' => $this->employee_id,
                    'reference' => $this->code,
                    'notes' => 'Penyerahan permintaan '.$this->code.'. '.$this->purpose,
                ]);

                $baris->forceFill([
                    'quantity_issued' => $jumlah,
                    'supply_transaction_id' => $mutasi->getKey(),
                ])->save();
            }

            $this->forceFill([
                'status' => 'diserahkan',
                'issued_by_user_id' => Auth::id(),
                'issued_at' => now(),
                'issue_note' => $catatan,
            ])->save();
        });

        return true;
    }

    public function tolak(string $alasan, string $tahap): bool
    {
        if (! in_array($this->status, ['diajukan', 'disetujui'], true)) {
            return false;
        }

        return $this->forceFill([
            'status' => 'ditolak',
            'rejection_reason' => $alasan,
            'rejected_stage' => $tahap,
        ])->save();
    }

    /**
     * Mengembalikan permintaan yang ditolak ke draf.
     *
     * Alasan penolakannya sengaja tidak dihapus, sama seperti pada penggantian biaya. Yang
     * memperbaiki perlu membacanya sambil mengubah daftarnya, dan tanpa jalan kembali ini
     * orang akan membuat permintaan kedua untuk barang yang sama.
     */
    public function kembalikanKeDraft(): bool
    {
        if ($this->status !== 'ditolak') {
            return false;
        }

        return $this->forceFill([
            'status' => 'draft',
            'submitted_at' => null,
            'approver_employee_id' => null,
            'approved_at' => null,
            'approval_note' => null,
            'approval_skipped_reason' => null,
        ])->save();
    }

    public function batalkan(): bool
    {
        if (! in_array($this->status, self::OPEN_STATUSES, true)) {
            return false;
        }

        return $this->forceFill(['status' => 'dibatalkan'])->save();
    }

    /** Daftar barang hanya boleh diubah pemohon selama permintaan masih draf. */
    public function rincianBisaDiubah(): bool
    {
        return $this->status === 'draft';
    }

    /** Jumlah serah hanya boleh disetel tim GA selama permintaan menunggu diserahkan. */
    public function jumlahSerahBisaDisetel(): bool
    {
        return $this->status === 'disetujui';
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
            'disetujui' => 'info',
            'diserahkan' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    public function tahapLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Masih disusun pemohon',
            'diajukan' => 'Di meja '.($this->approver?->full_name ?? 'kepala departemen'),
            'disetujui' => 'Di meja tim GA, menunggu barangnya diserahkan',
            'diserahkan' => 'Selesai',
            'ditolak' => 'Ditolak '.($this->rejected_stage === 'ga' ? 'tim GA' : 'atasan'),
            default => 'Dibatalkan',
        };
    }

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

    /**
     * Keterangan tanggal dibutuhkan. Yang sudah lewat disebut apa adanya, karena permintaan
     * yang tanggal butuhnya sudah lewat adalah permintaan yang terlambat, bukan permintaan
     * biasa, dan tim GA perlu melihatnya tanpa menghitung tanggal sendiri.
     */
    public function kebutuhanLabel(): string
    {
        if (blank($this->needed_date)) {
            return 'Tidak menyebut tanggal';
        }

        $tanggal = $this->needed_date->translatedFormat('d M Y');

        if (! $this->isOpen()) {
            return 'Dibutuhkan '.$tanggal;
        }

        $selisih = (int) now()->startOfDay()->diffInDays($this->needed_date->startOfDay(), false);

        return match (true) {
            $selisih < 0 => 'Lewat tanggal butuh, '.$tanggal,
            $selisih === 0 => 'Dibutuhkan hari ini',
            $selisih === 1 => 'Dibutuhkan besok',
            default => 'Dibutuhkan '.$tanggal,
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

    public function scopeMenungguPenyerahan(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'disetujui');
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->purpose;
    }
}
