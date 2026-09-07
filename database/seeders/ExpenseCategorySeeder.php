<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

/**
 * Kategori biaya GA yang lazim ada.
 *
 * Empat kategori pertama disambungkan ke catatan pekerjaan sehari hari, dan realisasinya
 * sudah lengkap pada hari kejadiannya. Enam sisanya bersumber tagihan: angkanya juga
 * dijumlahkan sendiri, tetapi baru setelah faktur rekanannya masuk dan disetujui.
 *
 * Nomor akun sengaja dikosongkan seluruhnya. Nomor akun adalah milik bagan akun
 * perusahaan, dan mengarangnya berarti menaruh angka palsu di jalur yang berujung ke
 * jurnal akuntansi.
 */
class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'PMLH', 'name' => 'Pemeliharaan gedung dan peralatan', 'source' => 'pemeliharaan',
                'description' => 'Servis, suku cadang, dan jasa tukang. Dijumlahkan dari perintah kerja dan kunjungan pemeliharaan yang sudah selesai.'],
            ['code' => 'BBM', 'name' => 'Bahan bakar kendaraan', 'source' => 'bbm',
                'description' => 'Dijumlahkan dari pengisian BBM yang dicatat pada tiap kendaraan.'],
            ['code' => 'DOKKEN', 'name' => 'Pajak dan dokumen kendaraan', 'source' => 'dokumen_kendaraan',
                'description' => 'Pajak tahunan, perpanjangan STNK, KIR, dan asuransi. Dibebankan menurut tanggal terbit dokumennya.'],
            ['code' => 'ATK', 'name' => 'Alat tulis dan barang habis pakai', 'source' => 'persediaan',
                'description' => 'Dijumlahkan dari barang yang keluar gudang, dinilai dengan harga pada mutasinya atau harga pembelian terakhir.'],
            ['code' => 'LSTR', 'name' => 'Listrik, air, dan telepon', 'source' => 'tagihan',
                'description' => 'Tagihan utilitas bulanan, dijumlahkan dari faktur rekanan yang sudah disetujui.'],
            ['code' => 'KBRS', 'name' => 'Kebersihan dan keamanan', 'source' => 'tagihan',
                'description' => 'Jasa cleaning service dan satpam, dijumlahkan dari faktur rekanan yang sudah disetujui.'],
            ['code' => 'SEWA', 'name' => 'Sewa gedung dan peralatan', 'source' => 'tagihan',
                'description' => 'Sewa ruang, mesin fotokopi, dan peralatan lain, dijumlahkan dari faktur rekanan yang sudah disetujui.'],
            ['code' => 'RMTG', 'name' => 'Rumah tangga kantor', 'source' => 'tagihan',
                'description' => 'Konsumsi rapat, galon, dan keperluan harian kantor, dijumlahkan dari faktur rekanan yang sudah disetujui.'],
            ['code' => 'TRNS', 'name' => 'Transportasi dan perjalanan', 'source' => 'tagihan',
                'description' => 'Transportasi daring, taksi, dan tol. Dijumlahkan dari faktur rekanan, dan nanti juga dari penggantian biaya karyawan.'],
            ['code' => 'LAIN', 'name' => 'Biaya GA lainnya', 'source' => 'tagihan',
                'description' => 'Biaya yang belum masuk kategori mana pun.'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::query()->updateOrCreate(
                ['code' => $category['code']],
                $category + ['is_active' => true],
            );
        }
    }
}
