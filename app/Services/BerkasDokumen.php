<?php

namespace App\Services;

use App\Models\DocumentVersion;
use App\Support\Berkas;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Menyimpan berkas dokumen, menghitung sidik jarinya, dan mengenali unggahan
 * kembar.
 *
 * Dua aturan yang tidak bisa ditawar:
 *
 * 1. Nama asli berkas tidak dipakai sebagai nama di penyimpanan. Nama dari
 *    pemakai bisa mengandung karakter yang menyulitkan dan sering membocorkan
 *    isi dokumennya lewat judul. Nama aslinya disimpan di kolom tersendiri dan
 *    baru dipakai saat berkasnya diunduh.
 * 2. Berkas tidak pernah berada di disk publik. Seluruhnya di disk dokumen,
 *    yang hanya bisa dibaca lewat alamat bertanda tangan berumur pendek.
 */
class BerkasDokumen
{
    /**
     * @return array{path: string, hash: string, size: int, mime: string, original: string, disk: string}
     */
    public function simpan(UploadedFile $berkas, int $dokumenId, int $nomorVersi): array
    {
        $hash = hash_file('sha256', $berkas->getRealPath());

        if ($hash === false) {
            throw new RuntimeException('Sidik jari berkas gagal dihitung, jadi berkasnya tidak disimpan.');
        }

        $ext = strtolower($berkas->getClientOriginalExtension() ?: 'bin');

        // Disusun per tahun dan bulan supaya satu folder tidak pernah berisi
        // ratusan ribu berkas, yang membuat pencadangan dan penelusuran lambat.
        $jalur = sprintf(
            'dokumen/%s/%s/%d/v%d/%s.%s',
            date('Y'),
            date('m'),
            $dokumenId,
            $nomorVersi,
            Str::ulid()->toBase32(),
            $ext,
        );

        $tersimpan = Berkas::disk()->putFileAs(dirname($jalur), $berkas, basename($jalur));

        if ($tersimpan === false) {
            throw new RuntimeException('Berkas gagal disimpan ke penyimpanan dokumen.');
        }

        return [
            'path' => $jalur,
            'hash' => $hash,
            'size' => $berkas->getSize() ?: 0,
            'mime' => $berkas->getMimeType() ?: 'application/octet-stream',
            'original' => $berkas->getClientOriginalName(),
            'disk' => Berkas::DISK,
        ];
    }

    /**
     * Mencari versi lain yang isinya persis sama.
     *
     * Dipakai untuk menawarkan tautan ke dokumen yang sudah ada, bukan untuk
     * menolak unggahannya. Menyimpan salinan kedua berarti dua berkas yang
     * sejak saat itu bisa menyimpang tanpa ada yang tahu yang mana yang benar.
     */
    public function kembaran(string $hash): ?DocumentVersion
    {
        return DocumentVersion::query()->where('file_hash', $hash)->first();
    }

    public function hapus(DocumentVersion $versi): bool
    {
        return Berkas::disk()->delete($versi->storage_path);
    }

    public function ada(DocumentVersion $versi): bool
    {
        return Berkas::disk()->exists($versi->storage_path);
    }

    /**
     * Memindahkan berkas yang gagal pemeriksaan virus ke luar jangkauan.
     *
     * Dipindah, bukan dihapus, supaya masih bisa diperiksa kalau ternyata
     * pemeriksaannya yang salah menuduh.
     */
    public function karantina(DocumentVersion $versi): string
    {
        $tujuan = 'karantina/'.Str::ulid()->toBase32().'.bin';

        Berkas::disk()->move($versi->storage_path, $tujuan);

        return $tujuan;
    }
}
