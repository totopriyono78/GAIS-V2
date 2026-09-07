<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Foto yang dilampirkan pemohon saat membuka tiket.
 *
 * Bentuknya sama persis dengan lampiran perintah kerja dan dokumen aset, sengaja: tiga
 * hal yang mirip sebaiknya juga berperilaku mirip, supaya orang yang sudah paham satu
 * layar langsung paham dua layar lainnya.
 */
class ServiceRequestAttachment extends Model
{
    use Auditable;

    protected $fillable = [
        'service_request_id',
        'name',
        'file_path',
        'original_name',
        'size_bytes',
        'uploaded_by_user_id',
    ];

    protected function casts(): array
    {
        return ['size_bytes' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceRequestAttachment $lampiran) {
            if (blank($lampiran->uploaded_by_user_id)) {
                $lampiran->uploaded_by_user_id = Auth::id();
            }
        });

        static::saving(function (ServiceRequestAttachment $lampiran) {
            if (! $lampiran->isDirty('file_path') && $lampiran->size_bytes !== null) {
                return;
            }

            $lampiran->size_bytes = (filled($lampiran->file_path) && Storage::disk('public')->exists($lampiran->file_path))
                ? Storage::disk('public')->size($lampiran->file_path)
                : null;
        });

        static::deleted(function (ServiceRequestAttachment $lampiran) {
            if (filled($lampiran->file_path)) {
                Storage::disk('public')->delete($lampiran->file_path);
            }
        });
    }

    protected function originalName(): Attribute
    {
        return Attribute::set(function (mixed $nilai): ?string {
            if (is_array($nilai)) {
                $nilai = reset($nilai);
            }

            return blank($nilai) ? null : (string) $nilai;
        });
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
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
        return $this->name.' ('.($this->serviceRequest?->code ?? '').')';
    }
}
