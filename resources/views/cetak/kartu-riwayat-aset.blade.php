<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu riwayat {{ $asset->code }}</title>
    <style>
        /*
         * Halaman ini tidak memuat font dari internet, sama alasannya dengan halaman
         * cetak label: sering dibuka di komputer gudang yang koneksinya lambat, dan
         * menunggu font membuat halaman tampil kosong.
         *
         * Warnanya mengikuti DESIGN.md bagian 4. Ditulis apa adanya di sini, bukan
         * lewat variabel panel, karena halaman ini berada di luar panel.
         */
        :root {
            --petrol: #17505E;
            --slate: #3A4A50;
            --terakota: #A2542F;
            --tinta: #1C1A17;
            --kertas: #FAF7F2;
            --hijau: #1F6B3F;
            --merah: #A32020;
            --abu: #76878E;
            --garis: #D8D2C8;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--kertas);
            color: var(--tinta);
            font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Angka berbaris lurus, motif identitas kedua dari DESIGN.md. */
        .nomor, .tanggal, .rincian { font-variant-numeric: tabular-nums; }

        .alat {
            padding: 14px 20px;
            background: #ffffff;
            border-bottom: 1px solid var(--garis);
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .alat .kanan { margin-inline-start: auto; display: flex; gap: 10px; }

        .tombol {
            font: inherit;
            padding: 7px 14px;
            border: 1px solid var(--petrol);
            background: var(--petrol);
            color: #ffffff;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }

        .tombol.sekunder { background: #ffffff; color: var(--petrol); }

        .halaman { max-width: 880px; margin: 0 auto; padding: 24px 20px 64px; }

        /* Motif identitas pertama: garis aksen 3px di sisi kiri judul. */
        h1, h2 {
            border-inline-start: 3px solid var(--terakota);
            padding-inline-start: 10px;
            margin: 0 0 4px;
            font-family: "Source Serif 4", Georgia, "Times New Roman", serif;
            letter-spacing: -0.01em;
        }

        h1 { font-size: 22px; }
        h2 { font-size: 16px; margin-top: 28px; }

        .subjudul { color: var(--slate); margin: 0 0 20px; }

        .ringkas {
            background: #ffffff;
            border: 1px solid var(--garis);
            border-radius: 8px;
            padding: 16px 18px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px 20px;
        }

        .ringkas dt { font-size: 12px; color: var(--slate); margin: 0 0 2px; }
        .ringkas dd { margin: 0; font-weight: 600; }

        .garis-waktu { list-style: none; margin: 12px 0 0; padding: 0; }

        .peristiwa {
            display: grid;
            grid-template-columns: 118px 1fr;
            gap: 0 18px;
            padding: 16px 0;
            border-top: 1px solid var(--garis);
        }

        .peristiwa:first-child { border-top: 0; }

        .tanggal { color: var(--slate); font-size: 13px; }

        .jenis {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px solid currentColor;
            margin-bottom: 6px;
        }

        .jenis[data-warna="petrol"] { color: var(--petrol); }
        .jenis[data-warna="hijau"] { color: var(--hijau); }
        .jenis[data-warna="terakota"] { color: var(--terakota); }
        .jenis[data-warna="merah"] { color: var(--merah); }
        .jenis[data-warna="abu"] { color: var(--abu); }

        .judul { font-weight: 600; margin: 0 0 4px; }
        .nomor { color: var(--slate); font-size: 13px; font-family: ui-monospace, "Cascadia Mono", Consolas, monospace; }

        .rincian { list-style: none; margin: 8px 0 0; padding: 0; }
        .rincian li { padding: 3px 0; }
        .rincian .label { color: var(--slate); }
        .rincian .lama { color: var(--slate); }
        .rincian .baru { font-weight: 600; }

        .keterangan { color: var(--slate); margin: 8px 0 0; }

        .kosong {
            background: #ffffff;
            border: 1px dashed var(--garis);
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            color: var(--slate);
        }

        .catatan-kaki {
            margin-top: 32px;
            padding-top: 14px;
            border-top: 1px solid var(--garis);
            color: var(--slate);
            font-size: 12px;
        }

        @media print {
            .alat { display: none; }
            html, body { background: #ffffff; }
            .halaman { max-width: none; padding: 0; }
            .ringkas, .kosong { border-color: #999999; }
            .peristiwa { break-inside: avoid; }
        }

        @media (max-width: 640px) {
            .peristiwa { grid-template-columns: 1fr; gap: 6px; }
            .halaman { padding: 16px 14px 48px; }
        }
    </style>
</head>
<body>
    <div class="alat">
        <div>
            <strong>{{ $merek }}</strong>
            <span class="nomor">Kartu riwayat aset</span>
        </div>
        <div class="kanan">
            <a class="tombol sekunder" href="{{ url('/admin/assets/'.$asset->id.'/edit') }}">Buka data aset</a>
            <button class="tombol" type="button" onclick="window.print()">Cetak</button>
        </div>
    </div>

    <div class="halaman">
        <h1>{{ $asset->name }}</h1>
        <p class="subjudul">
            <span class="nomor">{{ $asset->code }}</span>.
            Sekarang di {{ $asset->location?->code ?: 'lokasi yang belum diisi' }},
            dipegang {{ $asset->custodian?->full_name ?: 'belum ada penanggung jawab' }},
            status {{ $asset->statusLabel() }}.
        </p>

        <dl class="ringkas">
            <div>
                <dt>Kategori</dt>
                <dd>{{ $asset->category?->name ?: 'Belum diisi' }}</dd>
            </div>
            <div>
                <dt>Departemen</dt>
                <dd>{{ $asset->department?->name ?: 'Belum diisi' }}</dd>
            </div>
            <div>
                <dt>Kondisi</dt>
                <dd>{{ $asset->conditionLabel() }}</dd>
            </div>
            <div>
                <dt>Nilai perolehan</dt>
                <dd>{{ $asset->acquisition_cost > 0 ? 'Rp '.number_format((float) $asset->acquisition_cost, 0, ',', '.') : 'Tidak tercatat' }}</dd>
            </div>
        </dl>

        <h2>Riwayat</h2>

        @if (count($peristiwa) === 0)
            <p class="kosong">
                Belum ada peristiwa yang tercatat untuk aset ini. Riwayat terisi sendiri
                begitu aset diserahterimakan, diperiksa lewat stock opname, atau dilepas.
            </p>
        @else
            <ol class="garis-waktu">
                @foreach ($peristiwa as $item)
                    <li class="peristiwa">
                        <div class="tanggal">{{ $item['tanggal']->translatedFormat('d M Y') }}</div>
                        <div>
                            <span class="jenis" data-warna="{{ $item['warna'] }}">{{ $item['jenis'] }}</span>
                            <p class="judul">{{ $item['judul'] }}</p>
                            <p class="nomor">{{ $item['nomor'] }}</p>

                            @if (! empty($item['rincian']))
                                <ul class="rincian">
                                    @foreach ($item['rincian'] as $baris)
                                        <li>
                                            <span class="label">{{ $baris['label'] }}</span>
                                            @if (! empty($baris['dari']))
                                                <span class="lama">{{ $baris['dari'] }}</span> menjadi
                                            @endif
                                            <span class="baru">{{ $baris['ke'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if (! empty($item['keterangan']))
                                <p class="keterangan">{{ $item['keterangan'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif

        <p class="catatan-kaki">
            Kartu ini disusun dari dokumen serah terima, hasil stock opname, dan dokumen pelepasan
            aset ini. Perubahan yang dilakukan langsung lewat formulir aset tidak muncul di sini,
            dan bisa dilihat di layar Jejak audit.
            @if ($perusahaan !== '')
                Dicetak untuk {{ $perusahaan }}.
            @endif
        </p>
    </div>
</body>
</html>
