<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('GAIS_ADMIN_EMAIL', 'admin@gais.test');
        $existing = User::query()->where('email', $email)->first();

        if ($existing !== null) {
            $this->command?->info("Akun {$email} sudah ada, kata sandinya sengaja tidak diubah.");
            $this->command?->comment('Untuk mengganti kata sandinya, jalankan: php artisan gais:admin-password');

            $existing->roles()->syncWithoutDetaching(
                Role::query()->where('code', 'administrator')->pluck('id')->all(),
            );

            return;
        }

        $password = env('GAIS_ADMIN_PASSWORD');
        $generated = $password === null;
        $password = $password ?? Str::password(14);

        $user = User::query()->create([
            'name' => env('GAIS_ADMIN_NAME', 'Administrator'),
            'email' => $email,
            'password' => $password,
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $user->roles()->sync(Role::query()->where('code', 'administrator')->pluck('id')->all());

        $this->command?->info("Akun administrator dibuat: {$email}");

        if ($generated) {
            $this->command?->warn("Kata sandi acak: {$password}");
            $this->command?->warn('Catat sekarang, kata sandi ini tidak ditampilkan lagi. Ganti setelah masuk pertama kali.');
        }
    }
}
