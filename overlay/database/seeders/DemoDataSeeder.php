<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder ini tidak lagi mengisi data apa pun.
 *
 * Data contoh sekarang dibuat oleh perintah gais:demo-data, yang mencakup
 * departemen, lokasi, karyawan, dan ratusan aset sekaligus, serta bisa dihapus
 * kembali dengan satu perintah. Seeder lama hanya membuat empat baris bertanda
 * CONTOH yang tidak nyambung dengan modul aset, jadi isinya dipindahkan.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->warn('Seeder ini sudah tidak dipakai.');
        $this->command?->line('Pakai perintah berikut untuk mengisi data demo:');
        $this->command?->line('  php artisan gais:coa-demo');
        $this->command?->line('  php artisan gais:demo-data');
        $this->command?->line('Menghapusnya kembali: php artisan gais:demo-data --hapus');
    }
}
