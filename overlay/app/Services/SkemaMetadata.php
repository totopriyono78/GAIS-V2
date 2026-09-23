<?php

namespace App\Services;

use App\Enums\MetadataFieldType;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Menerjemahkan skema metadata sebuah jenis dokumen menjadi aturan validasi.
 *
 * Satu definisi skema dipakai untuk tiga hal, dan kelas ini memegang dua di
 * antaranya: memvalidasi isian dan menormalkan bentuk simpannya. Yang ketiga,
 * membangun form, dikerjakan layar yang membacanya.
 *
 * Metode membangun aturannya sengaja tidak menyentuh basis data sama sekali,
 * jadi seluruh perilakunya bisa diperiksa tanpa menjalankan aplikasi.
 */
class SkemaMetadata
{
    /**
     * Aturan validasi Laravel untuk satu skema.
     *
     * @param  array<string, array<string, mixed>>  $skema
     * @return array<string, list<mixed>>
     */
    public function aturan(array $skema): array
    {
        $aturan = [];

        foreach ($skema as $field => $definisi) {
            $tipe = MetadataFieldType::tryFrom($definisi['type'] ?? '') ?? MetadataFieldType::Teks;

            $baris = [($definisi['required'] ?? false) ? 'required' : 'nullable'];

            $baris = [...$baris, ...match ($tipe) {
                MetadataFieldType::Teks => ['string', 'max:255'],
                MetadataFieldType::TeksPanjang => ['string', 'max:10000'],
                MetadataFieldType::Angka => ['numeric'],
                MetadataFieldType::Tanggal => ['date'],
                MetadataFieldType::YaTidak => ['boolean'],
                // Rule::in, bukan string 'in:a,b,c'. Pilihan yang mengandung
                // koma akan terpecah jadi dua aturan kalau ditulis sebagai teks,
                // dan isian yang benar justru ditolak.
                MetadataFieldType::Pilihan => ['string', Rule::in($definisi['options'] ?? [])],
                MetadataFieldType::Relasi => ['integer', 'min:1'],
            }];

            if ($tipe === MetadataFieldType::Angka) {
                if (isset($definisi['min'])) {
                    $baris[] = 'min:'.$definisi['min'];
                }

                if (isset($definisi['max'])) {
                    $baris[] = 'max:'.$definisi['max'];
                }
            }

            $aturan[$field] = $baris;
        }

        return $aturan;
    }

    /**
     * Label yang dipakai pada pesan galat, diambil dari skema.
     *
     * Tanpa ini pesannya berbunyi "The nomor_kontrak field is required", dan
     * yang membacanya adalah staf GA, bukan yang menulis skemanya.
     *
     * @param  array<string, array<string, mixed>>  $skema
     * @return array<string, string>
     */
    public function namaField(array $skema): array
    {
        $nama = [];

        foreach ($skema as $field => $definisi) {
            $nama[$field] = $definisi['label'] ?? str_replace('_', ' ', $field);
        }

        return $nama;
    }

    /**
     * Membuang field yang tidak dikenal skema.
     *
     * Ini penting dan mudah dilewatkan. Tanpa penyaringan ini, kolom metadata
     * perlahan terisi sisa dari form versi lama yang fieldnya sudah dihapus,
     * dan filter yang membaca kolom itu tidak lagi bisa dipercaya.
     *
     * @param  array<string, array<string, mixed>>  $skema
     * @param  array<string, mixed>  $isian
     * @return array<string, mixed>
     */
    public function buangYangTakDikenal(array $skema, array $isian): array
    {
        return array_intersect_key($isian, $skema);
    }

    /**
     * Menyeragamkan bentuk nilai sebelum disimpan.
     *
     * Tanpa ini, satu angka bisa tersimpan sebagai teks "250000" di satu
     * dokumen dan sebagai angka 250000 di dokumen lain. Keduanya terlihat sama
     * di layar, tetapi filter metadata membandingkan bentuk simpannya, jadi
     * salah satunya tidak akan pernah muncul di hasil penyaringan.
     *
     * @param  array<string, array<string, mixed>>  $skema
     * @param  array<string, mixed>  $isian
     * @return array<string, mixed>
     */
    public function seragamkan(array $skema, array $isian): array
    {
        $hasil = [];

        foreach ($isian as $field => $nilai) {
            $tipe = MetadataFieldType::tryFrom($skema[$field]['type'] ?? '') ?? MetadataFieldType::Teks;

            if ($nilai === null || $nilai === '') {
                $hasil[$field] = null;

                continue;
            }

            $hasil[$field] = match ($tipe) {
                MetadataFieldType::Angka => is_numeric($nilai) ? $nilai + 0 : $nilai,
                MetadataFieldType::Relasi => (int) $nilai,
                MetadataFieldType::YaTidak => filter_var($nilai, FILTER_VALIDATE_BOOLEAN),
                MetadataFieldType::Tanggal => is_string($nilai) ? substr($nilai, 0, 10) : $nilai,
                default => (string) $nilai,
            };
        }

        return $hasil;
    }

    /**
     * Jalur lengkap: buang yang tidak dikenal, validasi, lalu seragamkan.
     *
     * Melempar ValidationException bawaan Laravel, bukan pengecualian sendiri,
     * supaya pesan galatnya muncul menempel di field yang salah pada form
     * Filament tanpa penanganan tambahan.
     *
     * @param  array<string, mixed>  $isian
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function periksa(DocumentType $jenis, array $isian): array
    {
        $skema = $jenis->metadata_schema ?? [];
        $bersih = $this->buangYangTakDikenal($skema, $isian);

        Validator::make($bersih, $this->aturan($skema), [], $this->namaField($skema))->validate();

        return $this->seragamkan($skema, $bersih);
    }
}
