<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Nilai identitas perusahaan sengaja dikosongkan. Mengarang nama, alamat, atau
 * nomor telepon perusahaan akan terbawa ke dokumen cetak nanti.
 */
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'group' => 'perusahaan',
                'key' => 'perusahaan.nama',
                'label' => 'Nama perusahaan',
                'description' => 'Dipakai pada kop laporan dan dokumen cetak.',
                'type' => 'text',
                'value' => null,
            ],
            [
                'group' => 'perusahaan',
                'key' => 'perusahaan.nama_singkat',
                'label' => 'Nama singkat',
                'description' => 'Muncul di sudut kiri atas aplikasi.',
                'type' => 'text',
                'value' => 'GAIS',
            ],
            [
                'group' => 'perusahaan',
                'key' => 'perusahaan.alamat',
                'label' => 'Alamat Head Office',
                'description' => 'Alamat lengkap kantor pusat.',
                'type' => 'textarea',
                'value' => null,
            ],
            [
                'group' => 'perusahaan',
                'key' => 'perusahaan.telepon',
                'label' => 'Telepon kantor',
                'description' => null,
                'type' => 'text',
                'value' => null,
            ],
            [
                'group' => 'perusahaan',
                'key' => 'perusahaan.email',
                'label' => 'Email GA',
                'description' => 'Alamat email tim GA untuk pemberitahuan.',
                'type' => 'text',
                'value' => null,
            ],
            [
                'group' => 'perusahaan',
                'key' => 'perusahaan.logo',
                'label' => 'Berkas logo',
                'description' => 'Belum ada berkas logo. Isi setelah file logo perusahaan tersedia.',
                'type' => 'text',
                'value' => '[LOGO]',
            ],
            [
                'group' => 'label',
                'key' => 'label.lebar_mm',
                'label' => 'Lebar label (mm)',
                'description' => 'Ukuran satu stiker. Bawaannya 70, sesuai lembar stiker A4 isi 24.',
                'type' => 'number',
                'value' => '70',
            ],
            [
                'group' => 'label',
                'key' => 'label.tinggi_mm',
                'label' => 'Tinggi label (mm)',
                'description' => 'Bawaannya 37, sesuai lembar stiker A4 isi 24.',
                'type' => 'number',
                'value' => '37',
            ],
            [
                'group' => 'label',
                'key' => 'label.kolom',
                'label' => 'Jumlah label per baris',
                'description' => 'Bawaannya 3 kolom.',
                'type' => 'number',
                'value' => '3',
            ],
            [
                'group' => 'label',
                'key' => 'label.margin_atas_mm',
                'label' => 'Margin atas halaman (mm)',
                'description' => 'Jarak dari tepi atas kertas ke label pertama. Sesuaikan kalau cetakan bergeser.',
                'type' => 'number',
                'value' => '8',
            ],
            [
                'group' => 'label',
                'key' => 'label.margin_kiri_mm',
                'label' => 'Margin kiri halaman (mm)',
                'description' => 'Jarak dari tepi kiri kertas ke kolom pertama.',
                'type' => 'number',
                'value' => '5',
            ],

            /*
             * Kebijakan penyusutan. Bawaannya mengikuti kebiasaan pajak Indonesia, sesuai
             * pilihan pemilik proyek pada 6 September 2026, dan ketiganya bisa diubah dari
             * layar ini tanpa menyentuh kode. Yang perlu diingat: mengubah kebijakan hanya
             * berpengaruh pada periode yang belum ditutup. Periode yang sudah ditutup
             * menyimpan angkanya sendiri dan tidak ikut berubah.
             */
            [
                'group' => 'penyusutan',
                'key' => 'penyusutan.mulai',
                'label' => 'Kapan penyusutan mulai dihitung',
                'description' => 'Pajak Indonesia menghitung penyusutan sejak bulan pengeluaran dilakukan, sebulan penuh, tanpa memandang tanggalnya. Pilihan kedua menunda ke bulan berikutnya, yang lebih umum dipakai pembukuan komersial.',
                'type' => 'pilihan',
                'value' => 'bulan_perolehan',
            ],
            [
                'group' => 'penyusutan',
                'key' => 'penyusutan.nilai_sisa',
                'label' => 'Nilai sisa bawaan',
                'description' => 'Penyusutan fiskal menyusutkan seluruh nilai perolehan sampai habis, jadi bawaannya nol. Pilihan kedua memakai persen nilai sisa yang diatur per kategori aset. Nilai sisa yang diisi langsung pada satu aset selalu menang atas keduanya.',
                'type' => 'pilihan',
                'value' => 'nol',
            ],
            [
                'group' => 'penyusutan',
                'key' => 'penyusutan.saldo_menurun_akhir',
                'label' => 'Saldo menurun di tahun terakhir',
                'description' => 'Saldo menurun murni tidak pernah benar benar habis, karena tiap tahun hanya mengambil sebagian dari sisanya. Kebiasaan pajak menghabiskan sisanya di tahun terakhir. Hanya berlaku untuk aset bermetode saldo menurun ganda.',
                'type' => 'pilihan',
                'value' => 'habiskan',
            ],
            [
                'group' => 'penyusutan',
                'key' => 'penyusutan.periode_mulai',
                'label' => 'Periode pertama yang dihitung GAIS',
                'description' => 'Bulan pertama yang penyusutannya dicatat di aplikasi ini, ditulis YYYY-MM, contohnya 2026-09. Bulan bulan sebelum ini dianggap sudah dicatat di tempat lain, dan angkanya masuk sebagai akumulasi awal tiap aset. Kosongkan untuk memakai bulan berjalan saat periode pertama ditutup.',
                'type' => 'text',
                'value' => null,
            ],
            [
                'group' => 'layanan',
                'key' => 'layanan.mendesak_lewati_persetujuan',
                'label' => 'Permintaan mendesak',
                'description' => 'Kebocoran air, listrik mati, dan lift berhenti tidak bisa menunggu atasan membuka aplikasi. Bawaannya, permintaan berprioritas mendesak langsung masuk antrean tim GA, dan alasan lompatan itu tertulis di tiketnya sehingga tetap terbaca siapa pun yang membukanya. Ubah ke pilihan kedua kalau perusahaan menghendaki semua permintaan lewat persetujuan tanpa kecuali.',
                'type' => 'pilihan',
                'value' => 'ya',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
