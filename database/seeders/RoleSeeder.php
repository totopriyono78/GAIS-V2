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
            'vendors.read', 'vendors.create', 'vendors.update',
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
            'vendors.read',
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
