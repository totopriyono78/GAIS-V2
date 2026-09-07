@php
    use Illuminate\Support\Carbon;

    $tanggal = $transfer->transfer_date instanceof Carbon
        ? $transfer->transfer_date
        : Carbon::parse($transfer->transfer_date);

    $asset = $transfer->asset;
    $perubahan = $transfer->changes();

    // Dua nama yang menandatangani. BAST menandatangani orangnya, BAM menandatangani
    // pihak yang bertanggung jawab atas pencatatan mutasinya.
    $pihakKiri = $jenis === 'bast'
        ? ['peran' => 'Yang menyerahkan', 'nama' => $transfer->handedOverBy?->full_name, 'jabatan' => $transfer->handedOverBy?->position]
        : ['peran' => 'Dibuat oleh', 'nama' => $transfer->createdByUser?->name, 'jabatan' => 'Petugas General Affair'];

    $pihakKanan = $jenis === 'bast'
        ? ['peran' => 'Yang menerima', 'nama' => $transfer->receivedBy?->full_name, 'jabatan' => $transfer->receivedBy?->position]
        : ['peran' => 'Mengetahui', 'nama' => null, 'jabatan' => 'Manajer General Affair'];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $judul }} {{ $transfer->code }}</title>
    <style>
        /*
         * Dompdf tidak mengerti flexbox maupun grid, jadi seluruh susunan halaman ini
         * memakai tabel. Fontnya DejaVu Sans, satu satunya font bawaan Dompdf yang
         * lengkap huruf Latinnya, jadi tidak ada berkas font yang perlu ikut dikirim.
         */
        @page { margin: 22mm 18mm; }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #1C1A17;
        }

        .kop { border-bottom: 2px solid #17505E; padding-bottom: 8px; margin-bottom: 18px; }
        .kop .nama { font-size: 13pt; font-weight: bold; color: #17505E; }
        .kop .alamat { font-size: 9pt; color: #3A4A50; }

        h1 {
            font-size: 12.5pt;
            text-align: center;
            margin: 0 0 2px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .nomor { text-align: center; font-size: 10pt; color: #3A4A50; margin: 0 0 18px; }

        p { margin: 0 0 10px; text-align: justify; }

        table.data { width: 100%; border-collapse: collapse; margin: 10px 0 16px; }
        table.data th, table.data td { border: 1px solid #BFB9AE; padding: 5px 8px; vertical-align: top; }
        table.data th {
            background: #F0EDE7;
            text-align: left;
            font-size: 9.5pt;
            width: 34%;
            font-weight: normal;
            color: #3A4A50;
        }

        table.rincian th { width: auto; text-align: left; background: #F0EDE7; font-weight: bold; color: #1C1A17; }
        table.rincian td { font-size: 10pt; }

        /*
         * Kolom tanda tangan sengaja tidak memakai border-collapse. Kalau digabung,
         * garis tanda tangan kedua kolom bersambung menjadi satu garis panjang di
         * bawah dua nama sekaligus, dan itu terbaca seperti satu tanda tangan untuk
         * dua orang. Dengan border-spacing, tiap kolom membawa garisnya sendiri dan
         * ada jarak kosong di tengah yang memisahkannya.
         */
        .ttd { width: 100%; margin-top: 26px; border-collapse: separate; border-spacing: 14mm 0; }
        .ttd td { width: 50%; vertical-align: top; text-align: center; padding: 0; }
        .ttd .peran { font-size: 10pt; color: #3A4A50; }
        .ttd .ruang { height: 62px; }
        .ttd .nama { border-top: 1px solid #1C1A17; padding-top: 4px; font-weight: bold; }
        .ttd .jabatan { font-size: 9pt; color: #3A4A50; }

        .kaki { margin-top: 22px; font-size: 8.5pt; color: #3A4A50; border-top: 1px solid #DCD6CB; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="kop">
        <div class="nama">{{ $perusahaan !== '' ? $perusahaan : $merek }}</div>
        @if ($alamat !== '')
            <div class="alamat">{{ $alamat }}@if ($telepon !== ''), Telepon {{ $telepon }}@endif</div>
        @endif
    </div>

    <h1>{{ $judul }}</h1>
    <p class="nomor">Nomor {{ $transfer->reference ?: $transfer->code }}</p>

    <p>
        Pada hari ini, {{ $tanggal->translatedFormat('l') }} tanggal {{ $tanggal->translatedFormat('d F Y') }},
        @if ($jenis === 'bast')
            telah dilakukan serah terima aset sebagaimana diuraikan di bawah ini, dari pihak yang menyerahkan
            kepada pihak yang menerima. Sejak dokumen ini ditandatangani, tanggung jawab atas aset tersebut
            beralih kepada pihak yang menerima.
        @else
            telah dilakukan mutasi aset sebagaimana diuraikan di bawah ini, dengan alasan
            {{ strtolower($transfer->reasonLabel()) }}. Perubahan ini sudah dicatat di dalam sistem
            dengan nomor {{ $transfer->code }}.
        @endif
    </p>

    <table class="data">
        <tr>
            <th>Kode aset</th>
            <td>{{ $asset?->code ?? 'Tidak tercatat' }}</td>
        </tr>
        <tr>
            <th>Nama aset</th>
            <td>{{ $asset?->name ?? 'Tidak tercatat' }}</td>
        </tr>
        <tr>
            <th>Merek dan tipe</th>
            <td>{{ trim(($asset?->brand ?? '').' '.($asset?->model ?? '')) ?: 'Tidak tercatat' }}</td>
        </tr>
        <tr>
            <th>Nomor seri</th>
            <td>{{ $asset?->serial_number ?: 'Tidak tercatat' }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $asset?->category?->pickerLabel() ?: 'Tidak tercatat' }}</td>
        </tr>
        <tr>
            <th>Kondisi saat diserahkan</th>
            <td>{{ $asset?->conditionLabel() ?? 'Tidak tercatat' }}</td>
        </tr>
    </table>

    <table class="data rincian">
        <tr>
            <th colspan="3">Perubahan yang terjadi</th>
        </tr>
        <tr>
            <td style="width: 34%; background: #F8F6F2;"><strong>Hal</strong></td>
            <td style="width: 33%; background: #F8F6F2;"><strong>Sebelum</strong></td>
            <td style="width: 33%; background: #F8F6F2;"><strong>Sesudah</strong></td>
        </tr>
        @forelse ($perubahan as $baris)
            <tr>
                <td>{{ $baris['label'] }}</td>
                <td>{{ $baris['dari'] }}</td>
                <td>{{ $baris['ke'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Tidak ada perubahan yang tercatat pada dokumen ini.</td>
            </tr>
        @endforelse
    </table>

    @if (filled($transfer->notes))
        <p><strong>Catatan:</strong> {{ $transfer->notes }}</p>
    @endif

    <p>
        Demikian berita acara ini dibuat dengan sebenarnya, untuk dipergunakan sebagaimana mestinya.
    </p>

    <table class="ttd">
        <tr>
            <td class="peran">{{ $pihakKiri['peran'] }}</td>
            <td class="peran">{{ $pihakKanan['peran'] }}</td>
        </tr>
        <tr>
            <td class="ruang"></td>
            <td class="ruang"></td>
        </tr>
        <tr>
            <td class="nama">{{ $pihakKiri['nama'] ?: '(...........................)' }}</td>
            <td class="nama">{{ $pihakKanan['nama'] ?: '(...........................)' }}</td>
        </tr>
        <tr>
            <td class="jabatan">{{ $pihakKiri['jabatan'] ?: '' }}</td>
            <td class="jabatan">{{ $pihakKanan['jabatan'] ?: '' }}</td>
        </tr>
    </table>

    <div class="kaki">
        Dokumen ini dibuat dari catatan {{ $transfer->code }} di {{ $merek }} pada
        {{ now()->translatedFormat('d F Y H:i') }}. Isi tabel perubahan dihitung dari selisih keadaan aset
        sebelum dan sesudah serah terima, bukan diketik ulang.
    </div>
</body>
</html>
