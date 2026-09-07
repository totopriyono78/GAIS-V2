<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

/**
 * Registri modul Tahap 1. Satu baris di sini berarti satu layar yang benar benar ada.
 * Aksi yang didaftarkan juga hanya aksi yang sudah ada tombolnya, supaya tidak ada
 * izin yang menjanjikan kemampuan yang belum dibangun.
 */
class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'code' => 'roles',
                'name' => 'Role',
                'description' => 'Membuat role dan menentukan modul serta aksi yang boleh diaksesnya.',
                'group' => 'Pengaturan Akses',
                'icon' => 'heroicon-o-shield-check',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'users',
                'name' => 'Pengguna',
                'description' => 'Akun yang bisa masuk ke aplikasi beserta role dan izin khususnya.',
                'group' => 'Pengaturan Akses',
                'icon' => 'heroicon-o-user-circle',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'modules',
                'name' => 'Modul',
                'description' => 'Registri modul yang menjadi sumber daftar izin.',
                'group' => 'Pengaturan Akses',
                'icon' => 'heroicon-o-squares-2x2',
                'sort' => 30,
                'available_actions' => ['read', 'update'],
            ],
            [
                'code' => 'asset_categories',
                'name' => 'Kategori aset',
                'description' => 'Kelompok aset beserta awalan kode, umur ekonomis, dan metode penyusutannya.',
                'group' => 'Aset',
                'icon' => 'heroicon-o-rectangle-group',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'assets',
                'name' => 'Daftar aset',
                'description' => 'Pendataan aset tetap dan aset bergerak, lokasi, penanggung jawab, status, dan kondisinya.',
                'group' => 'Aset',
                'icon' => 'heroicon-o-cube',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete', 'print'],
            ],
            [
                'code' => 'stock_opnames',
                'name' => 'Stock opname',
                'description' => 'Sesi pemeriksaan fisik aset, pencatatan temuan, dan penyesuaian data.',
                'group' => 'Aset',
                'icon' => 'heroicon-o-clipboard-document-check',
                'sort' => 30,
                'available_actions' => ['read', 'create', 'update', 'delete', 'approve'],
            ],
            [
                'code' => 'asset_transfers',
                'name' => 'Serah terima aset',
                'description' => 'Dokumen perpindahan aset antar ruangan, penanggung jawab, dan departemen.',
                'group' => 'Aset',
                'icon' => 'heroicon-o-arrow-right-start-on-rectangle',
                'sort' => 40,
                'available_actions' => ['read', 'create', 'delete'],
            ],
            [
                'code' => 'asset_disposals',
                'name' => 'Pelepasan aset',
                'description' => 'Dokumen penjualan, hibah, pemusnahan, dan kehilangan aset beserta dasarnya.',
                'group' => 'Aset',
                'icon' => 'heroicon-o-archive-box-x-mark',
                'sort' => 50,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'supply_items',
                'name' => 'Barang habis pakai',
                'description' => 'Daftar ATK dan perlengkapan habis pakai beserta stok dan batas pemesanan ulangnya.',
                'group' => 'Persediaan',
                'icon' => 'heroicon-o-archive-box',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'supply_transactions',
                'name' => 'Mutasi barang',
                'description' => 'Barang masuk, barang keluar, dan koreksi stok. Daftar ini yang menjadi dasar perhitungan stok.',
                'group' => 'Persediaan',
                'icon' => 'heroicon-o-arrows-right-left',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'departments',
                'name' => 'Departemen',
                'description' => 'Struktur departemen dan cost center untuk pembebanan biaya.',
                'group' => 'Data Induk',
                'icon' => 'heroicon-o-building-office-2',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'locations',
                'name' => 'Lokasi',
                'description' => 'Gedung, lantai, ruangan, dan area di Head Office.',
                'group' => 'Data Induk',
                'icon' => 'heroicon-o-map-pin',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'employees',
                'name' => 'Karyawan',
                'description' => 'Data karyawan yang dipakai sebagai penanggung jawab dan pengaju permintaan.',
                'group' => 'Data Induk',
                'icon' => 'heroicon-o-identification',
                'sort' => 30,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'depreciation_periods',
                'name' => 'Penyusutan aset',
                'description' => 'Penutupan penyusutan bulanan dan beban per aset.',
                'group' => 'Aset',
                'icon' => 'heroicon-o-arrow-trending-down',
                'sort' => 60,
                // Tidak ada create dan delete. Periode tidak dibuat tangan, melainkan
                // lahir dari penutupan, dan dibuang lewat aksi buka kembali yang punya
                // aturannya sendiri. Yang perlu diizinkan terpisah adalah menutupnya.
                'available_actions' => ['read', 'close', 'reopen'],
            ],
            [
                'code' => 'vendors',
                'name' => 'Rekanan',
                'description' => 'Tukang servis, bengkel, dan pemasok yang mengerjakan pemeliharaan.',
                'group' => 'Data Induk',
                'icon' => 'heroicon-o-building-storefront',
                'sort' => 40,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'service_requests',
                'name' => 'Permintaan perbaikan',
                'description' => 'Tiket kerusakan dari karyawan, persetujuan atasannya, dan penerimaannya oleh tim GA.',
                'group' => 'Pemeliharaan',
                'icon' => 'heroicon-o-lifebuoy',
                'sort' => 5,
                /*
                 * Modul ini dipakai dua kelompok orang yang berbeda, dan itu terbaca di
                 * daftar aksinya. Karyawan biasa cukup read dan create, dan tanpa
                 * read_all ia hanya melihat tiketnya sendiri. Kepala departemen menambah
                 * approve. Tim GA menambah read_all dan accept, karena merekalah yang
                 * mengubah tiket menjadi perintah kerja.
                 */
                'available_actions' => ['read', 'read_all', 'create', 'update', 'delete', 'approve', 'accept'],
            ],
            [
                'code' => 'service_request_categories',
                'name' => 'Jenis permintaan',
                'description' => 'Kelompok permintaan perbaikan beserta prioritas bawaan dan target waktu penyelesaiannya.',
                'group' => 'Data Induk',
                'icon' => 'heroicon-o-tag',
                'sort' => 50,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'maintenance_schedules',
                'name' => 'Jadwal pemeliharaan',
                'description' => 'Pekerjaan preventif yang berulang beserta jatuh temponya.',
                'group' => 'Pemeliharaan',
                'icon' => 'heroicon-o-calendar-days',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'work_orders',
                'name' => 'Perintah kerja',
                'description' => 'Pekerjaan pemeliharaan preventif dan korektif beserta biayanya.',
                'group' => 'Pemeliharaan',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'vehicles',
                'name' => 'Kendaraan dinas',
                'description' => 'Data kendaraan yang menempel pada aset, beserta pajak, STNK, KIR, dan asuransinya.',
                'group' => 'Kendaraan',
                'icon' => 'heroicon-o-truck',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'vehicle_bookings',
                'name' => 'Pemesanan kendaraan',
                'description' => 'Pemesanan pool car, persetujuan atasan, dan penugasan kendaraan beserta sopirnya.',
                'group' => 'Kendaraan',
                'icon' => 'heroicon-o-calendar-days',
                'sort' => 20,
                /*
                 * Sama seperti permintaan perbaikan, modul ini dipakai dua kelompok orang.
                 * Karyawan cukup read dan create, dan tanpa read_all ia hanya melihat
                 * pemesanannya sendiri. Kepala departemen menambah approve. Tim GA
                 * menambah read_all dan assign, karena merekalah yang menentukan
                 * kendaraan mana yang dipakai dan memeriksa bentroknya.
                 */
                'available_actions' => ['read', 'read_all', 'create', 'update', 'delete', 'approve', 'assign'],
            ],
            [
                'code' => 'expense_categories',
                'name' => 'Kategori biaya',
                'description' => 'Kelompok biaya GA beserta sumber realisasinya dan pemetaan ke akun perusahaan.',
                'group' => 'Data Induk',
                'icon' => 'heroicon-o-banknotes',
                'sort' => 60,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'budgets',
                'name' => 'Anggaran dan realisasi',
                'description' => 'Pagu per departemen per kategori per tahun, dibandingkan dengan realisasi yang dijumlahkan sendiri dari catatan yang sudah ada.',
                'group' => 'Anggaran',
                'icon' => 'heroicon-o-calculator',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'vendor_bills',
                'name' => 'Tagihan rekanan',
                'description' => 'Faktur dari rekanan, pembebanannya ke departemen dan kategori biaya, persetujuan, dan penandaan pembayaran.',
                'group' => 'Anggaran',
                'icon' => 'heroicon-o-document-currency-dollar',
                'sort' => 20,
                /*
                 * Tiga aksi yang dipisah karena tiga orang yang berbeda mengerjakannya.
                 * Staf GA mencatat dan mengajukan lewat create dan update. Manajer
                 * menyetujui lewat approve. Yang mengeluarkan uangnya menandai lewat pay.
                 * Menggabungkan approve dan pay berarti satu orang bisa menyetujui
                 * tagihannya sendiri lalu menyatakannya lunas tanpa ada yang tahu.
                 */
                'available_actions' => ['read', 'create', 'update', 'delete', 'approve', 'pay'],
            ],
            [
                'code' => 'reimbursements',
                'name' => 'Penggantian biaya',
                'description' => 'Pengajuan penggantian biaya karyawan beserta struknya, persetujuan atasan, pemeriksaan tim GA, dan penandaan transfer.',
                'group' => 'Anggaran',
                'icon' => 'heroicon-o-receipt-percent',
                'sort' => 30,
                /*
                 * Empat aksi yang dipisah karena empat orang yang berbeda mengerjakannya.
                 * Karyawan cukup read dan create, dan tanpa read_all ia hanya melihat
                 * pengajuannya sendiri. Kepala departemen menyetujui lewat approve, dan
                 * tanpa izin itu pun ia tetap bisa menyetujui pengajuan departemennya
                 * sendiri karena namanya tercatat sebagai penyetuju. Tim GA memeriksa
                 * struknya lewat verify. Yang mentransfer menandai lewat pay.
                 */
                'available_actions' => ['read', 'read_all', 'create', 'update', 'delete', 'approve', 'verify', 'pay'],
            ],
            [
                'code' => 'settings',
                'name' => 'Pengaturan',
                'description' => 'Identitas perusahaan dan pengaturan sistem lainnya.',
                'group' => 'Sistem',
                'icon' => 'heroicon-o-adjustments-horizontal',
                'sort' => 10,
                'available_actions' => ['read', 'update'],
            ],
            [
                'code' => 'audit_logs',
                'name' => 'Jejak audit',
                'description' => 'Riwayat penambahan, perubahan, dan penghapusan data.',
                'group' => 'Sistem',
                'icon' => 'heroicon-o-clipboard-document-list',
                'sort' => 20,
                'available_actions' => ['read'],
            ],
        ];

        foreach ($modules as $module) {
            Module::query()->updateOrCreate(
                ['code' => $module['code']],
                $module + ['is_active' => true],
            );
        }
    }
}
