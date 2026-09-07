<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Satu masa berlaku dokumen kendaraan.
 *
 * Perpanjangan tidak menimpa, melainkan menambah baris. Yang berlaku sekarang adalah
 * baris dengan tanggal berakhir terjauh untuk jenis itu, dihitung saat dibaca.
 */
class VehicleDocument extends Model
{
    use Auditable;

    /**
     * Jenis dokumen kendaraan yang punya masa berlaku.
     *
     * Pajak tahunan dan perpanjangan lima tahunan dipisah karena keduanya memang dua
     * urusan yang berbeda di Samsat: yang satu pengesahan tahunan, yang satu penggantian
     * STNK dan pelat. Digabung jadi satu jenis, salah satunya pasti terlewat.
     */
    public const TYPES = [
        'pajak_tahunan' => 'Pajak tahunan dan pengesahan STNK',
        'stnk_lima_tahun' => 'Perpanjangan STNK dan pelat nomor',
        'kir' => 'Uji berkala KIR',
        'asuransi' => 'Asuransi kendaraan',
        'lainnya' => 'Dokumen lain',
    ];

    /** Berapa hari sebelum jatuh tempo satu jenis mulai perlu diurus. */
    public const LEAD_DAYS = [
        'pajak_tahunan' => 30,
        'stnk_lima_tahun' => 60,
        'kir' => 30,
        'asuransi' => 30,
        'lainnya' => 30,
    ];

    protected $fillable = [
        'vehicle_id',
        'type',
        'document_number',
        'issued_date',
        'expires_at',
        'issuer',
        'cost',
        'file_path',
        'original_name',
        'size_bytes',
        'notes',
        'uploaded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expires_at' => 'date',
            'cost' => 'decimal:2',
            'size_bytes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VehicleDocument $dokumen) {
            if (blank($dokumen->uploaded_by_user_id)) {
                $dokumen->uploaded_by_user_id = Auth::id();
            }
        });

        static::saving(function (VehicleDocument $dokumen) {
            if (! $dokumen->isDirty('file_path') && $dokumen->size_bytes !== null) {
                return;
            }

            $dokumen->size_bytes = (filled($dokumen->file_path) && Storage::disk('public')->exists($dokumen->file_path))
                ? Storage::disk('public')->size($dokumen->file_path)
                : null;
        });

        static::deleted(function (VehicleDocument $dokumen) {
            if (filled($dokumen->file_path)) {
                Storage::disk('public')->delete($dokumen->file_path);
            }
        });
    }

    /**
     * FileUpload mengirim nama berkas asli sebagai larik berisi satu nilai. Diratakan di
     * sini supaya kolomnya tetap berisi teks, bukan JSON yang terbaca sebagai sampah.
     */
    protected function originalName(): Attribute
    {
        return Attribute::set(function (mixed $nilai): ?string {
            if (is_array($nilai)) {
                $nilai = reset($nilai);
            }

            return blank($nilai) ? null : (string) $nilai;
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function jenisLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function leadDays(): int
    {
        return self::LEAD_DAYS[$this->type] ?? 30;
    }

    /** Sisa hari sampai berakhir. Negatif berarti sudah lewat. */
    public function sisaHari(): int
    {
        return (int) Carbon::today()->diffInDays($this->expires_at, false);
    }

    public function sudahLewat(): bool
    {
        return $this->sisaHari() < 0;
    }

    public function segeraJatuhTempo(): bool
    {
        $sisa = $this->sisaHari();

        return $sisa >= 0 && $sisa <= $this->leadDays();
    }

    public function keteranganWaktu(): string
    {
        $sisa = $this->sisaHari();

        if ($sisa < 0) {
            return 'Lewat '.abs($sisa).' hari';
        }

        if ($sisa === 0) {
            return 'Berakhir hari ini';
        }

        if ($sisa <= $this->leadDays()) {
            return 'Sisa '.$sisa.' hari';
        }

        return 'Masih '.$sisa.' hari';
    }

    public function keadaanColor(): string
    {
        if ($this->sudahLewat()) {
            return 'danger';
        }

        return $this->segeraJatuhTempo() ? 'warning' : 'success';
    }

    public function sizeLabel(): string
    {
        $bytes = (int) $this->size_bytes;

        if ($bytes <= 0) {
            return 'Tidak ada berkas';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return number_format($bytes / 1024 / 1024, 1, ',', '.').' MB';
    }

    // ---------------------------------------------------------------- penyaring

    /**
     * Hanya baris yang berlaku, yaitu tanggal berakhir terjauh untuk tiap pasangan
     * kendaraan dan jenis. Ini yang membedakan "pajak kendaraan ini terlambat" dari
     * "pajak kendaraan ini tahun lalu memang sudah berakhir, dan sudah diperpanjang".
     */
    public function scopeBerlaku(Builder $query): Builder
    {
        return $query->whereRaw(
            'vehicle_documents.expires_at = ('
            .' select max(d2.expires_at) from vehicle_documents d2'
            .' where d2.vehicle_id = vehicle_documents.vehicle_id'
            .' and d2.type = vehicle_documents.type)'
        );
    }

    public function scopeJatuhTempo(Builder $query, int $hari = 30): Builder
    {
        return $query->berlaku()->where('expires_at', '<=', Carbon::today()->addDays($hari));
    }

    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->berlaku()->where('expires_at', '<', Carbon::today());
    }

    public function getAuditLabel(): string
    {
        return $this->jenisLabel().' '.($this->vehicle?->plate_number ?? '');
    }
}
