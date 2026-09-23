<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\LinkRelation;
use App\Enums\ScanStatus;
use App\Enums\VersionStatus;
use App\Exceptions\KonflikVersi;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Seluruh logika dokumen berada di sini, bukan di layar yang memanggilnya.
 *
 * Alasannya dua. Modul lain bisa menitipkan dokumen tanpa menyalin satu baris
 * pun logika ini, dan kalau nanti ada layar lain, entah unggah massal atau
 * antarmuka untuk ponsel, ia memanggil lapisan yang sama sehingga aturannya
 * tidak mungkin berbeda antar layar.
 */
class PengelolaDokumen
{
    public function __construct(
        private readonly BerkasDokumen $berkas,
        private readonly SkemaMetadata $skema,
        private readonly TautanDokumen $tautan,
    ) {}

    /**
     * Membuat dokumen baru beserta versi pertamanya.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function buat(
        DocumentType $jenis,
        UploadedFile $file,
        string $judul,
        array $metadata,
        User $user,
        ?int $kategoriId = null,
        ?Model $tautkanKe = null,
        LinkRelation $relasi = LinkRelation::Lampiran,
        ?string $kerahasiaan = null,
        ?int $departemenId = null,
    ): Document {
        // Metadata diperiksa SEBELUM berkasnya disentuh. Kalau urutannya
        // dibalik, isian yang ditolak tetap meninggalkan berkas yatim di
        // penyimpanan, dan tidak ada baris apa pun yang menunjuknya lagi.
        $bersih = $this->skema->periksa($jenis, $metadata);

        return DB::transaction(function () use (
            $jenis, $file, $judul, $bersih, $user, $kategoriId, $tautkanKe, $relasi, $kerahasiaan, $departemenId
        ): Document {
            $dokumen = Document::query()->create([
                'document_type_id' => $jenis->id,
                'category_id' => $kategoriId,
                'document_number' => $this->nomor($jenis),
                'title' => $judul,
                'metadata' => $bersih,
                'confidentiality' => $kerahasiaan ?? 'internal',
                'status' => DocumentStatus::Aktif->value,
                'owner_department_id' => $departemenId ?? $user->departmentId(),
                'created_by_user_id' => $user->id,
            ]);

            $versi = $this->simpanVersi($dokumen, $file, $user, 1, null);

            /*
             * Jenis yang tidak butuh pengesahan langsung berlaku begitu
             * diunggah. Jenis yang butuh pengesahan menunggu, dan sampai alur
             * pengesahannya dibangun, dokumennya memang belum punya versi yang
             * berlaku. Itu keadaan yang sah dan sengaja tidak disamarkan:
             * menandai versi yang belum disahkan sebagai berlaku persis
             * kesalahan yang paling merugikan di modul seperti ini.
             */
            if (! $jenis->needs_approval) {
                $versi->update([
                    'status' => VersionStatus::Disahkan->value,
                    'effective_from' => now()->toDateString(),
                    'approved_by_user_id' => $user->id,
                    'approved_at' => now(),
                ]);

                $dokumen->update(['current_version_id' => $versi->id]);
            }

            if ($tautkanKe instanceof Model) {
                $this->tautan->tautkan($dokumen, $tautkanKe, $relasi, $user->id);
            }

