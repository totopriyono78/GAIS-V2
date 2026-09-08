<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Laporan kejadian keamanan.
 *
 * Keadaannya tidak disimpan sebagai kolom status, melainkan dibaca dari tiga hal yang memang
 * sudah ada: apakah sudah ditutup, apakah sudah punya tiket perbaikan, dan bagaimana keadaan
 * tiket itu. Uraian alasannya ada di migrasinya.
 */
class IncidentReport extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'incident_report';

    public const CATEGORIES = [
        'kehilangan' => 'Kehilangan barang',
        'kerusakan' => 'Kerusakan atau perusakan',
        'orang_asing' => 'Orang tidak dikenal',
        'kecelakaan' => 'Kecelakaan orang',
        'kebakaran' => 'Kebakaran atau nyaris terbakar',
        'lainnya' => 'Lainnya',
    ];

    public const SEVERITIES = [
        'rendah' => 'Ringan',
        'sedang' => 'Sedang',
        'tinggi' => 'Berat',
    ];

    protected $fillable = [
        'code',
        'occurred_at',
        'category',
        'severity',
        'location_id',
        'security_shift_id',
        'reported_by_staff_id',
        'description',
        'service_request_id',
        'closed_at',
        'closing_note',
        'closed_by_user_id',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (IncidentReport $insiden) {
            if (blank($insiden->code)) {
                $insiden->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($insiden->created_by_user_id)) {
                $insiden->created_by_user_id = Auth::id();
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(SecurityShift::class, 'security_shift_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(ServiceStaff::class, 'reported_by_staff_id');
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function severityLabel(): string
    {
        return self::SEVERITIES[$this->severity] ?? $this->severity;
    }

    public function severityColor(): string
    {
        return match ($this->severity) {
            'tinggi' => 'danger',
            'sedang' => 'warning',
            default => 'gray',
        };
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function punyaTiket(): bool
    {
        return $this->service_request_id !== null;
    }

    /**
     * Keadaan insiden ini, dibaca dari tiketnya dan dari kolom penutupnya.
     *
     * Tiga kemungkinan, dan urutannya penting. Insiden yang sudah ditutup berhenti di situ
     * meskipun tiketnya masih berjalan, karena menutup insiden adalah pernyataan bahwa urusan
     * keamanannya selesai, bukan bahwa perbaikannya selesai.
     */
    public function tindakLanjutLabel(): string
    {
        if ($this->isClosed()) {
            return 'Selesai ditangani';
        }

        if ($this->punyaTiket()) {
            $tiket = $this->serviceRequest;

            /*
             * Status tiketnya disalin apa adanya, tanpa dikecilkan hurufnya. Sempat ditulis
             * dengan mb_strtolower supaya menyambung mulus di tengah kalimat, dan hasilnya
             * "menunggu tim ga": singkatan GA ikut mengecil dan berhenti terbaca sebagai nama
             * bagian. Kalimat yang mengalir tidak sepadan dengan singkatan yang rusak.
             */
            return $tiket === null
                ? 'Tiketnya sudah dihapus'
                : 'Ditangani lewat '.$tiket->code.', '.$tiket->statusLabel();
        }

        return 'Menunggu tindak lanjut';
    }

    public function tindakLanjutColor(): string
    {
        return match (true) {
            $this->isClosed() => 'success',
            $this->punyaTiket() => 'info',
            default => 'warning',
        };
    }

    /** Judul tiket perbaikan yang akan lahir dari insiden ini. */
    public function usulanJudulTiket(): string
    {
        $tempat = $this->location?->name;

        return $this->categoryLabel().($tempat !== null ? ' di '.$tempat : '').', '.$this->code;
    }

    /**
     * Prioritas tiket yang diusulkan dari berat ringannya insiden.
     *
     * Bukan pemetaan satu lawan satu yang kaku: yang berat menjadi mendesak karena insiden
     * keamanan berat hampir selalu menyangkut orang, bukan hanya barang.
     */
    public function usulanPrioritasTiket(): string
    {
        return match ($this->severity) {
            'tinggi' => 'mendesak',
            'sedang' => 'tinggi',
            default => 'normal',
        };
    }

    /** Insiden yang belum ditutup dan belum punya tiket. */
    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->whereNull('closed_at')->whereNull('service_request_id');
    }

    public function getAuditLabel(): string
    {
        return $this->code;
    }
}
