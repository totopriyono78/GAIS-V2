<?php

namespace Database\Seeders;

use App\Enums\Lifecycle;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

/**
 * Katalog awal jenis dokumen: 8 terkendali, 5 berjangka waktu, 7 lampiran.
 *
 * Daftar ini usulan berdasarkan lingkup GAIS, bukan hasil pendataan. Ia perlu
 * ditinjau orang yang tahu dokumen apa yang sebenarnya beredar di perusahaan.
 * Menambah jenis itu murah, cukup satu baris di sini atau satu form di layar.
 * Mengganti susunan metadata setelah jenis itu terpakai ribuan dokumen tidak.
 *
 * Masa simpan di bawah adalah angka umum, bukan angka yang sudah dikonfirmasi.
 * Sebagian jenis dokumen masa simpannya diatur peraturan, jadi angkanya perlu
 * dicek bagian legal sebelum modul ini dipakai sungguhan.
 *
 * Dua konvensi kolom yang mudah salah dibaca:
 *
 * - retention_years null berarti disimpan permanen, atau mengikuti catatan
 *   induknya untuk jenis lampiran. Nol bukan pilihan, dan constraint basis
 *   data memang hanya menerima null atau angka positif.
 * - number_prefix null untuk seluruh jenis lampiran, karena identitasnya
 *   mengikuti transaksi induk dan ia tidak diberi nomor dokumen sendiri.
 *
 * Seeder ini aman dijalankan ulang. Pencocokannya lewat kolom code, jadi
 * menjalankannya lagi setelah katalog diubah tidak menggandakan baris dan
 * tidak memutus satu pun tautan dari dokumen yang sudah terlanjur dibuat.
 */
class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->katalog() as $urutan => $jenis) {
            $kode = $jenis['code'];
            unset($jenis['code']);

