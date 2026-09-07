<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Tagihan dari rekanan.
 *
 * Satu tagihan adalah satu lembar faktur yang datang ke meja GA, dan alurnya tiga langkah:
 *
 *   GA mencatat dan mengajukan  ->  Manajer menyetujui  ->  Finance membayar
 *
 * Yang membuat modul ini berguna untuk anggaran bukan arsip fakturnya, melainkan baris
 * alokasinya. Satu tagihan listrik bisa dibebankan ke lima departemen sekaligus, dan
 * tanpa baris alokasi angkanya hanya bisa dibaca sebagai total kantor yang tidak menjawab
 * siapa pemakainya.
 *
 * Nilai tagihan tidak pernah menjadi kolom di tabel ini. Ia selalu dijumlahkan dari baris
 * alokasinya, karena tagihan yang totalnya tidak sama dengan jumlah rinciannya adalah
 * cacat yang paling mahal ditemukan belakangan, saat finance sudah terlanjur membayar.
 *
 * Yang masuk hitungan realisasi anggaran hanya tagihan yang sudah disetujui dan yang
 * sudah dibayar. Tagihan yang masih draf atau masih menunggu persetujuan dilaporkan
 * terpisah sebagai angka yang belum disetujui, bukan dijumlahkan diam diam, karena
 * tumpukan faktur yang belum disetujui adalah cara termudah membuat pagu terlihat sehat
 * padahal uangnya sudah habis.
 */
