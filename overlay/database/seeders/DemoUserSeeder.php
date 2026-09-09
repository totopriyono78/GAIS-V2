<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Support\AkunDemo;
use Illuminate\Database\Seeder;

/**
 * Empat akun demo, satu untuk tiap peran.
 *
 * Sengaja tidak dipanggil DatabaseSeeder. Akun ini hanya pantas ada di pemasangan
 * yang memang dipakai memperagakan sistem, dan menjalankannya harus keputusan sadar
 * seseorang, bukan efek samping dari deploy.
 *
 * Menjalankannya berulang kali aman. Kata sandinya sengaja disetel ulang tiap kali,
 * supaya kata sandi yang tertulis di layar masuk selalu benar benar bisa dipakai.
 *
 * Menghapusnya kembali cukup dengan menghapus keempat akunnya lewat menu Users.
 * Kartu karyawan yang tadi ditautkan ikut lepas sendiri, karena kolom penautnya
 * dibuat kosong saat penggunanya hilang.
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $kataSandi = AkunDemo::kataSandi();

        foreach (AkunDemo::AKUN as $akun) {
            $peran = Role::query()->where('code', $akun['peran'])->first();

            if ($peran === null) {
                $this->command?->error("Peran {$akun['peran']} belum ada. Jalankan RoleSeeder lebih dulu.");

                continue;
            }

            $pengguna = User::query()->updateOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['nama'],
                    'password' => $kataSandi,
                    /*
                     * Hanya akun administrator yang diberi tanda super.
                     *
                     * Tiga akun lainnya sengaja tidak, karena justru batas izinnya yang
                     * ingin diperlihatkan: menu yang tidak muncul dan tombol yang tidak
                     * ada adalah bagian dari yang sedang diperagakan.
                     */
                    'is_super_admin' => $akun['peran'] === 'administrator',
                    'is_active' => true,
                ],
            );

            $pengguna->roles()->sync([$peran->getKey()]);

            $this->tautkanKaryawan($pengguna, $akun);

            $this->command?->info("Akun demo siap: {$akun['email']} sebagai {$akun['peran_label']}");
        }

        $this->command?->newLine();
        $this->command?->warn("Kata sandi keempatnya: {$kataSandi}");
        $this->command?->line('Daftarnya baru muncul di halaman masuk kalau GAIS_DEMO_LOGIN bernilai true.');
    }

    /**
     * Menautkan akun ke satu kartu karyawan yang belum punya akun.
     *
     * Tanpa kartu karyawan, tiga modul yang menyempitkan daftarnya berdasarkan pemohon,
     * yaitu permintaan perbaikan, penggantian biaya, dan perjalanan dinas, akan
     * menampilkan daftar kosong bagi akun yang tidak punya izin read_all. Saat
     * diperagakan, layar kosong itu terbaca sebagai sistem yang rusak, padahal
     * penyempitannya justru sedang bekerja dengan benar.
     *
     * @param  array<string, string|null>  $akun
     */
    protected function tautkanKaryawan(User $pengguna, array $akun): void
    {
        if ($akun['departemen'] === null) {
            return;
        }

        // Sudah tertaut dari penjalanan sebelumnya.
        if ($pengguna->employee()->exists()) {
            return;
        }

        $departemen = Department::query()->where('name', $akun['departemen'])->first();

        $karyawan = Employee::query()
            ->whereNull('user_id')
            ->where('is_active', true)
            ->when($departemen !== null, fn ($query) => $query->where('department_id', $departemen->getKey()))
            // Urutan nama dipakai supaya penjalanan yang sama pada basis data yang sama
            // selalu memilih orang yang sama, bukan orang yang berganti ganti.
            ->orderBy('full_name')
            ->first();

        if ($karyawan === null) {
            $this->command?->warn(
                "Tidak ada kartu karyawan yang belum berakun di departemen {$akun['departemen']}, "
                ."jadi {$akun['email']} dibiarkan tanpa kartu karyawan."
            );

            return;
        }

        $karyawan->forceFill(['user_id' => $pengguna->getKey()])->save();

        $this->command?->line("  ditautkan ke kartu karyawan {$karyawan->full_name}");
    }
}
