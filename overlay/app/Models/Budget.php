<?php

namespace App\Models;

use App\Services\RealisasiBiaya;
use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu pagu anggaran: satu departemen, satu kategori, satu tahun.
 *
 * Realisasi tidak pernah menjadi kolom. Ia selalu dijumlahkan saat dibaca dari catatan
 * yang sudah ada, karena angka realisasi yang disimpan akan salah sejak perintah kerja
 * berikutnya ditutup.
 */
class Budget extends Model
{
    use Auditable;

    protected $fillable = [
        'department_id',
        'expense_category_id',
        'fiscal_year',
        'amount',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year' => 'integer',
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Budget $anggaran) {
            if (blank($anggaran->created_by_user_id)) {
                $anggaran->created_by_user_id = Auth::id();
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    // ---------------------------------------------------------------- turunan

    public function realisasi(): float
    {
        if ($this->category === null) {
            return 0.0;
        }

        return app(RealisasiBiaya::class)
            ->untuk($this->category, (int) $this->department_id, (int) $this->fiscal_year);
    }

    /**
     * Nilai yang sudah pasti keluar tetapi belum disetujui, dari faktur rekanan maupun
     * dari struk karyawan.
     *
     * Bukan realisasi, dan tidak pernah ikut dijumlahkan ke dalamnya. Angka ini ada karena
     * tumpukan faktur dan struk yang belum ditandatangani adalah cara termudah membuat pagu
     * terlihat sehat padahal uangnya sudah habis.
     */
    public function tertunda(): float
    {
        if ($this->category === null) {
            return 0.0;
        }

        return app(RealisasiBiaya::class)
            ->tertunda($this->category, (int) $this->department_id, (int) $this->fiscal_year);
    }

    public function tertundaLabel(): ?string
    {
        $tertunda = $this->tertunda();

        return $tertunda > 0
            ? Rupiah::penuh($tertunda).' lagi menunggu persetujuan'
            : null;
    }

    public function sisa(): float
    {
        return round((float) $this->amount - $this->realisasi(), 2);
    }

    /**
     * Sisa sebagai kalimat. Pagu yang terlewati ditulis "Lewat Rp 995.000", bukan
     * "Sisa Rp -995.000", karena sisa yang negatif bukan sisa, dan tanda minus di depan
     * rupiah adalah bentuk yang paling mudah terbaca salah saat halaman dibaca cepat.
     */
    public function sisaLabel(): string
    {
        $sisa = $this->sisa();

        return $sisa < 0
            ? 'Lewat '.Rupiah::penuh(abs($sisa))
            : 'Sisa '.Rupiah::penuh($sisa);
    }

    /** Persen terpakai. Null kalau pagunya nol, karena membagi dengan nol bukan 0 persen. */
    public function persenTerpakai(): ?float
    {
        $pagu = (float) $this->amount;

        if ($pagu <= 0) {
            return null;
        }

        return round($this->realisasi() / $pagu * 100, 1);
    }

    public function persenLabel(): string
    {
        $persen = $this->persenTerpakai();

        if ($persen === null) {
            return 'Pagunya nol';
        }

        return number_format($persen, 1, ',', '.').' persen';
    }

    /**
     * Keadaan pemakaian.
     *
     * Pagu yang nilainya nol tidak punya persen terpakai, dan menuliskannya sebagai nol
     * persen akan terbaca seolah departemen itu hemat padahal ia memang tidak pernah
     * diberi pagu.
     */
    public function keadaan(): string
    {
        $persen = $this->persenTerpakai();

        if ($persen === null) {
            return 'belum_dihitung';
        }

        return match (true) {
            $persen > 100 => 'lewat',
            $persen >= 85 => 'mendekati',
            default => 'aman',
        };
    }

    public function keadaanLabel(): string
    {
        return match ($this->keadaan()) {
            'lewat' => 'Melewati pagu',
            'mendekati' => 'Mendekati pagu',
            'aman' => 'Masih aman',
            default => 'Pagunya belum ditetapkan',
        };
    }

    public function keadaanColor(): string
    {
        return match ($this->keadaan()) {
            'lewat' => 'danger',
            'mendekati' => 'warning',
            'aman' => 'success',
            default => 'gray',
        };
    }

    // ---------------------------------------------------------------- penyaring

    public function scopeTahun(Builder $query, int $tahun): Builder
    {
        return $query->where('fiscal_year', $tahun);
    }

    public function getAuditLabel(): string
    {
        return ($this->department?->name ?? '').' '.($this->category?->name ?? '').' '.$this->fiscal_year;
    }
}
