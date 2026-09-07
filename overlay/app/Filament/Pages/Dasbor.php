<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AsetJatuhTempo;
use App\Filament\Widgets\AsetJatuhTempoTabel;
use App\Filament\Widgets\AsetKategoriChart;
use App\Filament\Widgets\AsetRingkasan;
use App\Filament\Widgets\AsetStatusChart;
use App\Filament\Widgets\KendaraanJatuhTempoTabel;
use App\Filament\Widgets\KendaraanRingkasan;
use App\Filament\Widgets\PemeliharaanBiayaChart;
use App\Filament\Widgets\PemeliharaanJatuhTempoTabel;
use App\Filament\Widgets\PemeliharaanRingkasan;
use App\Filament\Widgets\PermintaanMenunggu;
use App\Filament\Widgets\PersediaanPemakaianChart;
use App\Filament\Widgets\PersediaanPerluDipesan;
use App\Filament\Widgets\PersediaanRingkasan;
use App\Filament\Widgets\RingkasanTahapSatu;
use Filament\Pages\Dashboard;
use Livewire\Attributes\Url;

/**
 * Dasbor dengan tab.
 *
 * Sebelumnya seluruh angka menumpuk di satu halaman, dan begitu modul bertambah,
 * halaman itu berubah menjadi daftar panjang yang tidak ada urutan bacanya. Tab
 * mengelompokkannya menurut pekerjaan orangnya: yang mengurus aset membuka tab
 * Aset, yang menjaga stok membuka tab Persediaan.
 *
 * Tab yang sedang terbuka disimpan di alamat halaman, jadi satu tab bisa dikirim
 * lewat tautan dan tetap terbuka setelah halaman dimuat ulang.
 */
class Dasbor extends Dashboard
{
    #[Url(as: 'tab', keep: true)]
    public string $tab = 'ringkasan';

    /**
     * Berkas tampilan ditentukan lewat metode, bukan lewat properti, supaya tidak
     * bergantung pada properti $view di kelas induk yang bisa berubah antar versi.
     */
    public function getView(): string
    {
        return 'filament.pages.dasbor';
    }

    public function getTitle(): string
    {
        return 'Dasbor';
    }

    public function getHeading(): string
    {
        return 'Dasbor';
    }

    public function getSubheading(): ?string
    {
        return 'Seluruh angka di halaman ini dihitung dari basis data saat halaman dibuka.';
    }

    public function pilihTab(string $tab): void
    {
        if (array_key_exists($tab, $this->daftarTab())) {
            $this->tab = $tab;
        }
    }

    /**
     * Tab hanya muncul kalau isinya ada yang boleh dilihat pengguna ini. Tanpa itu,
     * pengguna yang tidak punya izin persediaan akan menemukan tab kosong, dan tab
     * kosong adalah bentuk lain dari menu yang menuju halaman yang tidak ada.
     *
     * @return array<string, array{judul: string, widget: array<int, class-string>}>
     */
    public function daftarTab(): array
    {
        $semua = [
            'ringkasan' => [
                'judul' => 'Ringkasan',
                'widget' => [RingkasanTahapSatu::class],
            ],
            'aset' => [
                'judul' => 'Aset',
                'widget' => [
                    AsetRingkasan::class,
                    AsetStatusChart::class,
                    AsetKategoriChart::class,
                    AsetJatuhTempo::class,
                    AsetJatuhTempoTabel::class,
                ],
            ],
            'pemeliharaan' => [
                'judul' => 'Pemeliharaan',
                'widget' => [
                    PemeliharaanRingkasan::class,
                    PermintaanMenunggu::class,
                    PemeliharaanJatuhTempoTabel::class,
                    PemeliharaanBiayaChart::class,
                ],
            ],
            'kendaraan' => [
                'judul' => 'Kendaraan',
                'widget' => [
                    KendaraanRingkasan::class,
                    KendaraanJatuhTempoTabel::class,
                ],
            ],
            'persediaan' => [
                'judul' => 'Persediaan',
                'widget' => [
                    PersediaanRingkasan::class,
                    PersediaanPemakaianChart::class,
                    PersediaanPerluDipesan::class,
                ],
            ],
        ];

        $hasil = [];

        foreach ($semua as $kunci => $isi) {
            $terlihat = array_values(array_filter(
                $isi['widget'],
                fn (string $widget): bool => $widget::canView(),
            ));

            if ($terlihat === []) {
                continue;
            }

            $hasil[$kunci] = ['judul' => $isi['judul'], 'widget' => $terlihat];
        }

        return $hasil;
    }

    /** @return array<int, class-string> */
    public function widgetTabIni(): array
    {
        $tabs = $this->daftarTab();

        if (! array_key_exists($this->tab, $tabs)) {
            $this->tab = (string) array_key_first($tabs);
        }

        return $tabs[$this->tab]['widget'] ?? [];
    }
}
