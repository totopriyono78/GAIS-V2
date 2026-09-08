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
use Illuminate\Support\Facades\DB;

/**
 * Pembelian ATK, dalam dua bentuk yang memakai tabel dan layar yang sama.
 *
 * Pesanan resmi:
 *
 *   GA menyusun  ->  Manajer menyetujui  ->  Barang datang bertahap  ->  Selesai
 *
 * Pembelian langsung:
 *
 *   GA mencatat apa yang sudah dibeli  ->  Langsung diterima penuh  ->  Selesai
 *
 * Pembelian langsung tidak melewati persetujuan, dan itu bukan kelalaian. Saat seseorang
 * mencatat pembelian langsung, uangnya sudah keluar dan barangnya sudah di tangan, jadi
 * persetujuan di titik itu tidak bisa mencegah apa pun. Kendalinya ada di tagihan rekanan
 * atau penggantian biaya yang tetap harus disetujui sebelum dibayar. Alasan lompatan itu
 * tetap ditulis ke kolomnya sendiri supaya terbaca di layar, sama seperti pada tiga modul
 * alur lainnya.
 *
 * Yang paling penting untuk diingat saat membaca berkas ini: penerimaan barang di sini
 * menambah stok, persis seperti penyerahan barang di permintaan pemakaian menguranginya.
 * Karena itu penerimaan dibungkus satu transaksi basis data dan seluruh barisnya diperiksa
 * lebih dulu sebelum satu pun mutasi disimpan.
 */
