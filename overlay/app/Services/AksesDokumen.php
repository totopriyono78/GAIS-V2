<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Mencatat siapa membuka dan mengunduh dokumen.
 *
 * Jejak audit yang sudah ada di GAIS mencatat siapa mengubah data. Untuk
 * dokumen itu tidak cukup: yang biasanya ditanyakan justru siapa saja yang
 * pernah membuka kontrak atau mengunduh dokumen berklasifikasi rahasia,
 * padahal mereka tidak mengubah apa pun.
 *
 * Ditulis lewat query builder, bukan lewat model, karena tabel ini bertambah
 * pada hampir setiap permintaan dan barisnya tidak pernah diubah maupun
 * dibaca sebagai model. Kuncinya pun gabungan id dan waktu, karena tabelnya
 * dipartisi, jadi memperlakukannya sebagai model berkunci tunggal justru
 * menyesatkan.
 */
class AksesDokumen
{
    public function catat(
        Document $dokumen,
        User $user,
        string $aksi,
        ?DocumentVersion $versi = null,
        ?string $ip = null,
    ): void {
        DB::table('document_access_log')->insert([
            'document_id' => $dokumen->id,
            'version_id' => $versi?->id,
            'user_id' => $user->id,
            'action' => $aksi,
            'ip_address' => $ip ?? request()?->ip(),
            'accessed_at' => now(),
        ]);
    }

    /**
     * Dipanggil sekali untuk satu pencarian, bukan sekali untuk tiap baris
     * hasilnya. Satu halaman hasil berisi dua puluh lima dokumen, dan mencatat
     * dua puluh lima baris untuk satu tindakan membuat tabelnya penuh oleh
     * kejadian yang tidak pernah ditanyakan siapa pun.
     *
     * @param  list<int>  $dokumenId
     */
    public function catatHasilPencarian(array $dokumenId, User $user): void
    {
        if ($dokumenId === []) {
            return;
        }

        $sekarang = now();
        $ip = request()?->ip();

        DB::table('document_access_log')->insert(array_map(
            static fn (int $id): array => [
                'document_id' => $id,
                'version_id' => null,
                'user_id' => $user->id,
                'action' => 'search_hit',
                'ip_address' => $ip,
                'accessed_at' => $sekarang,
            ],
            $dokumenId,
        ));
    }
}
