<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Satu dokumen pelepasan aset.
 *
 * Menyimpan dokumen ini membuat status aset menjadi Sudah dilepas. Dengan begitu
 * status itu berhenti menjadi label yang bisa dipilih siapa saja di formulir aset,
 * dan menjadi akibat dari satu peristiwa yang ada tanggal, cara, dan dokumennya.
 *
 * Laba atau rugi pelepasan sengaja belum dihitung di sini. Angkanya memerlukan
 * nilai buku, dan nilai buku memerlukan penyusutan yang belum dibangun. Yang
 * ditampilkan sekarang hanya hasil pelepasan dan nilai perolehan apa adanya,
 * tanpa mengaku sebagai laba atau rugi.
 */
class AssetDisposal extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'asset_disposal';

    public const METHODS = [
        'dijual' => 'Dijual',
        'dihibahkan' => 'Dihibahkan',
        'tukar_tambah' => 'Tukar tambah',
        'dimusnahkan' => 'Dimusnahkan',
        'hilang' => 'Hilang',
    ];

    /** Cara pelepasan yang wajar menghasilkan uang. */
    public const METHODS_WITH_PROCEEDS = ['dijual', 'tukar_tambah'];

    protected $fillable = [
        'code',
        'asset_id',
        'disposal_date',
        'method',
        'proceeds',
        'counterparty',
        'reference',
        'previous_status',
        'approved_by_employee_id',
        'document_path',
        'reason',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'disposal_date' => 'date',
            'proceeds' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AssetDisposal $disposal) {
            if (blank($disposal->code)) {
                $disposal->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($disposal->created_by_user_id)) {
                $disposal->created_by_user_id = Auth::id();
            }

            // Status sebelumnya disimpan sebelum diubah, supaya pembatalan bisa
            // mengembalikan keadaan yang persis, bukan menebak dengan status aktif.
            $disposal->previous_status = $disposal->asset?->status;
        });

        static::created(function (AssetDisposal $disposal) {
            $disposal->asset?->forceFill(['status' => 'dilepas'])->save();
        });

        static::deleted(function (AssetDisposal $disposal) {
            $asset = $disposal->asset;

            if ($asset === null || $asset->status !== 'dilepas') {
                return;
            }

            $asset->forceFill(['status' => $disposal->previous_status ?: 'tidak_dipakai'])->save();
        });
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by_employee_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }

    public function methodColor(): string
    {
        return match ($this->method) {
            'dijual', 'tukar_tambah' => 'success',
            'dihibahkan' => 'primary',
            'hilang' => 'danger',
            default => 'warning',
        };
    }

    public static function expectsProceeds(?string $method): bool
    {
        return in_array($method, self::METHODS_WITH_PROCEEDS, true);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.($this->asset?->code ?? '');
    }
}
