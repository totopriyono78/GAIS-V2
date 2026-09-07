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
            AdminUserSeeder::class,
        ]);
    }
}
