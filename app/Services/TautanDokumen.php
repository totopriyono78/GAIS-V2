<?php

namespace App\Services;

use App\Enums\LinkRelation;
use App\Models\Document;
use App\Models\DocumentLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Menautkan dokumen ke catatan di modul mana pun.
 *
 * Inilah yang membuat modul dokumen menjadi lemari bersama. Layar Vendor, Aset,
 * atau Kendaraan tetap menampilkan daftar lampirannya seperti biasa, hanya
 * sumber datanya yang jadi satu, beserta aturan masa simpan dan jejak aksesnya.
 */
class TautanDokumen
{
    public function tautkan(
        Document $dokumen,
        Model $pemilik,
        LinkRelation $relasi = LinkRelation::Lampiran,
        ?int $userId = null,
    ): DocumentLink {
        return DocumentLink::query()->firstOrCreate(
            [
                'document_id' => $dokumen->id,
                'linkable_type' => $pemilik->getMorphClass(),
                'linkable_id' => $pemilik->getKey(),
                'relation' => $relasi->value,
            ],
            ['created_by_user_id' => $userId ?? Auth::id()],
        );
    }

    /**
     * Melepas tautan, bukan menghapus dokumennya.
     *
     * Dokumen yang tidak lagi tertaut ke mana pun tetap ada di lemari dan tetap
     * tunduk pada masa simpannya. Menghapus berkasnya adalah tindakan lain yang
     * perlu berita acara, bukan efek samping dari melepas satu tautan.
     */
    public function lepas(
        Document $dokumen,
        Model $pemilik,
        LinkRelation $relasi = LinkRelation::Lampiran,
    ): bool {
        return DocumentLink::query()
            ->where('document_id', $dokumen->id)
            ->where('linkable_type', $pemilik->getMorphClass())
            ->where('linkable_id', $pemilik->getKey())
            ->where('relation', $relasi->value)
            ->delete() > 0;
    }

    /**
     * @return Collection<int, Document>
     */
    public function dokumenMilik(Model $pemilik, ?LinkRelation $relasi = null): Collection
    {
        return Document::query()
            ->whereHas('links', function ($q) use ($pemilik, $relasi) {
                $q->where('linkable_type', $pemilik->getMorphClass())
                    ->where('linkable_id', $pemilik->getKey());

                if ($relasi !== null) {
                    $q->where('relation', $relasi->value);
                }
            })
            ->with(['type', 'currentVersion'])
            ->get();
    }
}
