<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Seeder sengaja tidak pernah mengganti kata sandi akun yang sudah ada, supaya
 * menjalankan ulang seeder di server tidak diam diam mereset akses orang.
 * Penggantian kata sandi harus tindakan yang disengaja, dan ini perintahnya.
 */
class SetAdminPassword extends Command
{
    protected $signature = 'gais:admin-password
        {email? : Email akun yang diganti kata sandinya, bawaannya dari GAIS_ADMIN_EMAIL di .env}
        {--password= : Isi kata sandi langsung tanpa diketik interaktif. Hati hati, nilainya tersimpan di riwayat perintah}';

    protected $description = 'Mengganti kata sandi satu akun pengguna';

    public function handle(): int
    {
        $email = $this->argument('email') ?: env('GAIS_ADMIN_EMAIL', 'admin@gais.test');

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error("Tidak ada akun dengan email {$email}.");

            $terdaftar = User::query()->orderBy('email')->pluck('email');

            if ($terdaftar->isEmpty()) {
                $this->line('Belum ada akun sama sekali. Jalankan php artisan db:seed --class=AdminUserSeeder dulu.');
            } else {
                $this->line('Akun yang terdaftar:');
                $terdaftar->each(fn (string $item) => $this->line("  {$item}"));
            }

            return self::FAILURE;
        }

        $password = $this->option('password');

        if ($password === null) {
            $password = $this->secret('Kata sandi baru, minimal 8 karakter. Ketikan tidak ditampilkan di layar');
            $ulangi = $this->secret('Ketik ulang kata sandi baru');

            if ($password !== $ulangi) {
                $this->error('Dua ketikan tidak sama. Kata sandi tidak diubah.');

                return self::FAILURE;
            }
        }

        if ($password === null || mb_strlen($password) < 8) {
            $this->error('Kata sandi minimal 8 karakter. Kata sandi tidak diubah.');

            return self::FAILURE;
        }

        // Kolom password memakai cast hashed, jadi nilai mentah di sini di-hash sekali oleh Eloquent.
        $user->password = $password;
        $user->save();

        $this->info("Kata sandi untuk {$email} sudah diganti.");

        if (! $user->is_active) {
            $this->warn('Akun ini berstatus nonaktif, jadi belum bisa masuk. Aktifkan dulu di layar Pengguna.');
        }

        if (! $user->is_super_admin && $user->roles()->count() === 0) {
            $this->warn('Akun ini bukan super admin dan belum punya role, jadi menunya akan kosong setelah masuk.');
        }

        return self::SUCCESS;
    }
}
