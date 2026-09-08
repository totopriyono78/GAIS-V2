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
 * Satu sesi penghitungan fisik barang habis pakai.
 *
 * Alurnya menyalin opname aset dari kiriman B, sengaja:
 *
 *   Susun daftar  ->  Mulai menghitung  ->  Catat temuan  ->  Selesai  ->  Terapkan penyesuaian
 *
 * Langkah terakhir dipisah dan butuh izin tersendiri, sesuai keputusan pemilik proyek pada
 * 8 September 2026. Menutup sesi dan mengubah angka gudang adalah dua keputusan yang berbeda,
 * dan menggabungkannya berarti orang yang menutup lembar hitungan sekaligus menggeser stok
 * tanpa ada jeda untuk memeriksa ulang.
 *
 * Penyesuaian di sini tidak mengubah kolom mana pun, melainkan melahirkan mutasi koreksi.
 * Stok tidak pernah disimpan sebagai kolom sejak kiriman C, jadi satu satunya cara mengubahnya
 * adalah menambah baris ke buku stok, dan itu justru bagus: hasil opname jadi meninggalkan
 * jejak yang bisa ditelusuri seperti mutasi lainnya.
 */
class SupplyOpname extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'supply_opname';

    public const STATUSES = [
        'draft' => 'Draf',
        'berjalan' => 'Sedang dihitung',
        'selesai' => 'Selesai dihitung',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected $fillable = [
        'code',
        'name',
        'scope_category',
        'scope_location_id',
        'status',
        'started_at',
        'finished_at',
        'adjusted_at',
        'adjusted_by_user_id',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'adjusted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SupplyOpname $opname) {
            if (blank($opname->code)) {
                $opname->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($opname->created_by_user_id)) {
                $opname->created_by_user_id = Auth::id();
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function lines(): HasMany
    {
        return $this->hasMany(SupplyOpnameLine::class);
    }

    /*
     * Namanya tidak diawali kata scope walaupun kolomnya scope_location_id, dengan alasan
     * yang sama seperti pada opname aset: Eloquent memperlakukan setiap metode berawalan
     * scope sebagai local query scope.
     */
    public function targetLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'scope_location_id');
    }

    public function adjustedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by_user_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'draft' => 'gray',
            'berjalan' => 'warning',
            'selesai' => $this->isAdjusted() ? 'success' : 'info',
            default => 'gray',
        };
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isRunning(): bool
    {
        return $this->status === 'berjalan';
    }

    public function isFinished(): bool
    {
        return $this->status === 'selesai';
    }

    public function isAdjusted(): bool
    {
        return $this->adjusted_at !== null;
    }

    /**
     * Apakah pergerakan stok di sela sela penghitungan masih perlu diwaspadai orang.
     *
     * Peringatannya berbunyi "pastikan hitungan fisiknya diambil setelah barang itu bergerak",
     * dan kalimat itu hanya ada gunanya selama masih ada orang yang memegang hasil hitungan
     * dan bisa berbuat sesuatu terhadapnya. Pada sesi yang dibatalkan tidak akan ada hitungan
     * yang dipakai, dan pada sesi yang penyesuaiannya sudah diterapkan urusannya sudah lewat,
     * lagipula yang menggeser stoknya justru koreksi sesi itu sendiri.
     */
    public function pergerakanPerluDiwaspadai(): bool
    {
        return in_array($this->status, ['draft', 'berjalan', 'selesai'], true)
            && ! $this->isAdjusted();
    }

    public function tahapLabel(): string
    {
        return match (true) {
            $this->status === 'draft' => $this->lines()->exists()
                ? 'Daftar sudah disusun, tinggal dimulai'
                : 'Daftar targetnya belum disusun',
            $this->status === 'berjalan' => $this->belumDihitung().' barang belum dihitung',
            $this->status === 'selesai' && ! $this->isAdjusted() => 'Menunggu penyesuaian stok diterapkan',
            $this->status === 'selesai' => 'Selesai dan stoknya sudah disesuaikan',
            default => 'Dibatalkan',
        };
    }

    /** Ringkasan cakupan dalam satu kalimat. */
    public function describeScope(): string
    {
        $bagian = [];

        if (filled($this->scope_category)) {
            $bagian[] = 'kategori '.(SupplyItem::CATEGORIES[$this->scope_category] ?? $this->scope_category);
        }

        if ($this->scope_location_id !== null) {
            $bagian[] = 'tempat simpan '.($this->targetLocation?->name ?? 'yang sudah dihapus');
        }

        return $bagian === [] ? 'Seluruh barang habis pakai yang aktif' : 'Terbatas pada '.implode(' dan ', $bagian);
    }

    // ---------------------------------------------------------------- hitungan

    public function jumlahBaris(): int
    {
        return $this->lines()->count();
    }

    public function sudahDihitung(): int
    {
        return $this->lines()->where('checked', true)->count();
    }

    public function belumDihitung(): int
    {
        return $this->lines()->where('checked', false)->count();
    }

    /** Berapa baris yang hitungan fisiknya berbeda dari stok menurut catatan sekarang. */
    public function jumlahSelisih(): int
    {
        return $this->lines()->with('item')->get()
            ->filter(fn (SupplyOpnameLine $b): bool => $b->checked && $b->selisih() !== 0)
            ->count();
    }

    /**
     * Baris yang stoknya sempat bergerak setelah daftar disusun.
     *
     * Ini bukan kesalahan, hanya keadaan yang perlu dilihat orang. Barang yang keluar gudang
     * di tengah penghitungan membuat lembar hitungan patut dicurigai, dan menyembunyikannya
     * berarti membiarkan orang menyesuaikan stok berdasarkan angka yang sudah usang.
     */
    public function barisStokBergerak(): int
    {
        // Sesinya dititipkan ke tiap baris, bukan dibiarkan diambil ulang satu per satu.
        // stokBergerak() menanyakan apakah sesinya sudah disesuaikan, dan tanpa titipan ini
        // pertanyaan itu berubah menjadi satu kueri untuk setiap barang di daftar.
        return $this->lines()->with('item')->get()
            ->filter(fn (SupplyOpnameLine $b): bool => $b->setRelation('opname', $this)->stokBergerak())
            ->count();
    }

    // ---------------------------------------------------------------- alur

    public function targetQuery(): Builder
    {
        $query = SupplyItem::query()->where('is_active', true);

        if (filled($this->scope_category)) {
            $query->where('category', $this->scope_category);
        }

        if ($this->scope_location_id !== null) {
            $query->where('location_id', $this->scope_location_id);
        }

        return $query;
    }

    /**
     * Menyusun daftar target dari cakupan, beserta stok menurut catatan saat ini.
     *
     * Baris lama dihapus lebih dulu supaya daftarnya benar benar mencerminkan keadaan saat
     * disusun, sama seperti pada opname aset. Karena itu penyusunan ulang hanya boleh selama
     * sesi masih draf: mengulangnya di tengah penghitungan akan membuang hitungan fisik yang
     * sudah terlanjur dicatat orang di lapangan.
     */
    public function generateLines(): int
    {
        return DB::transaction(function (): int {
            $this->lines()->delete();

            $jumlah = 0;

            $this->targetQuery()->orderBy('name')->chunkById(200, function ($items) use (&$jumlah): void {
                $baris = [];

                foreach ($items as $item) {
                    $baris[] = [
                        'supply_opname_id' => $this->id,
                        'supply_item_id' => $item->id,
                        'item_code' => $item->code,
                        'item_name' => $item->name,
                        'unit' => $item->unit,
                        'system_quantity' => $item->currentStock(),
                        'counted_quantity' => null,
                        'checked' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($baris !== []) {
                    SupplyOpnameLine::query()->insert($baris);
                    $jumlah += count($baris);
                }
            });

            return $jumlah;
        });
    }

    public function alasanBelumBisaDimulai(): ?string
    {
        if ($this->status !== 'draft') {
            return 'Sesi ini sudah tidak berstatus draf.';
        }

        if (! $this->lines()->exists()) {
            return 'Daftar targetnya masih kosong. Susun daftar lebih dulu supaya ada yang bisa dihitung.';
        }

        return null;
    }

    public function mulai(): bool
    {
        if ($this->alasanBelumBisaDimulai() !== null) {
            return false;
        }

        return $this->forceFill(['status' => 'berjalan', 'started_at' => now()])->save();
    }

    public function selesaikan(): bool
    {
        if ($this->status !== 'berjalan') {
            return false;
        }

        return $this->forceFill(['status' => 'selesai', 'finished_at' => now()])->save();
    }

    public function kembalikanKeHitung(): bool
    {
        if ($this->status !== 'selesai' || $this->isAdjusted()) {
            return false;
        }

        return $this->forceFill(['status' => 'berjalan', 'finished_at' => null])->save();
    }

    public function batalkan(): bool
    {
        if ($this->isAdjusted() || ! in_array($this->status, ['draft', 'berjalan', 'selesai'], true)) {
            return false;
        }

        return $this->forceFill(['status' => 'dibatalkan'])->save();
    }

    /**
     * Menerapkan hasil hitungan ke buku stok.
     *
     * Untuk tiap baris yang sudah dihitung dan selisihnya bukan nol, lahir satu mutasi koreksi
     * yang besarnya persis selisih antara hitungan fisik dan stok menurut catatan **saat ini**.
     * Setelah ini stok tiap barang sama persis dengan angka yang dihitung orang di gudang, dan
     * itulah satu satunya hasil yang membuat opname ada gunanya.
     *
     * Baris yang belum dihitung dilewati tanpa mengubah apa pun. Tidak dihitung berarti tidak
     * diketahui, dan menyamakan tidak diketahui dengan nol akan menghapus stok barang yang
     * kebetulan tidak sempat dicek.
     *
     * Seluruhnya dibungkus satu transaksi basis data, dengan alasan yang sama seperti
     * penyerahan dan penerimaan: koreksi yang berhenti di tengah meninggalkan gudang yang
     * separuh disesuaikan, dan tidak ada layar yang bisa menjelaskan keadaan itu.
     *
     * @return array{koreksi: int, tambah: int, kurang: int, dilewati: int}
     */
    public function terapkanPenyesuaian(): array
    {
        $hasil = ['koreksi' => 0, 'tambah' => 0, 'kurang' => 0, 'dilewati' => 0];

        DB::transaction(function () use (&$hasil): void {
            foreach ($this->lines()->with('item')->get() as $baris) {
                if (! $baris->checked || $baris->counted_quantity === null) {
                    $hasil['dilewati']++;

                    continue;
                }

                $selisih = $baris->selisih();

                if ($selisih === 0 || $baris->item === null) {
                    continue;
                }

                $mutasi = SupplyTransaction::query()->create([
                    'supply_item_id' => $baris->supply_item_id,
                    'type' => $selisih > 0 ? 'koreksi_tambah' : 'koreksi_kurang',
                    'quantity' => abs($selisih),
                    'transaction_date' => now()->toDateString(),
                    'reference' => $this->code,
                    'notes' => 'Hasil opname '.$this->code.'. Hitungan fisik '
                        .$baris->item->formatQuantity((int) $baris->counted_quantity)
                        .', catatan '.$baris->item->formatQuantity($baris->stokSekarang()).'.'
                        .(filled($baris->notes) ? ' '.$baris->notes : ''),
                ]);

                $baris->forceFill(['supply_transaction_id' => $mutasi->getKey()])->save();

                $hasil['koreksi']++;
                $selisih > 0 ? $hasil['tambah']++ : $hasil['kurang']++;
            }

            $this->forceFill([
                'adjusted_at' => now(),
                'adjusted_by_user_id' => Auth::id(),
            ])->save();
        });

        return $hasil;
    }

    /** Temuan hanya bisa dicatat selama sesi sedang berjalan. */
    public function temuanBisaDicatat(): bool
    {
        return $this->status === 'berjalan';
    }

    /** Daftar target hanya bisa disusun ulang selama sesi masih draf. */
    public function daftarBisaDisusun(): bool
    {
        return $this->status === 'draft';
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeTerbuka(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('status'), ['draft', 'berjalan', 'selesai'])
            ->whereNull($query->qualifyColumn('adjusted_at'));
    }

    public function scopeMenungguPenyesuaian(Builder $query): Builder
    {
        return $query->where($query->qualifyColumn('status'), 'selesai')
            ->whereNull($query->qualifyColumn('adjusted_at'));
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