class SupplyPurchase extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'supply_purchase';

    public const KINDS = [
        'pesanan' => 'Pesanan resmi ke pemasok',
        'langsung' => 'Pembelian langsung',
    ];

    public const STATUSES = [
        'draft' => 'Draf',
        'diajukan' => 'Menunggu persetujuan',
        'disetujui' => 'Menunggu barang datang',
        'diterima_sebagian' => 'Diterima sebagian',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /** Status yang berarti pesanan masih menunggu sesuatu terjadi. */
    public const OPEN_STATUSES = ['draft', 'diajukan', 'disetujui', 'diterima_sebagian'];

    /** Status yang membolehkan barang diterima. */
    public const RECEIVABLE_STATUSES = ['disetujui', 'diterima_sebagian'];

    protected $fillable = [
        'code',
        'kind',
        'vendor_id',
        'supplier_name',
        'order_date',
        'expected_date',
        'description',
        'notes',
        'status',
        'submitted_at',
        'approved_by_user_id',
        'approved_at',
        'approval_note',
        'approval_skipped_reason',
        'rejection_reason',
        'closed_at',
        'closing_reason',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupplyPurchase $pesanan) {
            if (blank($pesanan->code)) {
                $pesanan->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($pesanan->created_by_user_id)) {
                $pesanan->created_by_user_id = Auth::id();
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
        return $this->hasMany(SupplyPurchaseLine::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(SupplyReceipt::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(VendorBill::class);
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    // ---------------------------------------------------------------- nilai

    /** Nilai pesanan, selalu dijumlahkan dari barisnya dan tidak pernah disimpan. */
    public function total(): float
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->get();

        return round($baris->sum(fn (SupplyPurchaseLine $b): float => $b->subtotal()), 2);
    }

    /**
     * Nilai barang yang benar benar sudah datang, dihitung dengan harga pesanannya.
     *
     * Ini angka pembanding tagihan. Barang yang belum datang tidak boleh ikut dihitung,
     * karena pertanyaan yang dijawabnya adalah "berapa nilai yang sudah kita terima", bukan
     * "berapa nilai yang kita janjikan".
     */
    public function totalDiterima(): float
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->get();

        return round($baris->sum(
            fn (SupplyPurchaseLine $b): float => $b->jumlahDiterima() * (float) $b->unit_price
        ), 2);
    }

    public function totalLabel(): string
    {
        return $this->lines()->exists() ? Rupiah::penuh($this->total()) : 'Belum ada barang';
    }

    /** Berapa jenis barang yang belum datang seluruhnya. */
    public function barisBelumLengkap(): int
    {
        $baris = $this->relationLoaded('lines') ? $this->lines : $this->lines()->get();

        return $baris->filter(fn (SupplyPurchaseLine $b): bool => $b->sisa() > 0)->count();
    }

    public function seluruhnyaDiterima(): bool
    {
        return $this->lines()->exists() && $this->barisBelumLengkap() === 0;
    }

    public function adaYangSudahDiterima(): bool
    {
        return $this->receipts()->exists();
    }

    public function jenisLabel(): string
    {
        $jumlah = $this->relationLoaded('lines') ? $this->lines->count() : $this->lines()->count();

        return $jumlah === 0 ? 'Belum ada barang' : $jumlah.' jenis barang';
    }

    /** Siapa pemasoknya, dari kolom mana pun yang terisi. */
    public function pemasokLabel(): string
    {
        if ($this->kind === 'pesanan') {
            return $this->vendor?->name ?? 'Rekanan sudah dihapus';
        }

        return filled($this->supplier_name) ? $this->supplier_name : 'Penjual tidak dicatat';
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? $this->kind;
    }

    // ---------------------------------------------------------------- alur

    public function alasanBelumBisaDiajukan(): ?string
    {
        if ($this->status !== 'draft') {
            return 'Pembelian ini sudah tidak berstatus draf.';
        }

        if (! $this->lines()->exists()) {
            return 'Belum ada barang yang dibeli. Tambahkan minimal satu baris supaya ada nilai yang disetujui.';
        }

        if ($this->total() <= 0) {
            return 'Nilai seluruh barisnya nol. Periksa lagi jumlah dan harga tiap barangnya.';
        }

        if ($this->kind === 'pesanan' && blank($this->vendor_id)) {
            return 'Pesanan resmi harus menunjuk rekanan terdaftar. Pilih rekanannya lebih dulu.';
        }

        return null;
    }

    /**
     * Mengajukan pesanan resmi ke manajer GA.
     *
     * Hanya untuk `kind` pesanan. Pembelian langsung memakai catatDanTerima() dan tidak
     * pernah melewati status diajukan.
     */
    public function ajukan(): bool
    {
        if ($this->kind !== 'pesanan' || $this->alasanBelumBisaDiajukan() !== null) {
            return false;
        }

        return $this->forceFill([
            'status' => 'diajukan',
            'submitted_at' => now(),
            'rejection_reason' => null,
        ])->save();
    }

    public function setujui(?string $catatan = null): bool
    {
        if ($this->status !== 'diajukan') {
            return false;
        }

        return $this->forceFill([
            'status' => 'disetujui',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
            'approval_note' => $catatan,
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
        ])->save();
    }

    public function kembalikanKeDraft(): bool
    {
        if ($this->status !== 'ditolak') {
            return false;
        }

        return $this->forceFill([
            'status' => 'draft',
            'submitted_at' => null,
            'approved_by_user_id' => null,
            'approved_at' => null,
            'approval_note' => null,
        ])->save();
    }

    /**
     * Mencatat pembelian langsung dan menerima seluruh barangnya sekaligus.
     *
     * Satu tindakan, bukan tiga, karena ketiga hal yang biasanya terpisah sudah terjadi
     * semua sebelum orang membuka layar ini: pesanannya sudah dibuat di kasir, disetujui
     * dengan membayar, dan diterima dengan membawa pulang barangnya.
     */
    public function catatDanTerima(string $tanggal, ?string $nomorNota = null, ?string $catatan = null): bool
    {
        if ($this->kind !== 'langsung' || $this->status !== 'draft') {
            return false;
        }

        if ($this->alasanBelumBisaDiajukan() !== null) {
            return false;
        }

        $jumlah = [];

        foreach ($this->lines()->get() as $baris) {
            $jumlah[$baris->getKey()] = $baris->quantity_ordered;
        }

        $this->forceFill([
            'status' => 'disetujui',
            'submitted_at' => now(),
            'approved_at' => now(),
            'approval_skipped_reason' => 'Pembelian langsung, barangnya sudah di tangan saat dicatat',
        ])->save();

        return $this->terima($tanggal, $jumlah, $nomorNota, $catatan) !== null;
    }

    /**
     * Menerima barang, seluruhnya atau sebagian. Inilah satu satunya tempat stok bertambah
     * karena pembelian.
     *
     * Seluruhnya dibungkus satu transaksi basis data. Kalau baris kelima gagal, empat mutasi
     * sebelumnya ikut dibatalkan dan stok kembali seperti sebelum tombol ditekan. Alasannya
     * sama dengan penyerahan barang di permintaan pemakaian: penerimaan yang berhenti di
     * tengah meninggalkan gudang yang catatannya tidak sama dengan isinya.
     *
     * Harga satuan ikut dipasang di mutasinya. Itu yang membuat kartu barang memperbarui
     * harga pembelian terakhirnya sendiri, dan yang membuat nilai persediaan di dasbor
     * bergerak mengikuti harga yang benar benar dibayar.
     *
     * @param  array<int, int>  $jumlah  kunci adalah id baris pesanan, nilainya jumlah yang datang
     */
    public function terima(string $tanggal, array $jumlah, ?string $nomorNota = null, ?string $catatan = null): ?SupplyReceipt
    {
        if (! in_array($this->status, self::RECEIVABLE_STATUSES, true)) {
            return null;
        }

        if ($this->masalahPenerimaan($jumlah) !== []) {
            return null;
        }

        $bersih = array_filter($jumlah, fn (int $n): bool => $n > 0);

        if ($bersih === []) {
            return null;
        }

        $penerimaan = null;

        DB::transaction(function () use ($tanggal, $bersih, $nomorNota, $catatan, &$penerimaan): void {
            $penerimaan = $this->receipts()->create([
                'receipt_date' => $tanggal,
                'delivery_note_number' => $nomorNota,
                'notes' => $catatan,
                'received_by_user_id' => Auth::id(),
            ]);

            foreach ($bersih as $barisId => $n) {
                $baris = $this->lines()->whereKey($barisId)->first();

                if ($baris === null) {
                    continue;
                }

                $mutasi = SupplyTransaction::query()->create([
                    'supply_item_id' => $baris->supply_item_id,
                    'type' => 'masuk',
                    'quantity' => $n,
                    'unit_price' => $baris->unit_price,
                    'transaction_date' => $tanggal,
                    'supplier' => $this->pemasokLabel(),
                    'reference' => $penerimaan->code,
                    'notes' => 'Penerimaan '.$penerimaan->code.' atas pembelian '.$this->code.'. '.$this->description,
                ]);

                $penerimaan->lines()->create([
                    'supply_purchase_line_id' => $baris->getKey(),
                    'quantity' => $n,
                    'supply_transaction_id' => $mutasi->getKey(),
                ]);
            }

            $this->load('lines');
            $this->forceFill([
                'status' => $this->seluruhnyaDiterima() ? 'selesai' : 'diterima_sebagian',
            ])->save();
        });

        return $penerimaan;
    }

    /**
     * Apa saja yang salah dengan jumlah yang akan diterima, sebagai kalimat siap baca.
     *
     * Yang diperiksa hanya kelebihan terima. Kekurangan tidak diperiksa, karena barang yang
     * datang kurang adalah keadaan normal yang justru menjadi alasan penerimaan sebagian ada.
     *
     * @param  array<int, int>  $jumlah
     * @return array<int, string>
     */
    public function masalahPenerimaan(array $jumlah): array
    {
        $masalah = [];

        foreach ($this->lines()->with('item')->get() as $baris) {
            $n = (int) ($jumlah[$baris->getKey()] ?? 0);

            if ($n <= 0) {
                continue;
            }

            $sisa = $baris->sisa();

            if ($n > $sisa) {
                $nama = $baris->item?->name ?? 'Barang yang sudah dihapus';
                $masalah[] = $nama.' diterima '.$baris->formatJumlah($n)
                    .', padahal sisa yang belum datang tinggal '.$baris->formatJumlah($sisa);
            }
        }

        return $masalah;
    }

    /**
     * Menutup pesanan yang sisanya tidak akan datang.
     *
     * Tanpa ini, daftar pesanan terbuka akan penuh selamanya oleh dua box yang batal dikirim
     * pemasok, dan orang akan berhenti mempercayai daftar itu. Sisa yang tidak jadi datang
     * tetap terbaca di barisnya, jadi menutup pesanan tidak menghapus catatan apa pun.
     */
    public function tutup(string $alasan): bool
    {
        if (! in_array($this->status, ['disetujui', 'diterima_sebagian'], true)) {
            return false;
        }

        return $this->forceFill([
            'status' => 'selesai',
            'closed_at' => now(),
            'closing_reason' => $alasan,
        ])->save();
    }

    public function batalkan(): bool
    {
        if (! in_array($this->status, self::OPEN_STATUSES, true)) {
            return false;
        }

        // Pesanan yang barangnya sudah terlanjur masuk gudang tidak boleh dibatalkan, karena
        // membatalkannya berarti mengaku tidak pernah membeli barang yang stoknya sudah
        // bertambah. Yang seperti itu ditutup dengan alasan, bukan dibatalkan.
        if ($this->adaYangSudahDiterima()) {
            return false;
        }

        return $this->forceFill(['status' => 'dibatalkan'])->save();
    }

    /** Rincian hanya bisa diubah selama pembelian masih draf. */
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
            'diterima_sebagian' => 'warning',
            'selesai' => 'success',
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
            'draft' => $this->kind === 'langsung'
                ? 'Belum dicatat masuk gudang'
                : 'Masih disusun tim GA',
            'diajukan' => 'Di meja manajer GA',
            'disetujui' => 'Menunggu barangnya datang',
            'diterima_sebagian' => $this->barisBelumLengkap().' jenis barang belum datang seluruhnya',
            'selesai' => filled($this->closing_reason) ? 'Ditutup sebelum lengkap' : 'Selesai',
            'ditolak' => 'Ditolak manajer GA',
            default => 'Dibatalkan',
        };
    }

    /** Keterangan tanggal barang dijanjikan datang. */
    public function janjiLabel(): string
    {
        if (blank($this->expected_date)) {
            return 'Tidak menyebut tanggal';
        }

        $tanggal = $this->expected_date->translatedFormat('d M Y');

        if (! in_array($this->status, self::RECEIVABLE_STATUSES, true)) {
            return 'Dijanjikan '.$tanggal;
        }

        $selisih = (int) now()->startOfDay()->diffInDays($this->expected_date->startOfDay(), false);

        return match (true) {
            $selisih < 0 => 'Lewat janji, '.$tanggal,
            $selisih === 0 => 'Dijanjikan hari ini',
            $selisih === 1 => 'Dijanjikan besok',
            default => 'Dijanjikan '.$tanggal,
        };
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

    public function scopeMenungguBarang(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), self::RECEIVABLE_STATUSES);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->description;
    }
}
