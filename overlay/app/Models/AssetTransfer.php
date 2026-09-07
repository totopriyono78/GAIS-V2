<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu dokumen serah terima aset.
 *
 * Menyimpan dokumen ini sekaligus memindahkan asetnya. Tidak ada tahap
 * persetujuan terpisah, karena serah terima dicatat setelah barangnya benar
 * benar berpindah tangan, jadi tidak ada apa apa yang masih perlu diputuskan.
 *
 * Dokumen ini tidak bisa diubah. Kalau isinya salah, yang benar adalah mencatat
 * serah terima baru yang mengembalikan barangnya, sama seperti tidak ada orang
 * yang menghapus tanda tangan di berita acara yang sudah ditandatangani.
 * Satu satunya pengecualian ada di canBeUndone().
 */
class AssetTransfer extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'asset_transfer';

    public const REASONS = [
        'mutasi_ruangan' => 'Pindah ruangan',
        'ganti_pemegang' => 'Ganti penanggung jawab',
        'mutasi_departemen' => 'Pindah departemen',
        'perbaikan' => 'Dibawa untuk perbaikan',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'code',
        'asset_id',
        'transfer_date',
        'reason',
        'from_location_id',
        'from_custodian_employee_id',
        'from_department_id',
        'to_location_id',
        'to_custodian_employee_id',
        'to_department_id',
        'handed_over_by_employee_id',
        'received_by_employee_id',
        'reference',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AssetTransfer $transfer) {
            if (blank($transfer->code)) {
                $transfer->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($transfer->created_by_user_id)) {
                $transfer->created_by_user_id = Auth::id();
            }

            $transfer->copyOriginFromAsset();
        });

        static::created(function (AssetTransfer $transfer) {
            $transfer->applyToAsset();
        });

        // Pembatalan diletakkan di model, bukan di tombolnya, supaya jalur apa pun
        // yang menghapus dokumen ini tetap mengembalikan asetnya. Penjaganya di
        // canBeUndone(), jadi dokumen lama tidak akan menimpa keadaan yang sekarang.
        static::deleting(function (AssetTransfer $transfer) {
            if ($transfer->canBeUndone()) {
                $transfer->undo();
            }
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function fromCustodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from_custodian_employee_id');
    }

    public function toCustodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to_custodian_employee_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function handedOverBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'handed_over_by_employee_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by_employee_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    /**
     * Menyalin keadaan aset sekarang menjadi kolom asal. Dipanggil sekali saat
     * dokumen dibuat, jadi angkanya beku dan tidak ikut berubah kemudian.
     */
    public function copyOriginFromAsset(): void
    {
        $asset = $this->asset;

        if ($asset === null) {
            return;
        }

        $this->from_location_id = $asset->location_id;
        $this->from_custodian_employee_id = $asset->custodian_employee_id;
        $this->from_department_id = $asset->department_id;
    }

    /**
     * Kolom tujuan yang dikosongkan berarti tidak diubah. Dengan begitu satu
     * dokumen bisa memindahkan ruangannya saja tanpa memaksa orang mengisi ulang
     * penanggung jawab dan departemen yang memang tidak berubah.
     */
    public function applyToAsset(): void
    {
        $asset = $this->asset;

        if ($asset === null) {
            return;
        }

        $asset->forceFill(array_filter([
            'location_id' => $this->to_location_id,
            'custodian_employee_id' => $this->to_custodian_employee_id,
            'department_id' => $this->to_department_id,
        ], fn ($value) => $value !== null))->save();
    }

    /**
     * Apa saja yang benar benar berubah, untuk ditampilkan di layar tanpa membuat
     * orang membandingkan enam kolom sendiri.
     *
     * @return array<int, array{label: string, dari: string, ke: string}>
     */
    public function changes(): array
    {
        $hasil = [];

        $pasangan = [
            ['Lokasi', $this->fromLocation?->code, $this->toLocation?->code, $this->to_location_id],
            ['Penanggung jawab', $this->fromCustodian?->full_name, $this->toCustodian?->full_name, $this->to_custodian_employee_id],
            ['Departemen', $this->fromDepartment?->name, $this->toDepartment?->name, $this->to_department_id],
        ];

        foreach ($pasangan as [$label, $dari, $ke, $tujuanTerisi]) {
            if ($tujuanTerisi === null || $dari === $ke) {
                continue;
            }

            $hasil[] = [
                'label' => $label,
                'dari' => $dari ?? 'Belum diisi',
                'ke' => $ke ?? 'Belum diisi',
            ];
        }

        return $hasil;
    }

    public function describeChanges(): string
    {
        $perubahan = $this->changes();

        if ($perubahan === []) {
            return 'Tidak ada yang berubah';
        }

        return implode(', ', array_map(
            fn (array $baris): string => $baris['label'].' '.$baris['dari'].' menjadi '.$baris['ke'],
            $perubahan,
        ));
    }

    /**
     * Pembatalan hanya boleh untuk dokumen terakhir milik aset itu, dan hanya
     * selama aset masih persis seperti yang dokumen ini tetapkan. Kalau sudah ada
     * perpindahan lain sesudahnya, membatalkan yang ini akan mengembalikan keadaan
     * yang sudah tidak berlaku, dan itu justru merusak datanya.
     */
    public function canBeUndone(): bool
    {
        $asset = $this->asset;

        if ($asset === null) {
            return false;
        }

        $terakhir = static::query()
            ->where('asset_id', $this->asset_id)
            ->orderByDesc('transfer_date')
            ->orderByDesc('id')
            ->first();

        if ($terakhir === null || $terakhir->getKey() !== $this->getKey()) {
            return false;
        }

        foreach ([
            ['to_location_id', 'location_id'],
            ['to_custodian_employee_id', 'custodian_employee_id'],
            ['to_department_id', 'department_id'],
        ] as [$tujuan, $kolomAset]) {
            if ($this->{$tujuan} !== null && $asset->{$kolomAset} !== $this->{$tujuan}) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mengembalikan aset ke keadaan sebelum dokumen ini. Hanya dipanggil dari jalur
     * yang sudah memeriksa canBeUndone().
     */
    public function undo(): void
    {
        $asset = $this->asset;

        if ($asset === null) {
            return;
        }

        $kembali = [];

        if ($this->to_location_id !== null) {
            $kembali['location_id'] = $this->from_location_id;
        }

        if ($this->to_custodian_employee_id !== null) {
            $kembali['custodian_employee_id'] = $this->from_custodian_employee_id;
        }

        if ($this->to_department_id !== null) {
            $kembali['department_id'] = $this->from_department_id;
        }

        if ($kembali !== []) {
            $asset->forceFill($kembali)->save();
        }
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.($this->asset?->code ?? '');
    }
}