            return $dokumen->refresh();
        });
    }

    /**
     * Menambah versi baru. Statusnya draf sampai disahkan.
     */
    public function tambahVersi(
        Document $dokumen,
        UploadedFile $file,
        string $catatanPerubahan,
        User $user,
    ): DocumentVersion {
        if (! $dokumen->type->is_versioned) {
            throw new RuntimeException(
                'Jenis dokumen '.$dokumen->type->name.' tidak memakai versi. '
                .'Lampiran dikoreksi lewat transaksinya, bukan direvisi sebagai versi baru.',
            );
        }

        return DB::transaction(function () use ($dokumen, $file, $catatanPerubahan, $user): DocumentVersion {
            // Baris dokumennya dikunci supaya dua unggahan bersamaan tidak
            // memperebutkan nomor versi yang sama. Constraint unik memang akan
            // menolak yang kedua, tetapi menolaknya di depan pemakai sebagai
            // pesan galat jauh lebih buruk daripada menunggu sepersekian detik.
            $terkunci = Document::query()->whereKey($dokumen->id)->lockForUpdate()->firstOrFail();

            return $this->simpanVersi(
                $terkunci,
                $file,
                $user,
                $terkunci->nomorVersiBerikutnya(),
                $catatanPerubahan,
            );
        });
    }

    /**
     * Mengesahkan sebuah versi dan menetapkannya sebagai yang berlaku.
     *
     * Urutannya wajib: tutup versi lama dulu, baru tetapkan yang baru. Kalau
     * dibalik, periode keduanya sesaat bertindih dan constraint menolaknya.
     */
    public function sahkan(
        DocumentVersion $versi,
        User $pengesah,
        ?Carbon $berlakuMulai = null,
    ): DocumentVersion {
        $tanggal = ($berlakuMulai ?? now())->toDateString();

        try {
            return DB::transaction(function () use ($versi, $pengesah, $tanggal): DocumentVersion {
                $dokumen = Document::query()->whereKey($versi->document_id)->lockForUpdate()->firstOrFail();

                /*
                 * Status versi lama sengaja TIDAK diubah menjadi ditarik.
                 *
                 * Constraint bekerja dengan rentang tanggal, jadi beberapa baris
                 * berstatus disahkan boleh hidup bersama selama periodenya tidak
                 * bertindih. Mengubah statusnya membuat pertanyaan "versi mana
                 * yang berlaku 15 Maret lalu" tidak bisa dijawab lagi, dan justru
                 * itu pertanyaan yang ditanyakan saat audit.
                 *
                 * Yang berubah cuma tanggal berakhirnya.
                 */
                DocumentVersion::query()
                    ->where('document_id', $dokumen->id)
                    ->where('status', VersionStatus::Disahkan->value)
                    ->whereNull('effective_until')
                    ->whereKeyNot($versi->id)
                    ->update([
                        'effective_until' => $tanggal,
                        'updated_at' => now(),
                    ]);

                $versi->update([
                    'status' => VersionStatus::Disahkan->value,
                    'effective_from' => $tanggal,
                    'approved_by_user_id' => $pengesah->id,
                    'approved_at' => now(),
                ]);

                $dokumen->update(['current_version_id' => $versi->id]);

                return $versi->refresh();
            });
        } catch (QueryException $e) {
            // Bukan kerusakan sistem. Ada pengesahan lain yang diproses
            // bersamaan dan sudah menempati periode itu.
            if ($e->getCode() === KonflikVersi::SQLSTATE || str_contains($e->getMessage(), 'satu_versi_berlaku')) {
                throw KonflikVersi::untukDokumen($versi->document_id);
            }

            throw $e;
        }
    }

    /**
     * Menarik versi yang berlaku tanpa menggantinya.
     *
     * Dipakai saat sebuah SOP dicabut dan penggantinya belum ada. Berbeda dari
     * pengesahan: setelah ini dokumennya tidak punya versi berlaku sama sekali,
     * dan itu keadaan yang sah. Di sinilah status ditarik memang tepat dipakai.
     */
    public function tarikTanpaPengganti(DocumentVersion $versi, ?Carbon $sampai = null): DocumentVersion
    {
        $tanggal = ($sampai ?? now())->toDateString();

        return DB::transaction(function () use ($versi, $tanggal): DocumentVersion {
            $versi->update([
                'effective_until' => $tanggal,
                'status' => VersionStatus::Ditarik->value,
            ]);

            Document::query()
                ->whereKey($versi->document_id)
                ->where('current_version_id', $versi->id)
                ->update(['current_version_id' => null]);

            return $versi->refresh();
        });
    }

    public function tolak(DocumentVersion $versi, User $user, string $alasan): DocumentVersion
    {
        $versi->update([
            'status' => VersionStatus::Ditolak->value,
            'change_note' => trim(($versi->change_note ?? '')."\n[Ditolak] ".$alasan),
            'approved_by_user_id' => $user->id,
            'approved_at' => now(),
        ]);

        return $versi->refresh();
    }

    /**
     * Mengarsipkan, bukan menghapus. Berkas dan metadatanya tetap ada, hanya
     * keluar dari daftar aktif.
     */
    public function arsipkan(Document $dokumen, string $alasan): Document
    {
        $dokumen->update([
            'status' => DocumentStatus::Diarsipkan->value,
            'archived_at' => now(),
            'description' => trim(($dokumen->description ?? '')."\n[Diarsipkan] ".$alasan),
        ]);

        return $dokumen->refresh();
    }

    public function kembalikanDariArsip(Document $dokumen): Document
    {
        $dokumen->update([
            'status' => DocumentStatus::Aktif->value,
            'archived_at' => null,
        ]);

        return $dokumen->refresh();
    }

    // ------------------------------------------------------------------ dalam

    /**
     * Nomor dokumen, diambil dari urutan nomor yang sudah dipakai seluruh GAIS.
     *
     * Lampiran tidak diberi nomor, dan itu bukan kelalaian: identitasnya
     * mengikuti transaksi induknya.
     */
    private function nomor(DocumentType $jenis): ?string
    {
        return $jenis->perluNomor() ? NumberGenerator::next($jenis->kodeUrutan()) : null;
    }

    private function simpanVersi(
        Document $dokumen,
        UploadedFile $file,
        User $user,
        int $nomorVersi,
        ?string $catatanPerubahan,
    ): DocumentVersion {
        $tersimpan = $this->berkas->simpan($file, $dokumen->id, $nomorVersi);

        return DocumentVersion::query()->create([
            'document_id' => $dokumen->id,
            'version_number' => $nomorVersi,
            'storage_disk' => $tersimpan['disk'],
            'storage_path' => $tersimpan['path'],
            'file_hash' => $tersimpan['hash'],
            'file_size' => $tersimpan['size'],
            'mime_type' => $tersimpan['mime'],
            'original_name' => $tersimpan['original'],
            'status' => VersionStatus::Draf->value,
            /*
             * Pemeriksaan virus belum dipasang di lingkungan ini, jadi statusnya
             * ditulis apa adanya sebagai tidak diperiksa, bukan sebagai bersih.
             *
             * Bedanya bukan soal kata. Menandainya bersih berarti layar akan
             * menyatakan sesuatu yang tidak pernah diperiksa siapa pun, dan
             * itu jenis kebohongan yang baru ketahuan pada hari terburuk.
             * Menandainya menunggu juga salah, karena tidak ada yang akan
             * datang memeriksanya, dan berkasnya akan tertahan selamanya.
             */
            'scan_status' => ScanStatus::Dilewati->value,
            'change_note' => $catatanPerubahan,
            'uploaded_by_user_id' => $user->id,
        ]);
    }
}
