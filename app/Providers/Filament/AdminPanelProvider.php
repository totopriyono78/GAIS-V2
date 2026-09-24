<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use App\Support\InisialAvatar;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dasbor;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->brandName($this->brandName())
            ->colors($this->colors())
            /*
             * Huruf dan avatar diinangkan sendiri, tidak diambil dari internet.
             *
             * Bawaan Filament memanggil fonts.bunny.net untuk huruf dan ui-avatars.com
             * untuk avatar. Di jaringan kantor yang tidak punya akses keluar, keduanya
             * gagal: huruf jatuh ke bawaan sistem sehingga tampilan berbeda antar
             * komputer, dan avatar hilang sama sekali. Yang kedua juga mengirim nama
             * setiap karyawan ke layanan pihak ketiga tanpa alasan yang sepadan.
             */
            ->font(
                'Montserrat',
                url: asset('fonts/montserrat/index.css'),
                provider: LocalFontProvider::class,
                // Dua ketebalan yang menutup hampir seluruh layar dimuat lebih awal,
                // supaya teks tidak sempat tergambar dengan huruf bawaan lalu berganti.
                preload: [
                    asset('fonts/montserrat/files/montserrat-latin-400-normal.woff2'),
                    asset('fonts/montserrat/files/montserrat-latin-500-normal.woff2'),
                ],
            )
            ->defaultAvatarProvider(InisialAvatar::class)
            // Layar kantor paling umum masih 1366 piksel. Sidebar yang selalu terbuka
            // memakan 20rem dari lebar itu, jadi tabel padat jadi terpotong. Dua baris
            // di bawah mengembalikan ruang itu ke isi halaman.
            /*
             * Halaman detail di aplikasi ini adalah meja kerja, bukan arsip.
             *
             * Bawaan Filament membuat seluruh relation manager di halaman Lihat menjadi
             * hanya baca, sehingga tombol Tambah, Ubah, dan Hapus di dalamnya hilang
             * tanpa pesan apa pun. Untuk aplikasi ini itu salah: halaman satu kendaraan
             * adalah tempat mencatat perpanjangan pajak, halaman satu permintaan adalah
             * tempat mengunggah foto keadaan, dan halaman satu jadwal adalah tempat
             * menutup kunjungan. Izin sebenarnya tetap dijaga oleh visible() pada tiap
             * tombol, yang membaca izin modul, bukan oleh sifat halamannya.
             */
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            /*
             * 20rem, sama dengan bawaan Filament. Sebelumnya 16rem.
             *
             * Montserrat, huruf gaya Vuexy yang dipakai sejak 24 September 2026, jauh lebih
             * lebar dari IBM Plex Sans. Pada 16rem, Preventive Maintenance dan Corrective
             * Maintenance terpotong menjadi elipsis. Diukur ulang di layar 1440 piksel:
             * pada 19,5rem nama terpanjang, Corrective Maintenance beserta badge jumlahnya,
             * hanya menyisakan 13 piksel, di bawah batas 20 piksel yang dipakai sejak awal
             * supaya pembulatan lebar huruf antar komputer tidak memotongnya. Pada 20rem
             * sisanya 21 piksel.
             *
             * Harganya 64 piksel lebar tabel di layar kantor 1366 piksel. Sidebar tetap
             * bisa diciutkan dari tombol di navbar untuk halaman yang butuh lebar penuh.
             */
            ->sidebarWidth('20rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->navigationGroups([
                'Assets',
                'Maintenance',
                'Vehicles',
                // Ditaruh setelah Vehicles, bersama dua kelompok di atasnya yang sama sama
                // mengurus gedung dan isinya. Kelompok yang tidak disebut di daftar ini
                // ditempatkan Filament di paling bawah, jadi menghilangkannya dari sini
                // berarti menaruh pekerjaan harian tim GA di bawah Pengaturan.
                'Facility Services',
                'Correspondence',
                // Ditaruh setelah Correspondence karena keduanya mengurus kertas, tetapi
                // dua hal yang berbeda: yang satu perpindahan fisik amplop, yang satu isi
                // dan keabsahan dokumennya. Mereka memang tidak boleh digabung.
                'Documents',
                'Office Supplies',
                'Budget & Expenses',
                'Master Data',
                'Access Control',
                'System',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Dasbor bawaan diganti versi bertab. Kelasnya tetap turunan Dashboard,
                // jadi alamat dan perannya sebagai halaman depan panel tidak berubah.
                Dasbor::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // Widget didaftarkan per tab di App\Filament\Pages\Dasbor, bukan di sini,
            // supaya tiap tab hanya memuat widget miliknya sendiri.
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                // Laravel 13 mengganti nama VerifyCsrfToken menjadi PreventRequestForgery,
                // dan menambahkan pemeriksaan asal permintaan lewat header Sec-Fetch-Site.
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            /*
             * Peluncur menu di ujung kiri topbar.
             *
             * Isinya tidak ditulis di mana pun. Ia dibaca dari navigasi panel yang sama
             * dengan yang menggambar sidebar, jadi menu yang disembunyikan izin juga
             * hilang dari peluncur, dan modul baru muncul di keduanya sekaligus. Ini yang
             * membuat aturan "menu tidak pernah ditulis manual" tetap berlaku.
             */
            ->renderHook(PanelsRenderHook::TOPBAR_START, fn (): string => view('filament.peluncur-menu')->render())
            /*
             * Daftar akun demo di bawah formulir masuk.
             *
             * Tampilannya sendiri yang memutuskan menggambar atau tidak, dan bawaannya
             * tidak. Ia hanya muncul kalau GAIS_DEMO_LOGIN menyala dan akun demonya
             * benar benar ada di basis data, karena menawarkan akun yang belum pernah
             * dibuat berarti menyodorkan tombol yang berujung penolakan masuk.
             */
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn (): string => view('filament.akun-demo')->render())
            /*
             * Baris hak cipta di kaki halaman.
             *
             * Cukup satu kait. Sejak Filament 5.7 tata letak sederhana (halaman masuk)
             * ikut menggambar kait FOOTER, jadi kait SIMPLE_LAYOUT_END yang dulu dipasang
             * untuk halaman masuk membuat baris hak cipta tampil dua kali di sana.
             */
            ->renderHook(PanelsRenderHook::FOOTER, fn (): string => view('filament.footer')->render());
    }

    /**
     * Palet lengkap gaya Vuexy, bukan satu warna dasar.
     *
     * Arah gaya diganti ke Vuexy atas permintaan pemilik proyek pada 24 September 2026.
     * Seluruh tingkat tetap ditulis tangan karena Color::hex() membangun tingkatnya
     * sendiri dan hasilnya tidak bisa diatur kontrasnya.
     *
     * Ungu Vuexy asli #7367F0 dipasang di tingkat 400. Ia tampil di tempat yang tidak
     * membawa teks kecil: garis fokus, cahaya menu aktif, lingkaran ikon statistik.
     * Tombol memakai tingkat 600 dan hover 500, jadi keduanya sengaja digeser sedikit
     * lebih gelap supaya tulisan putih di atasnya lolos 4,5:1 (5,68 dan 4,88). Ungu
     * #7367F0 sendiri hanya 4,26 dengan tulisan putih.
     *
     * Gray adalah abu keunguan Vuexy. Tingkat 900 dan 950 sekaligus menjadi warna kartu
     * dan latar tema gelap (#283046 dan #161D31), sama seperti dark layout Vuexy, karena
     * Filament melukis tema gelap dari dua tingkat itu.
     *
     * Merah dipasang lebih gelap dari Vuexy (#EA5455 di tingkat 400) karena tombol hapus
     * bertulisan putih. Hijau, jingga, dan biru muda memakai warna Vuexy apa adanya di
     * tingkat 500, dan Filament otomatis memberi tulisan gelap pada tombolnya karena
     * tulisan putih tidak cukup kontras di atas ketiganya.
     *
     * @return array<string, array<int, string>>
     */
    protected function colors(): array
    {
        return [
            'primary' => [
                50 => '#F2F1FE',
                100 => '#E6E3FD',
                200 => '#CCC7FA',
                300 => '#ADA5F6',
                400 => '#7367F0',
                500 => '#685BED',
                600 => '#5D4FE6',
                700 => '#4E41CC',
                800 => '#3F34A6',
                900 => '#2F2780',
                950 => '#1F1A57',
            ],
            'gray' => [
                50 => '#F8F8F8',
                100 => '#F3F2F7',
                200 => '#EBE9F1',
                300 => '#D8D6DE',
                400 => '#B9B9C3',
                500 => '#6E6B7B',
                600 => '#625F6E',
                700 => '#5E5873',
                800 => '#3B4253',
                900 => '#283046',
                950 => '#161D31',
            ],
            'success' => [
                50 => '#EEFBF3',
                100 => '#DDF6E8',
                200 => '#BAEDD1',
                300 => '#94E3B7',
                400 => '#60D694',
                500 => '#28C76F',
                600 => '#25AF66',
                700 => '#23945C',
                800 => '#1B7A45',
                900 => '#1D614A',
                950 => '#1B4941',
            ],
            'danger' => [
                50 => '#FDF1F1',
                100 => '#FCE4E4',
                200 => '#F8C8C9',
                300 => '#F4AAAA',
                400 => '#EA5455',
                500 => '#D83A3B',
                600 => '#C03234',
                700 => '#A12B2E',
                800 => '#822427',
                900 => '#651D20',
                950 => '#461416',
            ],
            'warning' => [
                50 => '#FFF7F0',
                100 => '#FFF0E1',
                200 => '#FFE0C3',
                300 => '#FFCFA1',
                400 => '#FFB874',
                500 => '#FF9F43',
                600 => '#DE8D40',
                700 => '#B9783E',
                800 => '#875833',
                900 => '#735138',
                950 => '#533F36',
            ],
            'info' => [
                50 => '#EBFBFD',
                100 => '#D6F7FB',
                200 => '#ADF0F8',
                300 => '#80E7F4',
                400 => '#42DBEE',
                500 => '#00CFE8',
                600 => '#03B6CE',
                700 => '#079AB1',
                800 => '#0A7D94',
                900 => '#0D647A',
                950 => '#104B61',
            ],
        ];
    }

    /**
     * Nama merek diambil dari pengaturan sistem supaya bisa diubah dari layar,
     * bukan dari kode. Kalau tabel pengaturan belum ada (saat migrate pertama),
     * dipakai nama produk apa adanya.
     */
    protected function brandName(): string
    {
        try {
            return Setting::get('perusahaan.nama_singkat', 'GAIS');
        } catch (\Throwable) {
            return 'GAIS';
        }
    }
}
