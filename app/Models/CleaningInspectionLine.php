<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Satu area yang diperiksa dalam satu putaran.
 *
 * Hasilnya tiga tingkat, bukan dua. Bersih dan kotor saja memaksa pengawas memilih antara
 * memaafkan dan menghukum, dan yang paling sering ia temui justru ada di tengah: sudah disapu
 * tetapi tempat sampahnya belum dikosongkan. Tanpa tingkat tengah, temuan seperti itu akan
 * dicatat sebagai bersih supaya tidak terasa berlebihan, dan lembar pemeriksaan berhenti
 * menerangkan keadaan sebenarnya.
 */
class CleaningInspectionLine extends Model
{
    use Auditable;

    public const RESULTS = [
        'bersih' => 'Bersih',
        'kurang' => 'Kurang rapi',
        'kotor' => 'Kotor',
    ];

    protected $fillable = [
        'cleaning_inspection_id',
        'service_area_id',
        'area_code',
        'area_name',
        'staff_name',
        'result',
        'checked',
        'notes',
        'file_path',
        'original_name',
    ];

    protected function casts(): array
    {
        return [
            'checked' => 'boolean',
        ];
    }

    // ---------------------------------------------------------------- relasi

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(CleaningInspection::class, 'cleaning_inspection_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(ServiceArea::class, 'service_area_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function hasilLabel(): string
    {
        if (! $this->checked || $this->result === null) {
            return 'Belum diperiksa';
        }

        return self::RESULTS[$this->result] ?? $this->result;
    }

    public function hasilColor(): string
    {
        return match (true) {
            ! $this->checked || $this->result === null => 'gray',
            $this->result === 'bersih' => 'success',
            $this->result === 'kurang' => 'warning',
            default => 'danger',
        };
    }

    /** Temuan, yaitu hasil yang perlu ditindaklanjuti. */
    public function isTemuan(): bool
    {
        return in_array($this->result, ['kurang', 'kotor'], true);
    }

    public function petugasLabel(): string
    {
        return $this->staff_name ?? 'Tanpa penanggung jawab';
    }

    public function fotoUrl(): ?string
    {
        return filled($this->file_path)
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    /**
     * Menghapus fotonya dari penyimpanan.
     *
     * Kolomnya sendiri tidak ikut dikosongkan di sini, karena pemanggilnya sering perlu
     * mengubah beberapa kolom sekaligus dalam satu penyimpanan.
     */
    public function hapusFoto(): void
    {
        if (blank($this->file_path)) {
            return;
        }

        Storage::disk('public')->delete($this->file_path);
    }

    public function getAuditLabel(): string
    {
        return ($this->inspection?->code ?? 'pemeriksaan').' '.$this->area_name;
    }
}
