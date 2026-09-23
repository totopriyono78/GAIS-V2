<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleSeeder::class,
            RoleSeeder::class,
            SettingSeeder::class,
            AssetCategorySeeder::class,
            ServiceRequestCategorySeeder::class,
            ExpenseCategorySeeder::class,
            NumberSequenceSeeder::class,
            // Dipanggil setelah NumberSequenceSeeder, karena menyimpan satu jenis
            // dokumen ikut mendaftarkan urutan nomornya ke tabel yang sama.
            DocumentTypeSeeder::class,
            DocumentCategorySeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