class VendorBill extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'vendor_bill';

    public const STATUSES = [
        'draft' => 'Draf',
        'diajukan' => 'Menunggu persetujuan',
        'disetujui' => 'Menunggu pembayaran',
        'dibayar' => 'Sudah dibayar',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang nilainya ikut dihitung sebagai realisasi anggaran. */
    public const COUNTED_STATUSES = ['disetujui', 'dibayar'];

    /** Status yang nilainya sudah pasti keluar tetapi belum disetujui. */
    public const PENDING_STATUSES = ['draft', 'diajukan'];

    /** Status yang berarti tagihan ini masih menunggu sesuatu terjadi. */
    public const OPEN_STATUSES = ['draft', 'diajukan', 'disetujui'];

    protected $fillable = [
        'code',
        'vendor_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'description',
        'status',
        'approved_by_user_id',
        'approved_at',
        'rejection_reason',
        'paid_date',
        'payment_reference',
        'paid_by_user_id',
        'file_path',
        'original_name',
        'size_bytes',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'approved_at' => 'datetime',
            'paid_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VendorBill $tagihan) {
            if (blank($tagihan->code)) {
                $tagihan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($tagihan->created_by_user_id)) {
                $tagihan->created_by_user_id = Auth::id();
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(VendorBillLine::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function paidByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    // ---------------------------------------------------------------- nilai

    /**
     * Nilai tagihan, yaitu jumlah seluruh baris alokasinya.
     *
     * Dihitung, bukan disimpan. Baris yang ditambah atau dihapus langsung mengubah
     * angkanya, dan tidak ada keadaan di mana total di layar berbeda dari rinciannya.
     */
    public function total(): float
    {
        return round((float) $this->lines()->sum('amount'), 2);
    }

    public function totalLabel(): string
    {
        return $this->lines()->exists()
            ? Rupiah::penuh($this->total())
            : 'Belum ada rincian';
    }

    /** Tahun anggaran yang dibebani. Tanggal faktur, bukan tanggal bayar. */
    public function tahunAnggaran(): ?int
    {
        return $this->invoice_date?->year;
    }

    // ---------------------------------------------------------------- alur

    /**
     * Tagihan tanpa rincian tidak bisa diajukan. Yang disetujui manajer adalah angka,
     * dan tagihan tanpa baris alokasi berarti meminta tanda tangan untuk nol rupiah
     * yang nanti berubah sendiri setelah disetujui.
     */
    public function alasanBelumBisaDiajukan(): ?string
    {
        if ($this->status !== 'draft') {
            return 'Tagihan ini sudah tidak berstatus draf.';
        }

        if (! $this->lines()->exists()) {
            return 'Belum ada rincian pembebanan. Tambahkan minimal satu baris supaya ada nilai yang disetujui.';
        }

        if ($this->total() <= 0) {
            return 'Jumlah seluruh rinciannya nol. Periksa lagi nilai tiap barisnya.';
        }

        return null;
    }

    public function ajukan(): bool
    {
        if ($this->alasanBelumBisaDiajukan() !== null) {
            return false;
        }

        return $this->forceFill([
            'status' => 'diajukan',
            'rejection_reason' => null,
        ])->save();
    }

    public function setujui(): bool
    {
        if ($this->status !== 'diajukan') {
            return false;
        }

        return $this->forceFill([
            'status' => 'disetujui',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
        ])->save();
    }

    public function tolak(string $alasan): bool
    {
        if ($this->status !== 'diajukan') {
            return false;
        }

        return $this->forceFill([
            'status' => 'ditolak',
            'rejection_reason' => $alasan,
            'approved_by_user_id' => null,
            'approved_at' => null,
        ])->save();
    }

    /**
     * Mengembalikan tagihan yang ditolak ke draf supaya bisa diperbaiki.
     *
     * Alasan penolakannya sengaja tidak dihapus. Yang memperbaiki perlu membacanya sambil
     * mengubah barisnya, dan menghapusnya berarti memaksa ia mengingat sendiri apa yang
     * tadi salah.
     */
    public function kembalikanKeDraft(): bool
    {
        if ($this->status !== 'ditolak') {
            return false;
        }

        return $this->forceFill(['status' => 'draft'])->save();
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
        if (! in_array($this->status, ['draft', 'diajukan', 'disetujui'], true)) {
            return false;
        }

        return $this->forceFill(['status' => 'dibatalkan'])->save();
    }

    /**
     * Rincian hanya boleh diubah selama tagihan masih draf.
     *
     * Setelah diajukan, mengubah barisnya berarti mengubah angka yang sedang atau sudah
     * ditandatangani orang lain tanpa ia tahu. Tagihan yang ditolak kembali ke draf lebih
     * dulu, dan barisnya bisa diperbaiki di sana.
     */
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

    /** Sudah lewat tanggal jatuh tempo dan belum dibayar. */
    public function jatuhTempoTerlewat(): bool
    {
        return $this->isOpen()
            && filled($this->due_date)
            && $this->due_date->isPast();
    }

    /**
     * Keterangan jatuh tempo sebagai kalimat. Tagihan tanpa tanggal jatuh tempo
     * mengatakannya apa adanya, bukan diam diam tampil seolah masih lama.
     */
    public function jatuhTempoLabel(): string
    {
        if ($this->status === 'dibayar') {
            return 'Dibayar '.($this->paid_date?->translatedFormat('d M Y') ?? 'tanggalnya tidak dicatat');
        }

        if (! $this->isOpen()) {
            return 'Tidak berlaku lagi';
        }

        if (blank($this->due_date)) {
            return 'Jatuh temponya tidak dicatat';
        }

        $hari = (int) Carbon::today()->diffInDays($this->due_date, false);

        return match (true) {
            $hari < 0 => 'Lewat '.abs($hari).' hari',
            $hari === 0 => 'Jatuh tempo hari ini',
            default => 'Sisa '.$hari.' hari',
        };
    }

    public function jatuhTempoColor(): string
    {
        if (! $this->isOpen() || blank($this->due_date)) {
            return 'gray';
        }

        $hari = (int) Carbon::today()->diffInDays($this->due_date, false);

        return match (true) {
            $hari < 0 => 'danger',
            $hari <= 7 => 'warning',
            default => 'gray',
        };
    }

    /**
     * Ringkasan pembebanan untuk dibaca sekilas di tabel dan di kotak persetujuan.
     *
     * Baris tanpa departemen tidak pernah dihitung sebagai departemen. Menyebut tagihan
     * yang jatuh ke dua departemen ditambah satu porsi bersama sebagai "3 departemen"
     * membuat yang menandatangani mengira seluruh nilainya sudah ada pemiliknya, padahal
     * ada bagian yang justru belum.
     */
    public function pembebananLabel(): string
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->with('department')->get();

        if ($baris->isEmpty()) {
            return 'Belum ada rincian';
        }

        $departemen = $baris
            ->filter(fn (VendorBillLine $l): bool => $l->department !== null)
            ->map(fn (VendorBillLine $l): string => $l->department->name)
            ->unique();

        $adaBersama = $baris->contains(fn (VendorBillLine $l): bool => $l->department === null);

        $inti = match ($departemen->count()) {
            0 => 'Belum terbebankan ke departemen',
            1 => $departemen->first(),
            default => $departemen->count().' departemen',
        };

        return $adaBersama && $departemen->count() > 0
            ? $inti.' dan sebagian biaya bersama'
            : $inti;
    }

    public function sizeLabel(): string
    {
        if (blank($this->size_bytes)) {
            return 'Tidak ada pindaian';
        }

        $kb = (int) $this->size_bytes / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1, ',', '.').' MB'
            : number_format(max($kb, 1), 0, ',', '.').' KB';
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeTerbuka(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::OPEN_STATUSES);
    }

    public function scopeMenungguPersetujuan(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'diajukan');
    }

    public function scopeMenungguPembayaran(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'disetujui');
    }

    /**
     * Kolom status disebut lengkap dengan nama tabelnya, karena penyaring ini akan
     * dipakai laporan yang menggabungkan tabel lain yang juga punya kolom status, dan
     * PostgreSQL menolak pertanyaan yang ambigu dengan galat yang jauh dari sebabnya.
     */
    public function scopeTerhitung(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::COUNTED_STATUSES);
    }

    public function scopeBelumDisetujui(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::PENDING_STATUSES);
    }

    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::OPEN_STATUSES)
            ->whereNotNull($query->qualifyColumn('due_date'))
            ->whereDate($query->qualifyColumn('due_date'), '<', Carbon::today());
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.($this->vendor?->name ?? '');
    }
}
