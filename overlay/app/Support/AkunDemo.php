<?php

namespace App\Support;

use App\Models\User;

/**
 * Akun demo, yaitu empat akun berperan berbeda untuk memperlihatkan sistem ini
 * dari sudut pandang yang berbeda dalam satu sesi.
 *
 * Daftarnya ada di satu tempat, di sini, dan dipakai dua pihak: DemoUserSeeder yang
 * membuat akunnya, dan tampilan di bawah formulir masuk yang menawarkannya untuk
 * diklik. Menaruhnya di dua tempat berarti suatu saat layar akan menawarkan akun
 * yang tidak pernah dibuat, dan orang yang mengkliknya hanya melihat penolakan
 * masuk tanpa keterangan apa apa.
 *
 * Kata sandinya sama untuk keempatnya. Kata sandi yang berbeda beda tidak menambah
 * pengamanan apa pun di sini, karena keduanya sama sama tertulis di layar, dan hanya
 * menambah satu hal yang bisa salah ketik saat seseorang mencoba dari ponselnya.
 */
class AkunDemo
{
    /**
     * @var array<int, array<string, string|null>>
     */
    public const AKUN = [
        [
            'email' => 'admin.demo@gais.test',
            'nama' => 'Demo Administrator',
            'peran' => 'administrator',
            'peran_label' => 'Administrator Sistem',
            'ringkas' => 'Seluruh modul terbuka, termasuk pengaturan, peran, dan hak akses.',
            'departemen' => null,
        ],
        [
            'email' => 'manajer.demo@gais.test',
            'nama' => 'Demo Manajer GA',
            'peran' => 'manajer-ga',
            'peran_label' => 'Manajer GA',
            'ringkas' => 'Menyetujui permintaan, memeriksa anggaran, mengubah data induk. Tidak mengelola akses pengguna.',
            'departemen' => 'General Affair',
        ],
        [
            'email' => 'staf.demo@gais.test',
            'nama' => 'Demo Staf GA',
            'peran' => 'staf-ga',
            'peran_label' => 'Staf GA',
            'ringkas' => 'Pekerjaan harian tim GA: mencatat aset, menyerahkan barang, mengerjakan tiket.',
            'departemen' => 'General Affair',
        ],
        [
            'email' => 'karyawan.demo@gais.test',
            'nama' => 'Demo Karyawan',
            'peran' => 'karyawan',
            'peran_label' => 'Karyawan',
            'ringkas' => 'Sudut pandang pemohon. Hanya melihat pengajuan yang ia buat sendiri.',
            'departemen' => 'Finance',
        ],
    ];

    public static function menyala(): bool
    {
        return (bool) config('gais.demo_login', false);
    }

    public static function kataSandi(): string
    {
        return (string) config('gais.demo_password', 'demo1234');
    }

    /**
     * Akun yang benar benar ada di basis data dan masih aktif.
     *
     * Yang tidak ada tidak ditawarkan. Menawarkan akun yang belum pernah dibuat
     * berarti menyodorkan tombol yang berujung pada penolakan masuk tanpa keterangan,
     * dan itu persis jenis tombol yang dilarang di proyek ini (R-26).
     *
     * @return array<int, array<string, string|null>>
     */
    public static function tersedia(): array
    {
        if (! static::menyala()) {
            return [];
        }

        $surel = User::query()
            ->whereIn('email', array_column(self::AKUN, 'email'))
            ->where('is_active', true)
            ->pluck('email')
            ->all();

        if ($surel === []) {
            return [];
        }

        return array_values(array_filter(
            self::AKUN,
            fn (array $akun): bool => in_array($akun['email'], $surel, true),
        ));
    }
}
