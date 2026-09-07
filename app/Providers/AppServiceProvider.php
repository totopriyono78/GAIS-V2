<?php

namespace App\Providers;

use App\Models\User;
use App\Services\PenyusutanAset;
use App\Services\RealisasiBiaya;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
         * Satu mesin penyusutan per permintaan, bukan satu per pemanggilan.
         *
         * Kolom di layar daftar memanggilnya berkali kali untuk baris yang sama, sekali
         * untuk nilainya, sekali untuk warnanya, sekali untuk keterangannya. Tanpa ini,
         * tiap pemanggilan membaca ulang pengaturan dan mencari ulang entri terakhir tiap
         * aset, dan satu halaman berisi lima puluh baris berubah menjadi ratusan kueri.
         */
        $this->app->singleton(PenyusutanAset::class);
        // Sama alasannya dengan penyusutan: satu halaman anggaran membaca realisasi
        // berkali kali, dan tanpa singleton ingatannya hilang pada tiap baris.
        $this->app->singleton(RealisasiBiaya::class);
    }

    public function boot(): void
    {
        $this->registerAuthorization();
        $this->registerLoginAudit();

        FilamentAsset::register([
            Css::make('gais', resource_path('css/gais.css')),
        ]);
    }

    /**
     * Izin disimpan sebagai kunci berbentuk kode_modul.aksi, misalnya employees.create.
     * Gate::before menerjemahkan kunci itu supaya $user->can('employees.create') bekerja,
     * tanpa perlu mendaftarkan ratusan gate satu per satu saat boot.
     */
    protected function registerAuthorization(): void
    {
        Gate::before(function (User $user, string $ability) {
            if ($user->is_super_admin) {
                return true;
            }

            if (! str_contains($ability, '.')) {
                return null;
            }

            return $user->hasPermission($ability) ? true : null;
        });
    }

    protected function registerLoginAudit(): void
    {
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;

            if (! $user instanceof User) {
                return;
            }

            $user->forceFill(['last_login_at' => now()])->saveQuietly();

            $this->writeAuthLog('login', $user);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user instanceof User) {
                $this->writeAuthLog('logout', $event->user);
            }
        });
    }

    protected function writeAuthLog(string $event, User $user): void
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => $user->getKey(),
                'user_name' => $user->name,
                'event' => $event,
                'auditable_type' => User::class,
                'auditable_id' => $user->getKey(),
                'auditable_label' => $user->name,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
