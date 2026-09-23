<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

/**
 * Struktur kategori awal: delapan kategori induk beserta anaknya.
 *
 * Kategori adalah tempat menaruh dokumen, jenis dokumen adalah apa dokumennya.
 * Keduanya terpisah, dan karena itu nama kategori di sini sengaja tidak selalu
 * sama dengan nama jenis dokumen.
 *
 * Kedalamannya dua tingkat dan berhenti di situ. Hierarki yang lebih dalam
 * membuat izin sulit diaudit dan memaksa orang menggandakan dokumen yang
 * relevan ke dua tempat. Pemakai yang tetap menginginkan rasa folder dilayani
 * filter tersimpan, bukan dengan menambah tingkat di sini.
 *
 * Konvensi kodenya: induk memakai satu segmen seperti tata-kelola, anak memakai
 * kode induk ditambah titik dan segmennya sendiri seperti tata-kelola.sop. Kode
 * inilah yang dipakai modul lain untuk merujuk kategori, jadi kode tidak boleh
 * diubah setelah terpakai. Kalau labelnya perlu disesuaikan, ganti namanya saja.
 *
 * Seeder ini aman dijalankan ulang. Kategori yang dihapus dari daftar ini tidak
 * ikut terhapus dari basis data, karena foreign key documents.category_id
 * memakai restrictOnDelete: menghapusnya harus lewat proses tersendiri yang
 * memindahkan dokumennya lebih dulu.
 */
class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pohon() as $urutanInduk => $induk) {
            $parent = DocumentCategory::query()->updateOrCreate(
                ['code' => $induk['code']],
                [
                    'parent_id' => null,
                    'name' => $induk['name'],
                    'sort_order' => ($urutanInduk + 1) * 10,
                    'is_active' => true,
                ],
            );

            foreach ($induk['children'] as $urutanAnak => $anak) {
                DocumentCategory::query()->updateOrCreate(
                    ['code' => $anak[0]],
                    [
                        'parent_id' => $parent->id,
                        'name' => $anak[1],
                        'sort_order' => ($urutanAnak + 1) * 10,
                        'is_active' => true,
                    ],
                );
            }
        }
    }

    /**
     * @return list<array{code: string, name: string, children: list<array{0: string, 1: string}>}>
     */
    private function pohon(): array
    {
        return [
            ['code' => 'tata-kelola', 'name' => 'Tata Kelola', 'children' => [
                ['tata-kelola.kebijakan', 'Kebijakan'],
                ['tata-kelola.sop', 'SOP dan Instruksi Kerja'],
                ['tata-kelola.form', 'Form dan Template'],
            ]],
            ['code' => 'legalitas', 'name' => 'Legalitas dan Perizinan', 'children' => [
                ['legalitas.perusahaan', 'Legalitas Perusahaan'],
                ['legalitas.sertifikat', 'Sertifikat dan Akreditasi'],
            ]],
            ['code' => 'kontrak', 'name' => 'Kontrak dan Perjanjian', 'children' => [
                ['kontrak.vendor', 'Vendor dan Pengadaan'],
                ['kontrak.sewa', 'Sewa'],
                ['kontrak.ekspedisi', 'Ekspedisi'],
                ['kontrak.asuransi', 'Asuransi'],
            ]],
            ['code' => 'vendor', 'name' => 'Vendor', 'children' => [
                ['vendor.legalitas', 'Legalitas Vendor'],
                ['vendor.evaluasi', 'Evaluasi dan Penilaian'],
            ]],
            ['code' => 'pengadaan', 'name' => 'Pengadaan', 'children' => [
                ['pengadaan.pr-po', 'PR dan PO'],
                ['pengadaan.bast', 'BAST dan Tanda Terima'],
                ['pengadaan.invoice', 'Invoice'],
            ]],
            ['code' => 'aset', 'name' => 'Aset dan Inventaris', 'children' => [
                ['aset.kepemilikan', 'Dokumen Kepemilikan'],
                ['aset.manual', 'Manual dan Garansi'],
            ]],
            ['code' => 'kendaraan', 'name' => 'Kendaraan', 'children' => [
                ['kendaraan.legal', 'Dokumen Legal'],
                ['kendaraan.servis', 'Servis dan Perawatan'],
            ]],
            ['code' => 'umum', 'name' => 'Umum', 'children' => [
                ['umum.pengumuman', 'Pengumuman'],
                ['umum.panduan', 'Panduan Karyawan'],
                ['umum.notulen', 'Notulen'],
            ]],
        ];
    }
}
