<?php

namespace Database\Seeders;

use App\Models\ServiceRequestCategory;
use Illuminate\Database\Seeder;

/**
 * Jenis permintaan perbaikan yang lazim ada di gedung kantor.
 *
 * Target waktu sengaja dibiarkan kosong. Berapa jam sebuah AC seharusnya selesai
 * diperbaiki adalah janji yang dibuat perusahaan kepada karyawannya, dan angka itu
 * akan dikutip orang saat menilai kinerja tim GA. Mengarangnya di sini berarti
 * menaruh janji yang tidak pernah disepakati siapa pun ke layar. Isi lewat menu
 * Jenis permintaan setelah tim GA menyepakatinya.
 *
 * Prioritas bawaan boleh diisi, karena itu bukan janji, melainkan titik awal yang
 * masih bisa dinaikkan pemohon saat membuat tiket.
 */
class ServiceRequestCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'LST', 'name' => 'Listrik dan penerangan', 'default_priority' => 'tinggi',
                'description' => 'Lampu mati, stopkontak tidak berfungsi, MCB turun berulang.'],
            ['code' => 'AC', 'name' => 'Pendingin ruangan', 'default_priority' => 'normal',
                'description' => 'AC tidak dingin, bocor, atau berisik.'],
            ['code' => 'AIR', 'name' => 'Air dan sanitasi', 'default_priority' => 'tinggi',
                'description' => 'Kebocoran pipa, keran rusak, toilet mampet.'],
            ['code' => 'MBL', 'name' => 'Meja, kursi, dan lemari', 'default_priority' => 'rendah',
                'description' => 'Perabot kantor yang rusak, goyang, atau perlu dipindahkan.'],
            ['code' => 'PTU', 'name' => 'Pintu, kunci, dan jendela', 'default_priority' => 'normal',
                'description' => 'Kunci macet, engsel lepas, kaca retak.'],
            ['code' => 'KBR', 'name' => 'Kebersihan', 'default_priority' => 'normal',
                'description' => 'Permintaan pembersihan di luar jadwal rutin.'],
            ['code' => 'JRG', 'name' => 'Jaringan dan kelistrikan data', 'default_priority' => 'tinggi',
                'description' => 'Titik LAN mati, kabel putus, rak jaringan. Perangkatnya sendiri urusan tim IT.'],
            ['code' => 'LFT', 'name' => 'Lift dan mesin gedung', 'default_priority' => 'mendesak',
                'description' => 'Lift berhenti, genset, pompa, atau mesin gedung lain yang berhenti bekerja.'],
            ['code' => 'LAIN', 'name' => 'Lainnya', 'default_priority' => 'normal',
                'description' => 'Permintaan yang belum masuk jenis mana pun. Tim GA memindahkannya saat menerima.'],
        ];

        foreach ($categories as $category) {
            ServiceRequestCategory::query()->updateOrCreate(
                ['code' => $category['code']],
                $category + ['is_active' => true],
            );
        }
    }
}
