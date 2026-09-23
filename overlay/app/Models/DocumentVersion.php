<?php

namespace App\Models;

use App\Enums\ScanStatus;
use App\Enums\VersionStatus;
use App\Support\Berkas;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Satu versi dokumen, berikut berkasnya.
 *
 * Status versi lama sengaja TIDAK diubah menjadi ditarik saat versi baru
 * disahkan. Constraint satu_versi_berlaku bekerja dengan rentang tanggal, jadi
 * beberapa baris berstatus disahkan boleh hidup bersama selama periodenya tidak
 * bertindih. Kalau status versi lama diubah, pertanyaan "versi mana yang
 * berlaku 15 Maret lalu" tidak bisa dijawab lagi, dan justru itu pertanyaan
 * yang ditanyakan saat audit.
 *
 * Status ditarik hanya dipakai saat dokumen ditarik peredarannya tanpa ada
 * penggantinya.
 */
class DocumentVersion extends Model
{
    use Auditable;

    protected $fillable = [
        'document_id',
        'version_number',
        'storage_disk',
        'storage_path',
        'file_hash',
        'file_size',
        'mime_type',
        'original_name',
        'status',
        'effective_from',
        'effective_until',
        'change_note',
        'scan_status',
        'uploaded_by_user_id',
        'approved_by_user_id',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => VersionStatus::class,
            'scan_status' => ScanStatus::class,
            'version_number' => 'integer',
            'file_size' => 'integer',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function content(): HasOne
    {
        return $this->hasOne(DocumentContent::class, 'document_version_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function scopeDisahkan(Builder $query): Builder
    {
        return $query->where('status', VersionStatus::Disahkan->value);
    }

    public function bolehDiunduh(): bool
    {
        return $this->scan_status->bolehDiunduh();
    }

    public function berlakuPada(string $tanggal): bool
    {
        if ($this->status !== VersionStatus::Disahkan || $this->effective_from === null) {
            return false;
        }

        $mulai = $this->effective_from->toDateString();
        $sampai = $this->effective_until?->toDateString();

        return $tanggal >= $mulai && ($sampai === null || $tanggal < $sampai);
    }

    /** Alamat berumur pendek untuk mengunduh berkas versi ini. */
    public function url(): ?string
    {
        return $this->bolehDiunduh() ? Berkas::url($this->storage_path) : null;
    }

    public function ukuranTerbaca(): string
    {
        $byte = (float) $this->file_size;
        $satuan = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($byte >= 1024 && $i < count($satuan) - 1) {
            $byte /= 1024;
            $i++;
        }

        return round($byte, 1).' '.$satuan[$i];
    }

    /**
     * PDF dan gambar bisa dipratinjau apa adanya. Berkas Office baru bisa
     * setelah layanan konversi dipasang, dan itu pekerjaan fase berikutnya.
     */
    public function bisaDipratinjau(): bool
    {
        return $this->mime_type === 'application/pdf'
            || str_starts_with($this->mime_type, 'image/');
    }

    public function getAuditLabel(): string
    {
        return ($this->document?->title ?? 'Dokumen').' versi '.$this->version_number;
    }
}
