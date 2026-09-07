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
    <style>
        .gais-tab-baris {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            border-bottom: 1px solid var(--gray-200, #D2DADE);
            padding-bottom: 0;
            margin-bottom: 4px;
        }

        .gais-tab {
            appearance: none;
            border: 0;
            background: transparent;
            font: inherit;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 14px;
            /* Tap target 44 piksel supaya tetap enak ditekan di telepon. */
            min-height: 44px;
            color: var(--gray-600, #596A70);
            border-bottom: 3px solid transparent;
            margin-bottom: -1px;
        }

        .gais-tab:hover { color: var(--primary-700, #17505E); }

        .gais-tab[aria-selected="true"] {
            color: var(--primary-700, #17505E);
            border-bottom-color: var(--primary-700, #17505E);
        }

        .gais-tab:focus-visible {
            outline: 2px solid #A2542F;
            outline-offset: 2px;
            border-radius: 4px;
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
