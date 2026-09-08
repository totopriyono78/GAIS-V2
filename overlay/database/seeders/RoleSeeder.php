<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $administrator = Role::query()->updateOrCreate(
            ['code' => 'administrator'],
            [
                'name' => 'Administrator Sistem',
                'description' => 'Mengelola pengguna, role, data induk, dan pengaturan sistem.',
                'is_system' => true,
                'data_scope' => 'all',
                'is_active' => true,
            ],
        );

        $administrator->permissions()->sync(Permission::query()->pluck('id')->all());

        $this->syncRole('manajer-ga', [
            'name' => 'Manajer GA',
            'description' => 'Melihat seluruh data GA dan mengubah data induk, tanpa mengelola akses pengguna.',
            'is_system' => false,
            'data_scope' => 'all',
            'is_active' => true,
        ], [
            'departments.read', 'departments.create', 'departments.update',
            'locations.read', 'locations.create', 'locations.update',
            'employees.read', 'employees.create', 'employees.update',
            'asset_categories.read', 'asset_categories.create', 'asset_categories.update',
            'assets.read', 'assets.create', 'assets.update', 'assets.print',
            'stock_opnames.read', 'stock_opnames.create', 'stock_opnames.update', 'stock_opnames.approve',
            'asset_transfers.read', 'asset_transfers.create', 'asset_transfers.delete',
            'asset_disposals.read', 'asset_disposals.create', 'asset_disposals.update', 'asset_disposals.delete',
            'supply_items.read', 'supply_items.create', 'supply_items.update',
            'supply_transactions.read', 'supply_transactions.create', 'supply_transactions.update', 'supply_transactions.delete',
            // Manajer GA menyerahkan barang lewat issue. approve juga diberikan supaya
            // antrean tidak tersangkut saat kepala departemen cuti panjang, dengan
            // alasan yang sama seperti pada penggantian biaya.
            'supply_requests.read', 'supply_requests.read_all', 'supply_requests.create',
            'supply_requests.update', 'supply_requests.delete', 'supply_requests.approve',
            'supply_requests.issue', 'supply_requests.request_for_others',
            // Manajer GA menyetujui pesanan sebelum dikirim ke pemasok. receive juga
            // diberikan karena di perusahaan menengah ia sering yang menerima kiriman
            // saat staf gudangnya sedang tidak di tempat.
            'supply_purchases.read', 'supply_purchases.create', 'supply_purchases.update',
            'supply_purchases.delete', 'supply_purchases.approve', 'supply_purchases.receive',
            // Manajer GA memegang adjust, karena menggeser angka gudang adalah keputusan
            // yang perlu dipertanggungjawabkan, bukan pekerjaan harian.
            'supply_opnames.read', 'supply_opnames.create', 'supply_opnames.update',
            'supply_opnames.delete', 'supply_opnames.adjust',
            'vendors.read', 'vendors.create', 'vendors.update',
            // Kebersihan. Manajer GA memegang data induknya sekaligus, karena menentukan siapa
            // penanggung jawab area adalah keputusan penugasan, bukan pekerjaan harian.
            'service_staff.read', 'service_staff.create', 'service_staff.update', 'service_staff.delete',
            'service_areas.read', 'service_areas.create', 'service_areas.update', 'service_areas.delete',
            'cleaning_inspections.read', 'cleaning_inspections.create', 'cleaning_inspections.update', 'cleaning_inspections.delete',
            // Keamanan. Menyusun jadwal jaga adalah keputusan penugasan, jadi ia sekelompok
            // dengan data induk petugas di atasnya.
            'security_shifts.read', 'security_shifts.create', 'security_shifts.update', 'security_shifts.delete',
            'incident_reports.read', 'incident_reports.create', 'incident_reports.update', 'incident_reports.delete',
            // Surat. Manajer GA melihat dan mencatat seluruh agenda surat.
            'letters.read', 'letters.create', 'letters.update', 'letters.delete',
            'parcel_shipments.read', 'parcel_shipments.create', 'parcel_shipments.update', 'parcel_shipments.delete',
            // Manajer GA menyetujui perjalanan dan menyerahkan uang mukanya. verify juga
            // diberikan supaya antrean tidak tersangkut saat stafnya cuti, dengan alasan yang
            // sama seperti pada tagihan dan penggantian biaya.
            'business_trips.read', 'business_trips.read_all', 'business_trips.create',
            'business_trips.update', 'business_trips.delete', 'business_trips.approve',
            'business_trips.pay', 'business_trips.verify',
            'maintenance_schedules.read', 'maintenance_schedules.create', 'maintenance_schedules.update', 'maintenance_schedules.delete',
            'work_orders.read', 'work_orders.create', 'work_orders.update', 'work_orders.delete',
            'service_request_categories.read', 'service_request_categories.create', 'service_request_categories.update',
            'service_requests.read', 'service_requests.read_all', 'service_requests.create',
            'service_requests.update', 'service_requests.approve', 'service_requests.accept',
            'depreciation_periods.read',
            'expense_categories.read', 'expense_categories.create', 'expense_categories.update',
            'budgets.read', 'budgets.create', 'budgets.update', 'budgets.delete',
            // Manajer GA menyetujui tagihan, dan juga boleh menandainya sudah dibayar
            // karena di banyak perusahaan menengah ia yang memegang bukti transfernya.
            'vendor_bills.read', 'vendor_bills.create', 'vendor_bills.update',
            'vendor_bills.delete', 'vendor_bills.approve', 'vendor_bills.pay',
            // Manajer GA memeriksa struk dan menandai transfer. approve juga diberikan
            // supaya antrean tidak tersangkut saat kepala departemen cuti panjang.
            'reimbursements.read', 'reimbursements.read_all', 'reimbursements.create',
            'reimbursements.update', 'reimbursements.delete', 'reimbursements.approve',
            'reimbursements.verify', 'reimbursements.pay',
            'vehicles.read', 'vehicles.create', 'vehicles.update', 'vehicles.delete',
            'vehicle_bookings.read', 'vehicle_bookings.read_all', 'vehicle_bookings.create',
            'vehicle_bookings.update', 'vehicle_bookings.delete', 'vehicle_bookings.approve',
            'vehicle_bookings.assign',
            'settings.read',
            'audit_logs.read',
        ]);

        $this->syncRole('staf-ga', [
            'name' => 'Staf GA',
            'description' => 'Mengelola data induk harian: karyawan, departemen, dan lokasi.',
            'is_system' => false,
            'data_scope' => 'all',
            'is_active' => true,
        ], [
            'departments.read',
            'locations.read', 'locations.create', 'locations.update',
            'employees.read', 'employees.create', 'employees.update',
            'asset_categories.read',
            'assets.read', 'assets.create', 'assets.update', 'assets.print',
            'stock_opnames.read', 'stock_opnames.create', 'stock_opnames.update',
            'asset_transfers.read', 'asset_transfers.create',
            'asset_disposals.read',
            'supply_items.read', 'supply_items.create', 'supply_items.update',
            'supply_transactions.read', 'supply_transactions.create', 'supply_transactions.update',
            // issue tanpa approve, sama seperti pola tagihan dan penggantian biaya. Staf
            // GA yang membuka lemari dan menyerahkan barangnya, tetapi tanda tangan
            // persetujuan tetap milik kepala departemen pemohon.
            'supply_requests.read', 'supply_requests.read_all', 'supply_requests.create',
            'supply_requests.update', 'supply_requests.issue', 'supply_requests.request_for_others',
            // receive tanpa approve, mengikuti pola yang sama dengan tagihan dan penggantian
            // biaya. Staf GA menyusun pesanan dan menerima barangnya, tetapi tanda tangan
            // yang membolehkan pesanan dikirim ke pemasok tetap milik manajer.
            'supply_purchases.read', 'supply_purchases.create', 'supply_purchases.update',
            'supply_purchases.receive',
            // Tanpa adjust, mengikuti pola yang sama dengan opname aset: staf menyusun daftar,
            // menghitung, dan menutup sesi, tetapi yang menggeser angka gudang bukan dia.
            'supply_opnames.read', 'supply_opnames.create', 'supply_opnames.update',
            'vendors.read',
            // Staf GA yang berkeliling memeriksa, jadi ia memegang penuh putaran pemeriksaan.
            // Data induk petugas dan area hanya bisa dibaca: menentukan siapa penanggung jawab
            // sebuah area adalah keputusan penugasan yang tetap milik manajer.
            'service_staff.read',
            'service_areas.read',
            'cleaning_inspections.read', 'cleaning_inspections.create', 'cleaning_inspections.update',
            // Staf GA mencatat kehadiran dan menulis laporan insiden, tetapi tidak menyusun
            // jadwal jaga. Menyusun jadwal berarti menentukan siapa bekerja kapan, dan itu
            // tetap milik manajer, pola yang sama dengan penanggung jawab area kebersihan.
            'security_shifts.read', 'security_shifts.update',
            'incident_reports.read', 'incident_reports.create', 'incident_reports.update',
            // Staf GA yang menerima surat di meja depan dan mencatat serah terimanya.
            // Tanpa delete, karena menghapus baris agenda meninggalkan lubang di urutan
            // nomor yang tidak bisa diterangkan kepada orang yang mencari suratnya.
            'letters.read', 'letters.create', 'letters.update',
            // Staf GA yang mengantar paket ke gerai dan menyalin angka dari resinya.
            'parcel_shipments.read', 'parcel_shipments.create', 'parcel_shipments.update',
            // Perjalanan dinas: staf GA mencatat pengajuan dan memeriksa pertanggungjawaban,
            // tetapi tidak menyetujui perjalanannya dan tidak menyerahkan uang mukanya.
            'business_trips.read', 'business_trips.read_all', 'business_trips.create',
            'business_trips.update', 'business_trips.verify',
            'maintenance_schedules.read', 'maintenance_schedules.create', 'maintenance_schedules.update',
            'work_orders.read', 'work_orders.create', 'work_orders.update',
            'service_request_categories.read',
            // read_all dan accept, tanpa approve. Staf GA menerima permintaan dan
            // mengubahnya menjadi pekerjaan, tetapi tanda tangan persetujuan tetap
            // milik kepala departemen pemohon.
            'service_requests.read', 'service_requests.read_all', 'service_requests.create',
            'service_requests.update', 'service_requests.accept',
            'vehicles.read', 'vehicles.create', 'vehicles.update',
            'vehicle_bookings.read', 'vehicle_bookings.read_all', 'vehicle_bookings.create',
            'vehicle_bookings.update', 'vehicle_bookings.assign',
            'expense_categories.read',
            'budgets.read',
            // Mencatat dan mengajukan tagihan, tanpa approve dan tanpa pay. Yang
            // memasukkan faktur tidak boleh sekaligus menyetujuinya.
            'vendor_bills.read', 'vendor_bills.create', 'vendor_bills.update',
            // verify tanpa approve dan tanpa pay. Staf GA memeriksa struknya, tetapi
            // tanda tangan atasan tetap milik kepala departemen pemohon.
            'reimbursements.read', 'reimbursements.read_all', 'reimbursements.create',
            'reimbursements.update', 'reimbursements.verify',
        ]);

        $this->syncRole('karyawan', [
            'name' => 'Karyawan',
            'description' => 'Melihat direktori karyawan dan daftar lokasi, serta mengajukan permintaan perbaikan.',
            'is_system' => false,
            'data_scope' => 'own',
            'is_active' => true,
        ], [
            'employees.read',
            'locations.read',
            'assets.read',
            /*
             * read tanpa read_all. Itu yang membuat karyawan hanya melihat permintaan
             * yang ia ajukan sendiri, ditambah permintaan departemen yang ia kepalai
             * kalau ia memang kepala departemen. Penyempitannya ada di
             * ServiceRequestResource::getEloquentQuery(), bukan di kolom data_scope.
             */
            'service_requests.read', 'service_requests.create',
            'vehicle_bookings.read', 'vehicle_bookings.create',
            /*
             * read tanpa read_all, sama seperti permintaan perbaikan. Karyawan hanya
             * melihat pengajuannya sendiri dan pengajuan departemen yang ia kepalai.
             * Kepala departemen tidak diberi izin approve: ia menyetujui karena namanya
             * tercatat sebagai penyetuju pengajuannya, bukan karena punya izin global.
             */
            'reimbursements.read', 'reimbursements.create', 'reimbursements.update',
            /*
             * read tanpa read_all, dan tanpa request_for_others. Itu yang membuat
             * karyawan biasa hanya bisa meminta ATK atas namanya sendiri, sementara
             * perwakilan departemen yang mengumpulkan kebutuhan seluruh timnya perlu
             * diberi izin request_for_others satu per satu lewat layar Role atau lewat
             * penambahan izin per pengguna.
             */
            'supply_requests.read', 'supply_requests.create', 'supply_requests.update',
            /*
             * read tanpa read_all, sama seperti permintaan perbaikan dan penggantian biaya.
             * Karyawan hanya melihat perjalanannya sendiri dan perjalanan departemen yang ia
             * kepalai. Kepala departemen menyetujui karena namanya tercatat sebagai penyetuju
             * pengajuan itu, bukan karena memegang izin approve.
             */
            'business_trips.read', 'business_trips.create', 'business_trips.update',
        ]);
    }

    /**
     * Izin ditambahkan, tidak menimpa. Seeder ini dijalankan lagi setiap ada tahap
     * baru, dan menimpa akan menghapus penyesuaian izin yang sudah dibuat admin
     * lewat layar Role.
     */
    protected function syncRole(string $code, array $attributes, array $permissionKeys): void
    {
        $existing = Role::query()->where('code', $code)->first();

        $role = $existing ?? Role::query()->create($attributes + ['code' => $code]);

        $role->addPermissionKeys($permissionKeys);
    }
}
