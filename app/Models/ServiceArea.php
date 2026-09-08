<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Area yang dibersihkan dan diperiksa.
 *
 * Seberapa sering dan siapa penanggung jawabnya jadi kolom di sini, bukan tabel jadwal
 * tersendiri. Uraian alasannya ada di migrasinya.
 */
class ServiceArea extends Model
{
    use Auditable;

    public const CATEGORIES = [
        'toilet' => 'Toilet',
        'ruang_kerja' => 'Ruang kerja',
        'ruang_rapat' => 'Ruang rapat',
        'lobi' => 'Lobi dan resepsionis',
        'pantry' => 'Pantry',
        'koridor' => 'Koridor dan tangga',
        'halaman' => 'Halaman dan parkir',
        'gudang' => 'Gudang',
        'lainnya' => 'Lainnya',
    ];

    public const FREQUENCIES = [
        'harian' => 'Setiap hari',
        'mingguan' => 'Seminggu sekali',
        'bulanan' => 'Sebulan sekali',
    ];

    protected $fillable = [
        'code',
        'name',
        'category',
        'location_id',
        'frequency',
        'service_staff_id',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ---------------------------------------------------------------- relasi

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(ServiceStaff::class, 'service_staff_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function frequencyLabel(): string
    {
        return self::FREQUENCIES[$this->frequency] ?? $this->frequency;
    }

    public function penanggungJawabLabel(): string
    {
        return $this->staff?->namaLengkap() ?? 'Belum ditentukan';
    }

    /** Letaknya, dalam satu kalimat. Kosong kalau areanya tidak ditautkan ke lokasi mana pun. */
    public function letakLabel(): ?string
    {
        return $this->location?->full_path;
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
