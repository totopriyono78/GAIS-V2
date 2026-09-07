@php
    use App\Services\Barcode\Code128;

    // Ruang yang tersisa untuk barcode setelah garis motif dan padding kiri kanan.
    $lebarBarcode = max($lebar - 8, 20);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label aset {{ $merek }}</title>
    <style>
        /*
         * Halaman ini hanya untuk dicetak, jadi tidak memuat font dari internet.
         * Alasannya: pencetakan label sering dilakukan di komputer gudang yang
         * koneksinya lambat, dan menunggu font membuat halaman tampil kosong.
         */
        :root {
            --tinta: #000000;
            --kertas: #ffffff;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            background: #faf7f2;
            color: #1c1a17;
            font-family: "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
        }

        .alat {
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid #d8d2c8;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .alat h1 {
            font-size: 16px;
            margin: 0;
            border-inline-start: 3px solid #a2542f;
            padding-inline-start: 10px;
        }

        .alat p {
            margin: 0;
            font-size: 13px;
            color: #3a4a50;
        }

        .alat .kanan { margin-inline-start: auto; display: flex; gap: 10px; }

        .tombol {
            font: inherit;
            font-size: 14px;
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 6px;
            border: 1px solid #17505e;
            background: #17505e;
            color: #ffffff;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .tombol.sekunder {
            background: #ffffff;
            color: #17505e;
        }

        .tombol:focus-visible {
            outline: 2px solid #a2542f;
            outline-offset: 2px;
        }

        .lembar {
            display: grid;
            grid-template-columns: repeat({{ $kolom }}, {{ $lebar }}mm);
            justify-content: start;
            gap: 0;
            padding: {{ $marginAtas }}mm {{ $marginKiri }}mm;
            background: #ffffff;
            margin: 16px auto;
            width: fit-content;
            box-shadow: 0 1px 3px rgba(28, 26, 23, 0.15);
        }

        .label {
            width: {{ $lebar }}mm;
            height: {{ $tinggi }}mm;
            padding: 2.5mm 3mm 2mm 0;
            display: flex;
            gap: 2.5mm;
            overflow: hidden;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /*
         * Motif garis aksen dari DESIGN.md tetap dipakai di label, tapi dicetak
         * hitam. Terracotta di printer hitam putih jadi abu abu pucat dan garisnya
         * hilang, sedangkan garis ini yang membuat label GAIS langsung dikenali.
         */
        .label .garis {
            width: 1mm;
            background: var(--tinta);
            flex: none;
        }

        .label .isi {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .label .nama {
            font-size: 8pt;
            font-weight: 700;
            line-height: 1.15;
            max-height: 2.4em;
            overflow: hidden;
        }

        .label .barcode {
            line-height: 0;
            margin-top: 0.6mm;
        }

        .label .kode {
            font-family: ui-monospace, "Cascadia Mono", Consolas, "Courier New", monospace;
            font-size: 8.5pt;
            letter-spacing: 0.02em;
            font-variant-numeric: tabular-nums;
            margin-top: 0.4mm;
        }

        .label .kaki {
            display: flex;
            justify-content: space-between;
            gap: 2mm;
            font-size: 6pt;
            color: #333333;
        }

        .label .kaki span {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        @media print {
            @page {
                size: A4;
                margin: {{ $marginAtas }}mm {{ $marginKiri }}mm;
            }

            html, body { background: #ffffff; }

            .alat { display: none; }

            .lembar {
                margin: 0;
                padding: 0;
                width: auto;
                box-shadow: none;
            }

            .label .kaki { color: #000000; }
        }
    </style>
</head>
<body>
    <div class="alat">
        <h1>Label aset</h1>
        <p>{{ $assets->count() }} label, ukuran {{ rtrim(rtrim(number_format($lebar, 1, ',', '.'), '0'), ',') }} kali {{ rtrim(rtrim(number_format($tinggi, 1, ',', '.'), '0'), ',') }} mm, {{ $kolom }} kolom per halaman. Ubah ukurannya di Pengaturan kalau tidak pas dengan stiker Anda.</p>
        <div class="kanan">
            <a class="tombol sekunder" href="{{ url('/admin/assets') }}">Kembali ke daftar aset</a>
            <button type="button" class="tombol" onclick="window.print()">Cetak sekarang</button>
        </div>
    </div>

    <div class="lembar">
        @foreach ($assets as $asset)
            <div class="label">
                <div class="garis" aria-hidden="true"></div>
                <div class="isi">
                    <div class="nama">{{ $asset->name }}</div>
                    <div class="barcode">{!! Code128::svgFitted($asset->code, $lebarBarcode, 11) !!}</div>
                    <div class="kode">{{ $asset->code }}</div>
                    <div class="kaki">
                        <span>{{ $asset->location?->code ?? 'Lokasi belum diisi' }}</span>
                        <span>{{ $merek }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
