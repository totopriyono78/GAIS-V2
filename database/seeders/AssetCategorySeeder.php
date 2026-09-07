<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

/**
 * Kelas aset yang lazim dipakai kantor, disusun mengikuti pengelompokan aset tetap
 * pada PSAK 216 dan dipetakan ke kelompok harta berwujud untuk penyusutan.
 *
 * Masa manfaat tidak ditulis di sini. Angkanya diturunkan dari kolom kelompok pajak
 * oleh model, memakai tabel PMK 72 Tahun 2023: kelompok 1 sampai 4 masing masing
 * 4, 8, 16, dan 20 tahun, bangunan permanen 20 tahun, bangunan tidak permanen 10 tahun.
 *
 * Nomor akun COA sengaja dibiarkan kosong. Bagan akun berbeda di tiap perusahaan,
 * dan nomor akun ini ikut tercetak di stiker aset, jadi menebaknya berisiko.
 * Kategori baru bisa dipakai setelah tim finance mengisi nomor akunnya.
 *
 * Penempatan kelompok pajak di bawah mengikuti praktik yang umum, tetapi tetap perlu
 * dikonfirmasi tim pajak perusahaan karena bisa berbeda menurut jenis usaha.
 */
class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'TNH',
                'name' => 'Tanah',
                'description' => 'Tanah milik perusahaan. Tidak disusutkan.',
                'tax_group' => 'tidak_disusutkan',
                'depreciation_method' => 'tidak_disusutkan',
            ],
            [
                'code' => 'BGN',
                'name' => 'Bangunan permanen',
                'description' => 'Gedung kantor dan bangunan permanen lainnya.',
                'tax_group' => 'bangunan_permanen',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'BGT',
                'name' => 'Bangunan tidak permanen',
                'description' => 'Bangunan sementara, misalnya gudang semi permanen.',
                'tax_group' => 'bangunan_tidak_permanen',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'PRS',
                'name' => 'Prasarana dan instalasi',
                'description' => 'Instalasi listrik, air, jaringan, dan pekerjaan renovasi yang melekat pada bangunan.',
                'tax_group' => 'kelompok_2',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'KR4',
                'name' => 'Kendaraan roda empat',
                'description' => 'Mobil operasional, bus, dan truk.',
                'tax_group' => 'kelompok_2',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'KR2',
                'name' => 'Kendaraan roda dua',
                'description' => 'Sepeda motor operasional.',
                'tax_group' => 'kelompok_1',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'PKY',
                'name' => 'Perabot kantor kayu',
                'description' => 'Meja, kursi, lemari, dan perabot berbahan kayu atau rotan yang bukan bagian bangunan.',
                'tax_group' => 'kelompok_1',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'PLG',
                'name' => 'Perabot kantor logam',
                'description' => 'Meja, kursi, lemari, dan filing cabinet berbahan logam yang bukan bagian bangunan.',
                'tax_group' => 'kelompok_2',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'KOM',
                'name' => 'Komputer dan perangkat jaringan',
                'description' => 'Komputer, laptop, printer, pemindai, server, dan perangkat jaringan.',
                'tax_group' => 'kelompok_1',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'MSK',
                'name' => 'Mesin dan peralatan kantor',
                'description' => 'Mesin fotokopi, mesin penghancur kertas, proyektor, dan peralatan kantor sejenis.',
                'tax_group' => 'kelompok_1',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'ALK',
                'name' => 'Alat komunikasi',
                'description' => 'Pesawat telepon, faksimile, telepon seluler, dan radio komunikasi.',
                'tax_group' => 'kelompok_1',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'PGU',
                'name' => 'Peralatan pengatur udara',
                'description' => 'AC, kipas angin, dan peralatan pengatur udara lainnya.',
                'tax_group' => 'kelompok_2',
                'depreciation_method' => 'garis_lurus',
            ],
            [
                'code' => 'ADP',
                'name' => 'Aset dalam penyelesaian',
                'description' => 'Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan sampai dipindahkan ke kelas yang sesuai.',
                'tax_group' => 'tidak_disusutkan',
                'depreciation_method' => 'tidak_disusutkan',
            ],
        ];

        foreach ($categories as $category) {
            AssetCategory::query()->updateOrCreate(
                ['code' => $category['code']],
                $category + ['is_active' => true],
            );
        }

        $this->command?->info('Kelas aset standar dimasukkan.');
        $this->command?->warn('Nomor akun COA masih kosong. Kategori belum bisa dipakai mencatat aset sampai tim finance mengisinya di menu Kategori aset.');
    }
}
