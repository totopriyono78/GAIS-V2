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
                'name' => 'Roles',
                'description' => 'Membuat role dan menentukan modul serta aksi yang boleh diaksesnya.',
                'group' => 'Access Control',
                'icon' => 'heroicon-o-shield-check',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'users',
                'name' => 'Users',
                'description' => 'Akun yang bisa masuk ke aplikasi beserta role dan izin khususnya.',
                'group' => 'Access Control',
                'icon' => 'heroicon-o-user-circle',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'modules',
                'name' => 'Modules',
                'description' => 'Registri modul yang menjadi sumber daftar izin.',
                'group' => 'Access Control',
                'icon' => 'heroicon-o-squares-2x2',
                'sort' => 30,
                'available_actions' => ['read', 'update'],
            ],
            [
                'code' => 'asset_categories',
                'name' => 'Asset Categories',
                'description' => 'Kelompok aset beserta awalan kode, umur ekonomis, dan metode penyusutannya.',
                'group' => 'Assets',
                'icon' => 'heroicon-o-rectangle-group',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'assets',
                'name' => 'Assets',
                'description' => 'Pendataan aset tetap dan aset bergerak, lokasi, penanggung jawab, status, dan kondisinya.',
                'group' => 'Assets',
                'icon' => 'heroicon-o-cube',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete', 'print'],
            ],
            [
                'code' => 'stock_opnames',
                'name' => 'Stock Opname',
                'description' => 'Sesi pemeriksaan fisik aset, pencatatan temuan, dan penyesuaian data.',
                'group' => 'Assets',
                'icon' => 'heroicon-o-clipboard-document-check',
                'sort' => 30,
                'available_actions' => ['read', 'create', 'update', 'delete', 'approve'],
            ],
            [
                'code' => 'asset_transfers',
                'name' => 'Asset Transfers',
                'description' => 'Dokumen perpindahan aset antar ruangan, penanggung jawab, dan departemen.',
                'group' => 'Assets',
                'icon' => 'heroicon-o-arrow-right-start-on-rectangle',
                'sort' => 40,
                'available_actions' => ['read', 'create', 'delete', 'print'],
            ],
            [
                'code' => 'asset_disposals',
                'name' => 'Asset Disposals',
                'description' => 'Dokumen penjualan, hibah, pemusnahan, dan kehilangan aset beserta dasarnya.',
                'group' => 'Assets',
                'icon' => 'heroicon-o-archive-box-x-mark',
                'sort' => 50,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'supply_items',
                'name' => 'Supply Items',
                'description' => 'Daftar ATK dan perlengkapan habis pakai beserta stok dan batas pemesanan ulangnya.',
                'group' => 'Office Supplies',
                'icon' => 'heroicon-o-archive-box',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'supply_transactions',
                'name' => 'Supply Movements',
                'description' => 'Barang masuk, barang keluar, dan koreksi stok. Daftar ini yang menjadi dasar perhitungan stok.',
                'group' => 'Office Supplies',
                'icon' => 'heroicon-o-arrows-right-left',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'supply_requests',
                'name' => 'Supply Requests',
                'description' => 'Permintaan pemakaian ATK oleh karyawan, persetujuan atasan, dan penyerahan barang oleh tim GA. Penyerahannya yang melahirkan mutasi barang keluar.',
                'group' => 'Office Supplies',
                'icon' => 'heroicon-o-clipboard-document-list',
                'sort' => 30,
                /*
                 * Karyawan cukup read dan create, dan tanpa read_all ia hanya melihat
                 * permintaannya sendiri. Kepala departemen menyetujui karena namanya
                 * tercatat sebagai penyetuju, bukan karena memegang approve. Tim GA
                 * menyerahkan barangnya lewat issue, dan itu satu satunya izin yang
                 * bisa mengurangi stok lewat modul ini. request_for_others yang
                 * memisahkan karyawan biasa dari perwakilan departemen.
                 */
                'available_actions' => ['read', 'read_all', 'create', 'update', 'delete', 'approve', 'issue', 'request_for_others'],
            ],
            [
                'code' => 'supply_purchases',
                'name' => 'Supply Purchases',
                'description' => 'Pesanan pembelian ATK ke pemasok dan pembelian langsung, beserta penerimaan barangnya. Penerimaannya yang melahirkan mutasi barang masuk.',
                'group' => 'Office Supplies',
                'icon' => 'heroicon-o-shopping-cart',
                'sort' => 40,
                /*
                 * approve dipegang manajer GA, yang menyetujui pesanan sebelum dikirim ke
                 * pemasok. receive dipegang staf gudang, yang menghitung barang saat datang.
                 * Keduanya dipisah karena menjawab pertanyaan yang berbeda: apakah kita
                 * boleh membeli ini, dan apakah barangnya benar benar sudah sampai.
                 */
                'available_actions' => ['read', 'create', 'update', 'delete', 'approve', 'receive'],
            ],
            [
                'code' => 'supply_opnames',
                'name' => 'Supply Opname',
                'description' => 'Penghitungan fisik barang habis pakai dan penyesuaian stok dari hasilnya. Tiap selisih lahir sebagai mutasi koreksi, bukan mengubah angka diam diam.',
                'group' => 'Office Supplies',
                'icon' => 'heroicon-o-clipboard-document-check',
                'sort' => 50,
                /*
                 * adjust dipisah dari update, sesuai keputusan pemilik proyek pada
                 * 8 September 2026. Menyusun daftar, menghitung, dan menutup sesi cukup
                 * dengan update. Menggeser angka gudang butuh izin tersendiri, dan itulah
                 * satu satunya tindakan di modul ini yang mengubah stok.
                 */
                'available_actions' => ['read', 'create', 'update', 'delete', 'adjust'],
            ],
            [
                'code' => 'service_staff',
                'name' => 'Service Staff',
                'description' => 'Petugas kebersihan dan keamanan, karyawan perusahaan maupun tenaga dari rekanan penyedia.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-identification',
                'sort' => 70,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'service_areas',
                'name' => 'Service Areas',
                'description' => 'Area yang dibersihkan dan diperiksa, beserta seberapa sering dan siapa penanggung jawabnya.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-sparkles',
                'sort' => 80,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'cleaning_inspections',
                'name' => 'Cleaning Inspections',
                'description' => 'Putaran pemeriksaan kebersihan oleh pengawas GA. Yang tercatat adalah hasil pemeriksaan, bukan laporan petugas.',
                'group' => 'Facility Services',
                'icon' => 'heroicon-o-clipboard-document-list',
                'sort' => 10,
                /*
                 * Tanpa aksi tersendiri untuk menyelesaikan putaran. Berbeda dari opname yang
                 * memisahkan adjust, di sini menyelesaikan pemeriksaan tidak mengubah angka
                 * apa pun di tempat lain, jadi tidak ada yang perlu dipertanggungjawabkan
                 * terpisah dari mencatat hasilnya.
                 */
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'security_shifts',
                'name' => 'Security Shifts',
                'description' => 'Jadwal jaga keamanan beserta kehadirannya. Rencana dan kenyataan disimpan berdampingan, tidak saling menimpa.',
                'group' => 'Facility Services',
                'icon' => 'heroicon-o-shield-exclamation',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'incident_reports',
                'name' => 'Incident Reports',
                'description' => 'Buku kejadian keamanan. Insiden yang butuh perbaikan fisik diteruskan menjadi tiket perbaikan.',
                'group' => 'Facility Services',
                'icon' => 'heroicon-o-exclamation-triangle',
                'sort' => 30,
                /*
                 * Tanpa aksi tersendiri untuk meneruskan insiden menjadi tiket. Yang lahir
                 * dari tombol itu adalah permintaan perbaikan, jadi izinnya pun izin membuat
                 * permintaan perbaikan, bukan izin baru di modul ini. Menambah izin sendiri
                 * di sini akan membuat orang bisa membuat tiket tanpa boleh membuat tiket.
                 */
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'letters',
                'name' => 'Letters',
                'description' => 'Buku agenda surat masuk dan surat keluar, beserta serah terima surat masuk ke orang yang dituju.',
                'group' => 'Correspondence',
                'icon' => 'heroicon-o-envelope',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'parcel_shipments',
                'name' => 'Parcel Shipments',
                'description' => 'Permintaan kirim paket keluar dan biaya sebenarnya dari resi ekspedisi. Biayanya memotong pagu anggaran departemen yang dibebani.',
                'group' => 'Correspondence',
                'icon' => 'heroicon-o-inbox-stack',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'business_trips',
                'name' => 'Business Trips',
                'description' => 'Perjalanan dinas beserta uang muka dan pertanggungjawabannya. Biaya yang sudah ditutup memotong pagu anggaran departemen yang dibebani.',
                'group' => 'Vehicles',
                'icon' => 'heroicon-o-map',
                'sort' => 30,
                /*
                 * Empat izin untuk empat tangan yang berbeda. approve dipegang atasan,
                 * pay dipegang tim GA yang menyerahkan uang mukanya, dan verify dipegang
                 * yang memeriksa pertanggungjawabannya. Menyatukan pay dan verify berarti
                 * orang yang menyerahkan uang sekaligus yang menyatakan uang itu terpakai
                 * dengan benar.
                 */
                'available_actions' => ['read', 'read_all', 'create', 'update', 'delete', 'approve', 'pay', 'verify'],
            ],
            [
                'code' => 'departments',
                'name' => 'Departments',
                'description' => 'Struktur departemen dan cost center untuk pembebanan biaya.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-building-office-2',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'locations',
                'name' => 'Locations',
                'description' => 'Gedung, lantai, ruangan, dan area di Head Office.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-map-pin',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'employees',
                'name' => 'Employees',
                'description' => 'Data karyawan yang dipakai sebagai penanggung jawab dan pengaju permintaan.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-identification',
                'sort' => 30,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'depreciation_periods',
                'name' => 'Depreciation',
                'description' => 'Penutupan penyusutan bulanan dan beban per aset.',
                'group' => 'Assets',
                'icon' => 'heroicon-o-arrow-trending-down',
                'sort' => 60,
                // Tidak ada create dan delete. Periode tidak dibuat tangan, melainkan
                // lahir dari penutupan, dan dibuang lewat aksi buka kembali yang punya
                // aturannya sendiri. Yang perlu diizinkan terpisah adalah menutupnya.
                'available_actions' => ['read', 'close', 'reopen'],
            ],
            [
                'code' => 'vendors',
                'name' => 'Vendors',
                'description' => 'Tukang servis, bengkel, dan pemasok yang mengerjakan pemeliharaan.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-building-storefront',
                'sort' => 40,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'service_requests',
                'name' => 'Corrective Maintenance',
                'description' => 'Tiket kerusakan dari karyawan, persetujuan atasannya, dan penerimaannya oleh tim GA.',
                'group' => 'Maintenance',
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
                'name' => 'Request Types',
                'description' => 'Kelompok permintaan perbaikan beserta prioritas bawaan dan target waktu penyelesaiannya.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-tag',
                'sort' => 50,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'maintenance_schedules',
                'name' => 'Preventive Maintenance',
                'description' => 'Pekerjaan preventif yang berulang beserta jatuh temponya.',
                'group' => 'Maintenance',
                'icon' => 'heroicon-o-calendar-days',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'work_orders',
                'name' => 'Work Orders',
                'description' => 'Pekerjaan pemeliharaan preventif dan korektif beserta biayanya.',
                'group' => 'Maintenance',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'sort' => 20,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'vehicles',
                'name' => 'Vehicles',
                'description' => 'Data kendaraan yang menempel pada aset, beserta pajak, STNK, KIR, dan asuransinya.',
                'group' => 'Vehicles',
                'icon' => 'heroicon-o-truck',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'vehicle_bookings',
                'name' => 'Vehicle Bookings',
                'description' => 'Pemesanan pool car, persetujuan atasan, dan penugasan kendaraan beserta sopirnya.',
                'group' => 'Vehicles',
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
                'name' => 'Expense Categories',
                'description' => 'Kelompok biaya GA beserta sumber realisasinya dan pemetaan ke akun perusahaan.',
                'group' => 'Master Data',
                'icon' => 'heroicon-o-banknotes',
                'sort' => 60,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'budgets',
                'name' => 'Budgets',
                'description' => 'Pagu per departemen per kategori per tahun, dibandingkan dengan realisasi yang dijumlahkan sendiri dari catatan yang sudah ada.',
                'group' => 'Budget & Expenses',
                'icon' => 'heroicon-o-calculator',
                'sort' => 10,
                'available_actions' => ['read', 'create', 'update', 'delete'],
            ],
            [
                'code' => 'vendor_bills',
                'name' => 'Vendor Bills',
                'description' => 'Faktur dari rekanan, pembebanannya ke departemen dan kategori biaya, persetujuan, dan penandaan pembayaran.',
                'group' => 'Budget & Expenses',
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
                'name' => 'Reimbursements',
                'description' => 'Pengajuan penggantian biaya karyawan beserta struknya, persetujuan atasan, pemeriksaan tim GA, dan penandaan transfer.',
                'group' => 'Budget & Expenses',
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
                'name' => 'Settings',
                'description' => 'Identitas perusahaan dan pengaturan sistem lainnya.',
                'group' => 'System',
                'icon' => 'heroicon-o-adjustments-horizontal',
                'sort' => 10,
                'available_actions' => ['read', 'update'],
            ],
            [
                'code' => 'audit_logs',
                'name' => 'Audit Log',
                'description' => 'Riwayat penambahan, perubahan, dan penghapusan data.',
                'group' => 'System',
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
