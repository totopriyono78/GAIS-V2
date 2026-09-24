@php
    $tabs = $this->daftarTab();
    $aktif = $this->tab;
@endphp

<x-filament-panels::page>
    {{--
        Tab digambar sebagai tombol biasa, bukan komponen tab bawaan, karena halaman
        dasbor bukan formulir dan tidak butuh keadaan tab yang ikut divalidasi. Yang
        dibutuhkan hanya satu hal: pindah kelompok tanpa memuat ulang halaman.
    --}}
    {{--
        Tab bergaya nav pills Vuexy: tab aktif berupa pil ungu bertulisan putih dengan
        bayangan sewarna, tab lain teks biasa. Pil padat lebih mudah ditemukan mata
        daripada garis bawah tipis saat dasbor dibuka sekilas di sela pekerjaan.
    --}}
    <style>
        .gais-tab-baris {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 4px;
        }

        .gais-tab {
            appearance: none;
            border: 0;
            background: transparent;
            font: inherit;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 18px;
            /* Tap target 44 piksel supaya tetap enak ditekan di telepon. */
            min-height: 44px;
            border-radius: 0.357rem;
            color: var(--gray-600);
            transition: background-color 0.15s ease, box-shadow 0.2s ease;
        }

        .dark .gais-tab { color: var(--gray-300); }

        .gais-tab:hover {
            color: var(--primary-600);
            background-color: color-mix(in srgb, var(--primary-400) 12%, transparent);
        }

        .dark .gais-tab:hover { color: var(--primary-300); }

        /* primary-600 dengan tulisan putih: 5,68:1. */
        .gais-tab[aria-selected="true"],
        .dark .gais-tab[aria-selected="true"] {
            color: #ffffff;
            background-color: var(--primary-600);
            box-shadow: 0 4px 18px -4px color-mix(in srgb, var(--primary-400) 65%, transparent);
        }

        .gais-tab:focus-visible {
            outline: 2px solid var(--primary-400);
            outline-offset: 2px;
        }
    </style>

    @if (count($tabs) > 1)
        <div class="gais-tab-baris" role="tablist" aria-label="Kelompok data dasbor">
            @foreach ($tabs as $kunci => $isi)
                <button
                    type="button"
                    role="tab"
                    class="gais-tab"
                    aria-selected="{{ $aktif === $kunci ? 'true' : 'false' }}"
                    wire:click="pilihTab('{{ $kunci }}')"
                    wire:key="tab-{{ $kunci }}"
                >{{ $isi['judul'] }}</button>
            @endforeach
        </div>
    @endif

    @php
        $widgets = $this->widgetTabIni();
    @endphp

    @if ($widgets === [])
        <x-filament::section>
            <x-slot name="heading">Belum ada yang bisa ditampilkan</x-slot>
            Dasbor mengikuti izin akun Anda. Begitu Anda diberi izin melihat aset atau
            persediaan, ringkasannya muncul sendiri di sini.
        </x-filament::section>
    @else
        {{--
            Dipakai komponen widget bawaan Filament, bukan grid buatan sendiri, supaya
            pengaturan columnSpan pada tiap widget tetap dihormati dan lebarnya sama
            dengan dasbor bawaan.
        --}}
        <x-filament-widgets::widgets
            :columns="2"
            :widgets="$widgets"
            wire:key="widget-{{ $aktif }}"
        />
    @endif
</x-filament-panels::page>
