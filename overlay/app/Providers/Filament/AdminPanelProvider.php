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
                'IBM Plex Sans',
                url: asset('fonts/ibm-plex-sans/index.css'),
                provider: LocalFontProvider::class,
                // Dua ketebalan yang menutup hampir seluruh layar dimuat lebih awal,
                // supaya teks tidak sempat tergambar dengan huruf bawaan lalu berganti.
                preload: [
                    asset('fonts/ibm-plex-sans/files/ibm-plex-sans-latin-400-normal.woff2'),
                    asset('fonts/ibm-plex-sans/files/ibm-plex-sans-latin-500-normal.woff2'),
                ],
            )
            ->defaultAvatarProvider(InisialAvatar::class)
            // Layar kantor paling umum masih 1366 piksel. Sidebar yang selalu terbuka
            // memakan 16rem dari lebar itu, jadi tabel padat jadi terpotong. Dua baris
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
             * 16rem, bukan 20rem bawaan Filament.
             *
             * Layar kantor paling umum masih 1366 piksel, dan sidebar 20rem memakan 320
             * piksel darinya sebelum satu kolom tabel pun tergambar. Angka 16rem dipilih
             * dari pengukuran, bukan dari selera: nama menu terpanjang yang ada sekarang
             * membutuhkan 204 piksel setelah ikon dan bantalannya, dan 16rem menyisakan
             * 20 piksel di atas kebutuhan itu. Bantalan daftar menunya dikecilkan di
             * gais.css supaya sisa itu benar benar ada.
             */
            ->sidebarWidth('16rem')
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
             * Baris hak cipta di kaki halaman.
             *
             * Dipasang di dua tempat karena panel ini punya dua tata letak. FOOTER
             * mengisi halaman panel biasa, SIMPLE_LAYOUT_END mengisi halaman masuk yang
             * memakai tata letak sederhana dan tidak punya kaki halaman sendiri.
             * Memasang satu saja membuat baris ini hilang persis di halaman pertama yang
             * dilihat orang.
             */
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
            ->renderHook(PanelsRenderHook::FOOTER, fn (): string => view('filament.footer')->render())
            ->renderHook(PanelsRenderHook::SIMPLE_LAYOUT_END, fn (): string => view('filament.footer')->render());
    }

    /**
     * Palet lengkap, bukan satu warna dasar.
     *
     * Color::hex() hanya mengambil rona dari warna yang diberikan, lalu membangun
     * sendiri tingkat terang dan kepekatannya. Untuk #17505E hasilnya rona yang benar
     * tetapi kepekatannya naik dari 0,062 menjadi 0,169 dalam OKLCH, dan itu yang
     * membuat tombol tampil sian menyala, bukan petrol tua seperti di DESIGN.md.
     * Karena itu seluruh tingkat ditulis di sini. Warna asli dari DESIGN.md dipasang
     * pada tingkat yang tingkat terangnya paling dekat: 700 untuk primary dan gray,
     * 600 untuk success, danger, dan warning. Tingkat lain dihitung dari warna itu
     * dengan rona tetap dan kepekatan yang mengecil ke dua arah.
     *
     * Filament 5 memakai tingkat 400 sebagai latar tombol terang dan 950 sebagai
     * warna tulisannya, jadi tingkat 400 memang tampil sebagai petrol muda. Warna
     * penuh #17505E tetap muncul pada teks, nav aktif, dan garis fokus.
     *
     * @return array<string, array<int, string>>
     */
    protected function colors(): array
    {
        $petrol = [
            50 => '#F0F7FA',
            100 => '#E0EEF2',
            200 => '#C5DEE5',
            300 => '#A2C7D1',
            400 => '#7DABB8',
            500 => '#588D9C',
            600 => '#38707F',
            700 => '#17505E',
            800 => '#0B3F4B',
            900 => '#032E38',
            950 => '#001D24',
        ];

        return [
            'primary' => $petrol,
            // Info tidak disebut di DESIGN.md. Dipakaikan petrol yang sama supaya
            // tidak ada biru asing yang masuk lewat pintu belakang.
            'info' => $petrol,
            'gray' => [
                50 => '#F3F6F7',
                100 => '#E7ECEE',
                200 => '#D2DADE',
                300 => '#B6C2C7',
                400 => '#96A5AB',
                500 => '#76878E',
                600 => '#596A70',
                700 => '#3A4A50',
                800 => '#2D3A40',
                900 => '#1F2A2F',
                950 => '#121A1D',
            ],
            'success' => [
                50 => '#EFF9F1',
                100 => '#DEF1E3',
                200 => '#C2E2CB',
                300 => '#9ECDAC',
                400 => '#77B289',
                500 => '#509568',
                600 => '#1F6B3F',
                700 => '#0C5C32',
                800 => '#004521',
                900 => '#003315',
                950 => '#00200A',
            ],
            'danger' => [
                50 => '#FFF0ED',
                100 => '#FFE1DC',
                200 => '#FFC6BF',
                300 => '#FFA49A',
                400 => '#ED7C72',
                500 => '#D3554D',
                600 => '#A32020',
                700 => '#910B12',
                800 => '#710003',
                900 => '#570000',
                950 => '#3B0000',
            ],
            'warning' => [
                50 => '#FFF3ED',
                100 => '#FDE5DB',
                200 => '#F7CFBD',
                300 => '#EAB198',
                400 => '#D58F6F',
                500 => '#BA6D49',
                600 => '#A2542F',
                700 => '#7C330B',
                800 => '#602100',
                900 => '#491500',
                950 => '#310A00',
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
