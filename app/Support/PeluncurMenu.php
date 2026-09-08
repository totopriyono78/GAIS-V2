<?php

namespace App\Support;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Support\Str;

/**
 * Bahan untuk peluncur menu, yaitu jendela berisi seluruh menu dalam bentuk ubin.
 *
 * Daftarnya tidak ditulis di sini. Ia dibaca dari navigasi panel yang sama dengan yang
 * menggambar sidebar, jadi menu yang tidak boleh dibuka seseorang juga tidak muncul di
 * peluncur, dan modul baru ikut muncul tanpa berkas ini disentuh. Itu yang menjaga R-24
 * dan sekaligus menjaga peluncur ini tidak pernah berbeda isi dengan sidebar.
 */
class PeluncurMenu
{
    /**
     * Kelompok yang hanya ditampilkan sebagai satu ubin, beserta ikonnya.
     *
     * Ketiganya adalah kelompok penyiapan, bukan pekerjaan harian: dibuka sesekali saat
     * menyetel sistem, lalu ditinggalkan. Menaruh seluruh isinya di layar pertama membuat
     * menu yang dipakai tiap hari harus dicari di antara menu yang dipakai setahun sekali.
     * Keputusan pemilik proyek pada 8 September 2026.
     *
     * Ikonnya ditentukan di sini karena kelompok navigasi di panel ini ditulis sebagai teks
     * biasa dan memang tidak punya ikon sendiri.
     *
     * @var array<string, string>
     */
    public const KELOMPOK_DIRINGKAS = [
        'Master Data' => 'heroicon-o-circle-stack',
        'Access Control' => 'heroicon-o-shield-check',
        'System' => 'heroicon-o-cog-6-tooth',
    ];

    /**
     * Warna ubin per kelompok.
     *
     * Nilainya bukan warna, melainkan nama yang diterjemahkan menjadi kelas CSS di
     * gais.css. Warnanya sendiri hidup di berkas gaya, supaya menyetelnya tidak perlu
     * menyentuh berkas PHP.
     *
     * Kelompok yang belum punya warna sendiri jatuh ke petrol, warna dasar aplikasi.
     * Itu membuat kelompok baru tetap tergambar wajar tanpa berkas ini disentuh, hanya
     * saja warnanya belum khas.
     *
     * @var array<string, string>
     */
    public const WARNA_KELOMPOK = [
        // Dasbor dan menu lain yang tidak berkelompok.
        '' => 'petrol',
        'Assets' => 'slate',
        'Maintenance' => 'terracotta',
        'Vehicles' => 'teal',
        'Facility Services' => 'olive',
        'Correspondence' => 'plum',
        'Office Supplies' => 'ochre',
        'Budget & Expenses' => 'forest',
        'Master Data' => 'stone',
        'Access Control' => 'brick',
        'System' => 'graphite',
    ];

    public const WARNA_BAWAAN = 'petrol';

    /**
     * Hasilnya diingat selama satu permintaan.
     *
     * Menyusun navigasi berarti memanggil pemeriksaan izin pada tiap resource. Tampilan
     * peluncur ini memanggil daftarnya lebih dari sekali dalam satu halaman, dan tanpa
     * ingatan ini seluruh pemeriksaan itu berjalan berulang kali untuk hasil yang sama.
     *
     * @var array<int, array<string, mixed>>|null
     */
    protected static ?array $ingatan = null;

    /**
     * Ubin untuk layar pertama peluncur.
     *
     * Ubin berjenis `menu` menuju satu halaman. Ubin berjenis `kelompok` membuka isinya di
     * jendela yang sama, dan isinya sudah ikut dibawa di kunci `isi`.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function ubin(): array
    {
        if (static::$ingatan !== null) {
            return static::$ingatan;
        }

        $ubin = [];

        foreach (Filament::getNavigation() as $kelompok) {
            if (! $kelompok instanceof NavigationGroup) {
                continue;
            }

            $label = trim((string) $kelompok->getLabel());
            $isi = static::menuDalam($kelompok, $label);

            // Kelompok yang seluruh isinya tidak boleh dibuka orang ini tidak ditampilkan
            // sama sekali, bukan ditampilkan kosong.
            if ($isi === []) {
                continue;
            }

            if (! array_key_exists($label, self::KELOMPOK_DIRINGKAS)) {
                $ubin = array_merge($ubin, $isi);

                continue;
            }

            $ubin[] = [
                'jenis' => 'kelompok',
                'kunci' => Str::slug($label),
                'label' => $label,
                'kelompok' => $label,
                'icon' => self::KELOMPOK_DIRINGKAS[$label],
                'warna' => static::warna($label),
                'aktif' => static::adaYangAktif($isi),
                'jumlah' => count($isi),
                'isi' => $isi,
            ];
        }

        return static::$ingatan = $ubin;
    }

    /**
     * Kelompok yang diringkas saja, untuk menggambar layar keduanya.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function kelompokDiringkas(): array
    {
        return array_values(array_filter(
            static::ubin(),
            fn (array $ubin): bool => $ubin['jenis'] === 'kelompok',
        ));
    }

    /**
     * Seluruh menu dalam satu daftar datar, termasuk isi kelompok yang diringkas.
     *
     * Dipakai kotak pencarian. Yang mengetik "role" ingin sampai ke menu Roles, dan tidak
     * peduli bahwa menu itu berada di dalam kelompok yang layar pertamanya diringkas.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function semuaMenu(): array
    {
        $menu = [];

        foreach (static::ubin() as $ubin) {
            if ($ubin['jenis'] === 'menu') {
                $menu[] = $ubin;

                continue;
            }

            $menu = array_merge($menu, $ubin['isi']);
        }

        return $menu;
    }

    /**
     * Teks yang dicocokkan kotak pencarian: nama menu ditambah nama kelompoknya.
     *
     * Nama kelompok ikut dicocokkan supaya mengetik "master" menampilkan seluruh isi
     * Master Data, bukan tidak menampilkan apa apa karena tidak ada menu yang bernama
     * "master".
     */
    public static function teksCari(array $menu): string
    {
        return Str::lower(trim($menu['label'].' '.($menu['kelompok'] ?? '')));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function menuDalam(NavigationGroup $kelompok, string $label): array
    {
        $menu = [];

        foreach ($kelompok->getItems() as $item) {
            if (! $item instanceof NavigationItem) {
                continue;
            }

            $url = $item->getUrl();

            // Ubin tanpa tujuan tidak dibuat. Tombol yang tidak membawa ke mana pun
            // dilarang di proyek ini (R-26), dan diam diam menggambarnya lebih buruk
            // daripada tidak menggambarnya sama sekali.
            if (blank($url)) {
                continue;
            }

            $menu[] = [
                'jenis' => 'menu',
                'label' => (string) $item->getLabel(),
                'kelompok' => $label,
                'url' => $url,
                'icon' => $item->getIcon(),
                'warna' => static::warna($label),
                'aktif' => $item->isActive(),
            ];
        }

        return $menu;
    }

    protected static function warna(string $kelompok): string
    {
        return self::WARNA_KELOMPOK[$kelompok] ?? self::WARNA_BAWAAN;
    }

    /**
     * @param  array<int, array<string, mixed>>  $menu
     */
    protected static function adaYangAktif(array $menu): bool
    {
        foreach ($menu as $satu) {
            if ($satu['aktif'] === true) {
                return true;
            }
        }

        return false;
    }
}
