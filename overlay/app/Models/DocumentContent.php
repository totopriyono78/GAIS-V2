<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Isi berkas dalam bentuk teks, untuk pencarian.
 *
 * Belum diisi apa pun sampai pembacaan isi berkas dikerjakan. Tabelnya sudah
 * ada supaya strukturnya tidak perlu diubah lagi saat dokumennya sudah ribuan.
 *
 * Kolom search_vector dibangkitkan PostgreSQL sendiri dan tidak boleh ditulis
 * dari PHP, karena itu ia tidak ada di fillable maupun di casts.
 */
class DocumentContent extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'document_version_id';

    protected $fillable = [
        'document_version_id',
        'body',
        'extraction_method',
        'ocr_confidence',
        'page_count',
        'extracted_at',
    ];

    protected function casts(): array
    {
        return [
            'ocr_confidence' => 'decimal:2',
            'page_count' => 'integer',
            'extracted_at' => 'datetime',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'document_version_id');
    }

    /**
     * Hasil pembacaan otomatis membuat dokumen bisa ditemukan, bukan membuat
     * isinya bisa dipercaya. Di bawah angka ini, hasilnya perlu ditinjau orang
     * sebelum dipakai mengisi apa pun.
     */
    public function perluDitinjau(): bool
    {
        return $this->extraction_method === 'ocr'
            && $this->ocr_confidence !== null
            && (float) $this->ocr_confidence < 85;
    }
}
