@php
    /**
     * Peluncur menu: satu tombol di topbar yang membuka seluruh menu sebagai ubin.
     *
     * Isinya dibaca dari navigasi panel lewat App\Support\PeluncurMenu, jadi menu yang
     * tidak boleh dibuka seseorang tidak pernah tergambar di sini, dan modul baru ikut
     * muncul tanpa berkas ini disentuh.
     */
    use App\Support\PeluncurMenu;

    $ubin = PeluncurMenu::ubin();
    $daftarRingkas = PeluncurMenu::kelompokDiringkas();
    $semuaMenu = PeluncurMenu::semuaMenu();

    // Teks yang dicocokkan kotak pencarian, disiapkan di sini supaya penyaringnya di
    // peramban hanya membandingkan teks, bukan menyusun ulang apa pun.
    $teksCari = array_map(fn (array $menu): string => PeluncurMenu::teksCari($menu), $semuaMenu);
@endphp

@if (filled($ubin))
    <div
        x-data="{
            buka: false,
            /*
             * Jebakan fokus dinyalakan satu tick setelah jendelanya tampil. Menyalakannya
             * pada tick yang sama membuat Alpine mencoba memindahkan fokus ke elemen yang
             * masih tersembunyi. Pemisahan ini mengikuti cara modal bawaan Filament.
             */
            jebakAktif: false,
            kelompok: null,
            cari: '',
            teksCari: @js($teksCari),
            get kunci() {
                return this.cari.trim().toLowerCase()
            },
            get mencari() {
                return this.kunci !== ''
            },
            get jumlahHasil() {
                return this.teksCari.filter((teks) => teks.includes(this.kunci)).length
            },
            cocok(teks) {
                return teks.includes(this.kunci)
            },
            bukaPeluncur() {
                this.kelompok = null
                this.cari = ''
                this.buka = true
                this.$nextTick(() => {
                    this.jebakAktif = true
                    this.$refs.kotakCari?.focus()
                })
            },
            tutup() {
                this.jebakAktif = false
                this.buka = false
                this.kelompok = null
                this.cari = ''
            },
            /*
             * Escape mundur satu langkah pada tiap tekanan, bukan langsung menutup.
             * Urutannya mengikuti urutan orang menyempitkan pandangannya: hapus pencarian
             * dulu, lalu keluar dari isi kelompok, baru menutup jendelanya.
             */
            mundur() {
                if (this.mencari) {
                    this.cari = ''

                    return
                }

                if (this.kelompok !== null) {
                    this.kelompok = null

                    return
                }

                this.tutup()
            },
        }"
        x-on:keydown.escape.window="buka && mundur()"
        class="gais-peluncur"
    >
        <button
            type="button"
            class="gais-peluncur-tombol"
            x-on:click="bukaPeluncur()"
            aria-haspopup="dialog"
            title="All Menus"
        >
            <x-filament::icon icon="heroicon-o-squares-2x2" class="gais-peluncur-tombol-ikon" />
            <span class="gais-peluncur-sr">All Menus</span>
        </button>

        {{--
            Dipindahkan ke body. Topbar panel ini melekat di atas layar dan punya konteks
            tumpukannya sendiri, jadi jendela yang tetap tinggal di dalamnya akan terpotong
            di tepi topbar alih alih menutupi halaman.
        --}}
        <template x-teleport="body">
            <div
                x-show="buka"
                x-cloak
                x-trap.noscroll="jebakAktif"
                x-transition.opacity.duration.150ms
                class="gais-peluncur-latar"
                role="dialog"
                aria-modal="true"
                aria-label="All Menus"
            >
                <div class="gais-peluncur-tirai" x-on:click="tutup()" aria-hidden="true"></div>

                <div class="gais-peluncur-jendela">
                    <div class="gais-peluncur-kepala">
                        <button
                            type="button"
                            class="gais-peluncur-mundur"
                            x-show="kelompok !== null && ! mencari"
                            x-cloak
                            x-on:click="kelompok = null"
                        >
                            <x-filament::icon icon="heroicon-o-arrow-left" class="gais-peluncur-mundur-ikon" />
                            <span>All Menus</span>
                        </button>

                        <h2 class="gais-peluncur-judul" x-show="kelompok === null || mencari">All Menus</h2>

                        @foreach ($daftarRingkas as $ringkas)
                            <h2
                                class="gais-peluncur-judul"
                                x-show="kelompok === @js($ringkas['kunci']) && ! mencari"
                                x-cloak
                            >
                                {{ $ringkas['label'] }}
                            </h2>
                        @endforeach

                        <div class="gais-peluncur-cari">
                            <x-filament::icon icon="heroicon-o-magnifying-glass" class="gais-peluncur-cari-ikon" />
                            <input
                                type="search"
                                class="gais-peluncur-cari-kotak"
                                x-ref="kotakCari"
                                x-model="cari"
                                placeholder="Cari menu"
                                aria-label="Cari menu"
                                autocomplete="off"
                            >
                            <button
                                type="button"
                                class="gais-peluncur-cari-hapus"
                                x-show="mencari"
                                x-cloak
                                x-on:click="cari = ''; $refs.kotakCari.focus()"
                                title="Clear Search"
                            >
                                <x-filament::icon icon="heroicon-o-x-mark" class="gais-peluncur-cari-hapus-ikon" />
                                <span class="gais-peluncur-sr">Clear Search</span>
                            </button>
                        </div>

                        <button type="button" class="gais-peluncur-tutup" x-on:click="tutup()" title="Close">
                            <x-filament::icon icon="heroicon-o-x-mark" class="gais-peluncur-tutup-ikon" />
                            <span class="gais-peluncur-sr">Close</span>
                        </button>
                    </div>

                    <div class="gais-peluncur-isi">
                        {{-- Layar pertama: seluruh menu harian, lalu tiga kelompok penyiapan --}}
                        <div x-show="! mencari && kelompok === null">
                            <div class="gais-peluncur-kisi">
                                @foreach ($ubin as $satu)
                                    @if ($satu['jenis'] === 'menu')
                                        <a
                                            href="{{ $satu['url'] }}"
                                            class="gais-ubin gais-ubin-{{ $satu['warna'] }} @if ($satu['aktif']) gais-ubin-aktif @endif"
                                            @if ($satu['aktif']) aria-current="page" @endif
                                        >
                                            <span class="gais-ubin-ikon">
                                                @if (filled($satu['icon']))
                                                    <x-filament::icon :icon="$satu['icon']" class="gais-ubin-ikon-svg" />
                                                @endif
                                            </span>
                                            <span class="gais-ubin-label">{{ $satu['label'] }}</span>
                                        </a>
                                    @else
                                        <button
                                            type="button"
                                            class="gais-ubin gais-ubin-{{ $satu['warna'] }} @if ($satu['aktif']) gais-ubin-aktif @endif"
                                            x-on:click="kelompok = @js($satu['kunci'])"
                                        >
                                            <span class="gais-ubin-ikon">
                                                <x-filament::icon :icon="$satu['icon']" class="gais-ubin-ikon-svg" />
                                            </span>
                                            <span class="gais-ubin-label">{{ $satu['label'] }}</span>
                                            <span class="gais-ubin-catatan">{{ $satu['jumlah'] }} menu</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Layar kedua, satu untuk tiap kelompok yang diringkas --}}
                        @foreach ($daftarRingkas as $ringkas)
                            <div x-show="! mencari && kelompok === @js($ringkas['kunci'])" x-cloak>
                                <div class="gais-peluncur-kisi">
                                    @foreach ($ringkas['isi'] as $satu)
                                        <a
                                            href="{{ $satu['url'] }}"
                                            class="gais-ubin gais-ubin-{{ $satu['warna'] }} @if ($satu['aktif']) gais-ubin-aktif @endif"
                                            @if ($satu['aktif']) aria-current="page" @endif
                                        >
                                            <span class="gais-ubin-ikon">
                                                @if (filled($satu['icon']))
                                                    <x-filament::icon :icon="$satu['icon']" class="gais-ubin-ikon-svg" />
                                                @endif
                                            </span>
                                            <span class="gais-ubin-label">{{ $satu['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        {{--
                            Hasil pencarian. Kelompok yang diringkas ikut dibuka di sini,
                            karena yang mengetik nama menu ingin sampai ke menunya, bukan
                            diberi tahu bahwa menu itu berada di dalam kelompok tertentu.
                        --}}
                        <div x-show="mencari" x-cloak>
                            <div class="gais-peluncur-kisi" x-show="jumlahHasil > 0">
                                @foreach ($semuaMenu as $indeks => $satu)
                                    <a
                                        href="{{ $satu['url'] }}"
                                        class="gais-ubin gais-ubin-{{ $satu['warna'] }} @if ($satu['aktif']) gais-ubin-aktif @endif"
                                        x-show="cocok(@js($teksCari[$indeks]))"
                                        @if ($satu['aktif']) aria-current="page" @endif
                                    >
                                        <span class="gais-ubin-ikon">
                                            @if (filled($satu['icon']))
                                                <x-filament::icon :icon="$satu['icon']" class="gais-ubin-ikon-svg" />
                                            @endif
                                        </span>
                                        <span class="gais-ubin-label">{{ $satu['label'] }}</span>
                                        @if (filled($satu['kelompok']))
                                            <span class="gais-ubin-catatan">{{ $satu['kelompok'] }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>

                            <p class="gais-peluncur-kosong" x-show="jumlahHasil === 0" x-cloak>
                                Tidak ada menu yang namanya mengandung
                                <strong x-text="cari.trim()"></strong>.
                                Coba potongan kata yang lebih pendek, atau bersihkan kotak pencariannya
                                untuk melihat seluruh menu lagi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endif