            DocumentType::query()->updateOrCreate(
                ['code' => $kode],
                [...$jenis, 'sort_order' => ($urutan + 1) * 10, 'is_active' => true],
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function katalog(): array
    {
        return [
            ...$this->terkendali(),
            ...$this->berjangkaWaktu(),
            ...$this->lampiran(),
        ];
    }

    /**
     * Terkendali: punya versi yang berlaku, masa berlaku, dan pengesahan.
     *
     * Inilah jenis yang paling menuntut, dan yang diperiksa auditor.
     *
     * @return list<array<string, mixed>>
     */
    private function terkendali(): array
    {
        return [
            [
                'code' => 'sop',
                'name' => 'SOP / Prosedur',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'SOP',
                'metadata_schema' => [
                    'departemen' => self::relasi('departments', 'Departemen pemilik', filter: true),
                    'nomor_revisi' => self::angka('Nomor revisi'),
                    'pengesah' => self::relasi('users', 'Disahkan oleh'),
                    'tanggal_sah' => self::tanggal('Tanggal pengesahan'),
                    'ruang_lingkup' => self::teksPanjang('Ruang lingkup', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'policy',
                'name' => 'Kebijakan Perusahaan',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => true,
                // Permanen. Kebijakan tidak dimusnahkan, hanya dicabut.
                'retention_years' => null, 'number_prefix' => 'KBJ',
                'metadata_schema' => [
                    'nomor_kebijakan' => self::teks('Nomor kebijakan', cari: true),
                    'bidang' => self::pilihan('Bidang kebijakan', ['Umum', 'Keuangan', 'Kepegawaian', 'Pengadaan', 'Aset', 'K3', 'Teknologi Informasi'], filter: true),
                    'pengesah' => self::relasi('users', 'Disahkan oleh'),
                    'tanggal_sah' => self::tanggal('Tanggal pengesahan'),
                    'ringkasan' => self::teksPanjang('Ringkasan kebijakan', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'work_instruction',
                'name' => 'Instruksi Kerja',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'IK',
                'metadata_schema' => [
                    'departemen' => self::relasi('departments', 'Departemen pemilik', filter: true),
                    'sop_induk' => self::relasi('documents', 'SOP induk', wajib: false, filter: true),
                    'nomor_revisi' => self::angka('Nomor revisi'),
                    'pengesah' => self::relasi('users', 'Disahkan oleh'),
                    'tanggal_sah' => self::tanggal('Tanggal pengesahan'),
                    'ruang_lingkup' => self::teksPanjang('Ruang lingkup', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'form_template',
                'name' => 'Form & Template',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => false,
                'retention_years' => 5, 'number_prefix' => 'FRM',
                'metadata_schema' => [
                    'departemen' => self::relasi('departments', 'Departemen pemilik', filter: true),
                    'kode_form' => self::teks('Kode form', cari: true),
                    'nomor_revisi' => self::angka('Nomor revisi'),
                    'format_berkas' => self::pilihan('Format berkas', ['PDF', 'Word', 'Excel', 'Formulir Cetak'], wajib: false, filter: true),
                    'petunjuk_pengisian' => self::teksPanjang('Petunjuk pengisian', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'company_legal',
                'name' => 'Legalitas Perusahaan',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => false, 'has_validity' => true,
                'retention_years' => null, 'number_prefix' => 'LGL',
                'metadata_schema' => [
                    'jenis_dokumen' => self::pilihan('Jenis', ['Akta Pendirian', 'Akta Perubahan', 'SK Kemenkumham', 'NIB', 'NPWP', 'SPPKP', 'Izin Usaha', 'Lainnya'], filter: true),
                    'nomor' => self::teks('Nomor dokumen', cari: true),
                    'penerbit' => self::teks('Instansi penerbit', wajib: false),
                    'terbit' => self::tanggal('Tanggal terbit'),
                    'berakhir' => self::tanggal('Berlaku sampai', wajib: false, filter: true, bantuan: 'Dikosongkan bila berlaku seumur perusahaan.'),
                ],
            ],
            [
                'code' => 'certificate',
                'name' => 'Sertifikat & Akreditasi',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => false, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'SRT',
                'metadata_schema' => [
                    'nama_sertifikat' => self::teks('Nama sertifikat', cari: true),
                    'standar' => self::pilihan('Standar atau skema', ['ISO 9001', 'ISO 14001', 'ISO 27001', 'ISO 45001', 'SNI', 'Lainnya'], wajib: false, filter: true),
                    'lembaga_penerbit' => self::teks('Lembaga penerbit', cari: true),
                    'nomor' => self::teks('Nomor sertifikat', cari: true),
                    'terbit' => self::tanggal('Tanggal terbit'),
                    'berakhir' => self::tanggal('Berlaku sampai', filter: true),
                    'ruang_lingkup' => self::teksPanjang('Ruang lingkup sertifikasi', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'guideline',
                'name' => 'Panduan Karyawan',
                'lifecycle' => Lifecycle::Controlled,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => false,
                'retention_years' => 5, 'number_prefix' => 'PDN',
                'metadata_schema' => [
                    'departemen' => self::relasi('departments', 'Departemen penyusun', wajib: false, filter: true),
                    'target_pembaca' => self::pilihan('Ditujukan untuk', ['Seluruh Karyawan', 'Karyawan Baru', 'Manajemen', 'Bagian Umum'], filter: true),
                    'nomor_revisi' => self::angka('Nomor revisi', wajib: false),
                    'ringkasan' => self::teksPanjang('Ringkasan isi', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'announcement',
                'name' => 'Pengumuman',
                'lifecycle' => Lifecycle::Controlled,
                // Satu satunya jenis terkendali yang tidak berversi. Pengumuman
                // yang berubah diterbitkan ulang, bukan direvisi.
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => true,
                'retention_years' => 2, 'number_prefix' => 'PNG',
                'metadata_schema' => [
                    'nomor_pengumuman' => self::teks('Nomor pengumuman', wajib: false, cari: true),
                    'kategori' => self::pilihan('Kategori', ['Kebijakan', 'Operasional', 'Kepegawaian', 'Fasilitas', 'Lainnya'], filter: true),
                    'diterbitkan_oleh' => self::relasi('users', 'Diterbitkan oleh'),
                    'tanggal_terbit' => self::tanggal('Tanggal terbit', filter: true),
                    'berlaku_sampai' => self::tanggal('Berlaku sampai', wajib: false, filter: true),
                    'isi_ringkas' => self::teksPanjang('Isi ringkas', wajib: false, cari: true),
                ],
            ],
        ];
    }

    /**
     * Berjangka waktu: yang menonjol para pihak dan tanggal berakhirnya.
     *
     * Revisinya biasanya berupa adendum, yaitu dokumen baru yang ditautkan,
     * bukan versi baru dari dokumen yang sama. Pertanyaan khas pemakainya
     * bukan "versi berapa yang berlaku" melainkan "mana saja yang jatuh tempo
     * tiga bulan lagi".
     *
     * @return list<array<string, mixed>>
     */
    private function berjangkaWaktu(): array
    {
        return [
            [
                'code' => 'contract_vendor',
                'name' => 'Kontrak / PKS Vendor',
                'lifecycle' => Lifecycle::TermBased,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'KTR',
                'metadata_schema' => [
                    'vendor_id' => self::relasi('vendors', 'Vendor', filter: true),
                    'nomor_kontrak' => self::teks('Nomor kontrak pihak kedua', cari: true),
                    'nilai_kontrak' => self::angka('Nilai kontrak (Rp)'),
                    'mulai' => self::tanggal('Mulai berlaku', filter: true),
                    'berakhir' => self::tanggal('Berakhir', filter: true),
                    'auto_renewal' => self::yaTidak('Perpanjangan otomatis'),
                    'pic_internal' => self::relasi('users', 'PIC internal'),
                    'ruang_lingkup' => self::teksPanjang('Ruang lingkup pekerjaan', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'contract_lease',
                'name' => 'Perjanjian Sewa',
                'lifecycle' => Lifecycle::TermBased,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'SWA',
                'metadata_schema' => [
                    'pihak_kedua' => self::teks('Pemberi sewa', cari: true),
                    'objek_sewa' => self::teks('Objek sewa', cari: true),
                    'lokasi' => self::teks('Lokasi', wajib: false, filter: true),
                    'nilai_sewa' => self::angka('Nilai sewa (Rp)'),
                    'periode_pembayaran' => self::pilihan('Periode pembayaran', ['Bulanan', 'Triwulan', 'Semester', 'Tahunan', 'Sekaligus'], wajib: false, filter: true),
                    'mulai' => self::tanggal('Mulai berlaku', filter: true),
                    'berakhir' => self::tanggal('Berakhir', filter: true),
                    'auto_renewal' => self::yaTidak('Perpanjangan otomatis'),
                    'pic_internal' => self::relasi('users', 'PIC internal'),
                ],
            ],
            [
                'code' => 'contract_courier',
                'name' => 'Kerjasama Ekspedisi',
                'lifecycle' => Lifecycle::TermBased,
                'is_versioned' => true, 'needs_approval' => true, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'EKS',
                'metadata_schema' => [
                    'vendor_id' => self::relasi('vendors', 'Mitra ekspedisi', filter: true),
                    'nomor_kontrak' => self::teks('Nomor perjanjian', cari: true),
                    'jenis_layanan' => self::pilihan('Jenis layanan', ['Dokumen', 'Paket', 'Dokumen dan Paket'], filter: true),
                    'cakupan_wilayah' => self::teksPanjang('Cakupan wilayah', wajib: false, cari: true),
                    'sla_hari' => self::angka('Janji waktu kirim (hari)', wajib: false, min: 1),
                    'mulai' => self::tanggal('Mulai berlaku', filter: true),
                    'berakhir' => self::tanggal('Berakhir', filter: true),
                    'pic_internal' => self::relasi('users', 'PIC internal'),
                ],
            ],
            [
                'code' => 'insurance',
                'name' => 'Polis Asuransi',
                'lifecycle' => Lifecycle::TermBased,
                'is_versioned' => true, 'needs_approval' => false, 'has_validity' => true,
                'retention_years' => 10, 'number_prefix' => 'ASR',
                'metadata_schema' => [
                    'nomor_polis' => self::teks('Nomor polis', cari: true),
                    'penanggung' => self::teks('Perusahaan asuransi', cari: true),
                    'jenis_asuransi' => self::pilihan('Jenis asuransi', ['Kendaraan', 'Properti', 'Kesehatan', 'Jiwa', 'Tanggung Gugat', 'Lainnya'], filter: true),
                    'objek_pertanggungan' => self::teks('Objek pertanggungan', wajib: false, cari: true),
                    'nilai_pertanggungan' => self::angka('Nilai pertanggungan (Rp)', wajib: false),
                    'premi' => self::angka('Premi (Rp)', wajib: false),
                    'mulai' => self::tanggal('Mulai berlaku', filter: true),
                    'berakhir' => self::tanggal('Berakhir', filter: true),
                ],
            ],
            [
                'code' => 'vendor_legal',
                'name' => 'Legalitas Vendor',
                'lifecycle' => Lifecycle::TermBased,
                'is_versioned' => true, 'needs_approval' => false, 'has_validity' => true,
                'retention_years' => 5, 'number_prefix' => 'VLG',
                'metadata_schema' => [
                    'vendor_id' => self::relasi('vendors', 'Vendor', filter: true),
                    'jenis_dokumen' => self::pilihan('Jenis', ['NIB', 'SIUP', 'TDP', 'NPWP', 'SPPKP', 'Akta Pendirian', 'SBU', 'ISO', 'Lainnya'], filter: true),
                    'nomor' => self::teks('Nomor dokumen', cari: true),
                    'penerbit' => self::teks('Instansi penerbit', wajib: false),
                    'terbit' => self::tanggal('Tanggal terbit'),
                    'berakhir' => self::tanggal('Berlaku sampai', wajib: false, filter: true),
                ],
            ],
        ];
    }

    /**
     * Lampiran: tidak punya siklus hidup sendiri.
     *
     * Hidupnya mengikuti catatan induknya. Berita acara tidak pernah direvisi
     * menjadi versi dua; kalau isinya salah, transaksinya yang dikoreksi.
     * Karena itu seluruhnya tidak berversi, tidak butuh pengesahan sendiri,
     * dan tidak diberi nomor dokumen sendiri.
     *
     * Dua di antaranya bermasa simpan kosong karena ikut umur induknya:
     * dokumen aset disimpan selama asetnya masih ada, dokumen kendaraan selama
     * kendaraannya masih dimiliki.
     *
     * @return list<array<string, mixed>>
     */
    private function lampiran(): array
    {
        return [
            [
                'code' => 'bast',
                'name' => 'Berita Acara Serah Terima',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => false,
                'retention_years' => 10, 'number_prefix' => null,
                'metadata_schema' => [
                    'nomor_bast' => self::teks('Nomor BAST', cari: true),
                    'tanggal_serah' => self::tanggal('Tanggal serah terima', filter: true),
                    'pihak_penyerah' => self::teks('Pihak yang menyerahkan', cari: true),
                    'pihak_penerima' => self::relasi('users', 'Pihak yang menerima'),
                    'uraian' => self::teksPanjang('Uraian barang atau pekerjaan', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'invoice_doc',
                'name' => 'Invoice / Tagihan',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => false,
                'retention_years' => 10, 'number_prefix' => null,
                'metadata_schema' => [
                    'nomor_invoice' => self::teks('Nomor invoice', cari: true),
                    'vendor_id' => self::relasi('vendors', 'Vendor', filter: true),
                    'tanggal_invoice' => self::tanggal('Tanggal invoice', filter: true),
                    'jatuh_tempo' => self::tanggal('Jatuh tempo', wajib: false, filter: true),
                    'nilai' => self::angka('Nilai tagihan (Rp)'),
                    'mata_uang' => self::pilihan('Mata uang', ['IDR', 'USD', 'SGD', 'EUR'], wajib: false, filter: true),
                    'nomor_faktur_pajak' => self::teks('Nomor faktur pajak', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'receipt_doc',
                'name' => 'Tanda Terima',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => false,
                'retention_years' => 10, 'number_prefix' => null,
                'metadata_schema' => [
                    'nomor_tanda_terima' => self::teks('Nomor tanda terima', wajib: false, cari: true),
                    'tanggal_terima' => self::tanggal('Tanggal terima', filter: true),
                    'diterima_oleh' => self::relasi('users', 'Diterima oleh'),
                    'diserahkan_oleh' => self::teks('Diserahkan oleh', wajib: false, cari: true),
                    'uraian' => self::teksPanjang('Uraian', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'po_doc',
                'name' => 'Dokumen Purchase Order',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => false,
                'retention_years' => 10, 'number_prefix' => null,
                'metadata_schema' => [
                    'nomor_po' => self::teks('Nomor PO', cari: true),
                    'vendor_id' => self::relasi('vendors', 'Vendor', filter: true),
                    'tanggal_po' => self::tanggal('Tanggal PO', filter: true),
                    'nilai_po' => self::angka('Nilai PO (Rp)'),
                    'status_dokumen' => self::pilihan('Status dokumen', ['Draf', 'Terkirim', 'Dikonfirmasi', 'Selesai', 'Batal'], wajib: false, filter: true),
                ],
            ],
            [
                'code' => 'asset_doc',
                'name' => 'Dokumen Aset',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => false,
                'retention_years' => null, 'number_prefix' => null,
                'metadata_schema' => [
                    'asset_id' => self::relasi('assets', 'Aset', filter: true),
                    'jenis_dokumen' => self::pilihan('Jenis', ['Manual', 'Kartu Garansi', 'Faktur Pembelian', 'Sertifikat Kepemilikan', 'Foto', 'Lainnya'], filter: true),
                    'nomor' => self::teks('Nomor dokumen', wajib: false, cari: true),
                    'berakhir' => self::tanggal('Berlaku sampai', wajib: false, filter: true, bantuan: 'Dipakai untuk masa garansi.'),
                    'catatan' => self::teksPanjang('Catatan', wajib: false, cari: true),
                ],
            ],
            [
                'code' => 'vehicle_doc',
                'name' => 'Dokumen Kendaraan',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => true,
                'retention_years' => null, 'number_prefix' => null,
                'metadata_schema' => [
                    'vehicle_id' => self::relasi('vehicles', 'Kendaraan', filter: true),
                    'jenis_dokumen' => self::pilihan('Jenis', ['STNK', 'BPKB', 'Faktur', 'Asuransi', 'Servis', 'KIR'], filter: true),
                    'nomor' => self::teks('Nomor', wajib: false, cari: true),
                    'berakhir' => self::tanggal('Berlaku sampai', wajib: false, filter: true),
                ],
            ],
            [
                'code' => 'delivery_proof',
                'name' => 'Bukti Pengiriman',
                'lifecycle' => Lifecycle::Attachment,
                'is_versioned' => false, 'needs_approval' => false, 'has_validity' => false,
                'retention_years' => 5, 'number_prefix' => null,
                'metadata_schema' => [
                    'nomor_resi' => self::teks('Nomor resi', cari: true),
                    'ekspedisi_id' => self::relasi('vendors', 'Mitra ekspedisi', wajib: false, filter: true),
                    'tanggal_kirim' => self::tanggal('Tanggal kirim', filter: true),
                    'tanggal_terima' => self::tanggal('Tanggal diterima', wajib: false, filter: true),
                    'tujuan' => self::teks('Tujuan', wajib: false, cari: true),
                    'penerima' => self::teks('Nama penerima', wajib: false, cari: true),
                    'status_pengiriman' => self::pilihan('Status pengiriman', ['Dalam Proses', 'Terkirim', 'Gagal', 'Dikembalikan'], wajib: false, filter: true),
                ],
            ],
        ];
    }

    // ---------------------------------------------------------- pembantu field

    /**
     * Tujuh pembantu di bawah membangun satu definisi field metadata.
     *
     * Bentuk panjangnya berupa larik enam baris per field, dan dengan dua puluh
     * jenis dokumen itu menjadi ratusan baris yang isinya hampir sama semua.
     * Ditulis begini, satu field muat satu baris dan yang berbeda antar field
     * langsung terlihat.
     *
     * Nama argumennya sengaja dipakai di pemanggil, jadi wajib: false terbaca
     * apa adanya tanpa perlu membuka berkas ini.
     *
     * @return array<string, mixed>
     */
    private static function dasar(string $tipe, string $label, bool $wajib, bool $cari, bool $filter, ?string $bantuan): array
    {
        return array_filter([
            'type' => $tipe,
            'label' => $label,
            'required' => $wajib,
            'searchable' => $cari,
            'filterable' => $filter,
            'help' => $bantuan,
        ], static fn ($nilai): bool => $nilai !== false && $nilai !== null);
    }

    private static function teks(string $label, bool $wajib = true, bool $cari = false, bool $filter = false, ?string $bantuan = null): array
    {
        return self::dasar('string', $label, $wajib, $cari, $filter, $bantuan);
    }

    private static function teksPanjang(string $label, bool $wajib = true, bool $cari = false, bool $filter = false, ?string $bantuan = null): array
    {
        return self::dasar('text', $label, $wajib, $cari, $filter, $bantuan);
    }

    private static function angka(string $label, bool $wajib = true, bool $cari = false, bool $filter = false, ?string $bantuan = null, ?int $min = null): array
    {
        return self::dasar('number', $label, $wajib, $cari, $filter, $bantuan)
            + ($min === null ? [] : ['min' => $min]);
    }

    private static function tanggal(string $label, bool $wajib = true, bool $cari = false, bool $filter = false, ?string $bantuan = null): array
    {
        return self::dasar('date', $label, $wajib, $cari, $filter, $bantuan);
    }

    private static function yaTidak(string $label, bool $wajib = false, bool $cari = false, bool $filter = false, ?string $bantuan = null): array
    {
        return self::dasar('boolean', $label, $wajib, $cari, $filter, $bantuan);
    }

    /**
     * @param  list<string>  $opsi
     */
    private static function pilihan(string $label, array $opsi, bool $wajib = true, bool $cari = false, bool $filter = false, ?string $bantuan = null): array
    {
        return self::dasar('select', $label, $wajib, $cari, $filter, $bantuan) + ['options' => $opsi];
    }

    /**
     * Relasi ke tabel lain. Nama tabelnya disebut apa adanya, bukan nama kelas
     * model, supaya skema ini tetap bisa dibaca dari luar PHP: layar, validator,
     * dan perender filter semuanya membaca definisi yang sama.
     */
    private static function relasi(string $tabel, string $label, bool $wajib = true, bool $cari = false, bool $filter = false, ?string $bantuan = null): array
    {
        return self::dasar('reference', $label, $wajib, $cari, $filter, $bantuan) + ['reference' => $tabel];
    }
}
