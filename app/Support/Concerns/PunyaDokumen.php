<?php

namespace App\Support\Concerns;

use App\Enums\LinkRelation;
use App\Models\Document;
use App\Models\DocumentType;
use App\Services\PengelolaDokumen;
use App\Services\TautanDokumen;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Dipasang di model mana pun yang perlu menyimpan lampiran.
 *
 * Inilah bentuk nyata dari lemari bersama di tingkat kode: model yang memakai
 * trait ini tidak butuh tabel lampiran sendiri, tidak butuh kolom file_path,
 * dan tidak menyalin satu baris pun logika penyimpanan berkas. Yang ia dapat
 * bukan cuma tempat menaruh berkas, melainkan sekalian masa simpan, jejak
 * akses, dan pemeriksaan kerahasiaan yang sudah ada di modul dokumen.
 *
 * Sepuluh tabel lampiran yang sudah berjalan di GAIS sengaja belum dipindahkan
 * ke sini. Modul baru memakai trait ini, yang lama dicicil setelah modul
 * dokumen terbukti stabil, dan tabel lamanya tidak dihapus sampai pemindahannya
 * selesai diperiksa.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait PunyaDokumen
{
    public function documents(): MorphToMany
    {
        return $this->morphToMany(Document::class, 'linkable', 'document_links')
            ->withPivot(['relation', 'created_by_user_id'])
            ->withTimestamps();
    }

    /**
     * @return Collection<int, Document>
     */
    public function dokumenBerelasi(?LinkRelation $relasi = null): Collection
    {
        return app(TautanDokumen::class)->dokumenMilik($this, $relasi);
    }

    /**
     * Mengunggah berkas baru sebagai dokumen sekaligus menautkannya ke sini.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function lampirkanDokumen(
        UploadedFile $file,
        string $kodeJenis,
        string $judul,
        array $metadata = [],
        LinkRelation $relasi = LinkRelation::Lampiran,
    ): Document {
        $jenis = DocumentType::query()->where('code', $kodeJenis)->firstOrFail();

        return app(PengelolaDokumen::class)->buat(
            jenis: $jenis,
            file: $file,
            judul: $judul,
            metadata: $metadata,
            user: Auth::user(),
            tautkanKe: $this,
            relasi: $relasi,
        );
    }

    /**
     * Menautkan dokumen yang SUDAH ada, tanpa menggandakan berkasnya.
     *
     * Dipakai saat satu kontrak relevan ke beberapa tempat sekaligus. Inilah
     * yang membuat struktur folder tidak dibutuhkan: dokumennya satu, dan ia
     * bisa muncul di banyak layar tanpa ada salinan yang bisa menyimpang.
     */
    public function tautkanDokumen(Document $dokumen, LinkRelation $relasi = LinkRelation::Lampiran): void
    {
        app(TautanDokumen::class)->tautkan($dokumen, $this, $relasi);
    }
}
