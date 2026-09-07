<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Lampiran perintah kerja: foto kerusakan, foto hasil, nota bengkel, faktur.
 *
 * Bentuknya sengaja sama persis dengan dokumen pendukung aset, termasuk cara menyimpan
 * ukuran dan membuang berkas fisiknya. Dua hal yang mirip sebaiknya juga berperilaku
 * mirip, supaya orang yang sudah paham satu layar langsung paham layar satunya.
 */
class WorkOrderAttachment extends Model
{
    use Auditable;

    public const TYPES = [
        'foto_kerusakan' => 'Foto kerusakan',
        'foto_hasil' => 'Foto hasil perbaikan',
        'nota' => 'Nota atau kuitansi',
        'faktur' => 'Faktur vendor',
        'berita_acara' => 'Berita acara',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'work_order_id',
        'type',
        'name',
        'file_path',
        'original_name',
        'size_bytes',
        'notes',
        'uploaded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WorkOrderAttachment $lampiran) {
            if (blank($lampiran->uploaded_by_user_id)) {
                $lampiran->uploaded_by_user_id = Auth::id();
            }
        });

        static::saving(function (WorkOrderAttachment $lampiran) {
            if (! $lampiran->isDirty('file_path') && $lampiran->size_bytes !== null) {
                return;
            }

            $lampiran->size_bytes = (filled($lampiran->file_path) && Storage::disk('public')->exists($lampiran->file_path))
                ? Storage::disk('public')->size($lampiran->file_path)
                : null;
        });

        static::deleted(function (WorkOrderAttachment $lampiran) {
            if (filled($lampiran->file_path)) {
                Storage::disk('public')->delete($lampiran->file_path);
            }
        });
    }

    /** Sama seperti dokumen aset: nama berkas asli selalu tersimpan sebagai satu teks. */
    protected function originalName(): Attribute
    {
        return Attribute::set(function (mixed $nilai): ?string {
            if (is_array($nilai)) {
                $nilai = reset($nilai);
            }

            return blank($nilai) ? null : (string) $nilai;
        });
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function sizeLabel(): string
    {
        $bytes = (int) $this->size_bytes;

        if ($bytes <= 0) {
            return 'Tidak diketahui';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 0, ',', '.').' KB';
        }

        return number_format($bytes / 1024 / 1024, 1, ',', '.').' MB';
    }

    public function getAuditLabel(): string
    {
        return $this->name.' ('.($this->workOrder?->code ?? '').')';
    }
}
