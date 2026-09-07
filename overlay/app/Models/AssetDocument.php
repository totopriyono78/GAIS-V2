<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Berkas pendukung satu aset: kartu garansi, buku manual, faktur, kontrak sewa,
 * sertifikat, dan lainnya.
 *
 * Dipisah dari kolom di tabel aset karena jumlahnya tidak tetap. Satu aset bisa
 * punya satu berkas, bisa juga tujuh, dan menaruhnya sebagai kolom berarti
 * menebak berapa banyak yang akan dibutuhkan orang.
 */
class AssetDocument extends Model
{
    use Auditable;

    public const TYPES = [
        'kartu_garansi' => 'Kartu garansi',
        'manual_book' => 'Buku manual',
        'faktur' => 'Faktur atau kuitansi',
        'kontrak_sewa' => 'Kontrak sewa',
        'sertifikat' => 'Sertifikat',
        'berita_acara' => 'Berita acara',
        'foto' => 'Foto',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'asset_id',
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
        static::creating(function (AssetDocument $document) {
            if (blank($document->uploaded_by_user_id)) {
                $document->uploaded_by_user_id = Auth::id();
            }
        });

        /*
         * Ukuran berkas dibaca dari cakram, bukan dari yang dilaporkan peramban, dan
         * dibaca di model supaya jalur apa pun yang menyimpan dokumen ikut terisi.
         */
        static::saving(function (AssetDocument $document) {
            if (! $document->isDirty('file_path') && $document->size_bytes !== null) {
                return;
            }

            $document->size_bytes = (filled($document->file_path) && Storage::disk('public')->exists($document->file_path))
                ? Storage::disk('public')->size($document->file_path)
                : null;
        });

        // Berkas fisik ikut dibuang, supaya cakram tidak penuh berkas yatim yang
        // tidak lagi bisa dibuka dari layar mana pun.
        static::deleted(function (AssetDocument $document) {
            if (filled($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
        });
    }

    /**
     * Nama berkas asli selalu disimpan sebagai satu teks.
     *
     * Komponen unggah Filament mengembalikan nama berkas dalam bentuk yang berbeda
     * tergantung apakah kolomnya menerima satu berkas atau banyak: satu teks untuk
     * yang tunggal, larik berkunci jalur berkas untuk yang jamak. Kolom di basis data
     * ini hanya menampung teks. Perata di sini membuat kolomnya tetap terisi benar
     * meskipun bentuk kiriman berubah, tanpa perlu menebak versi Filament.
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

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
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
        return $this->name.' ('.($this->asset?->code ?? '').')';
    }
}
