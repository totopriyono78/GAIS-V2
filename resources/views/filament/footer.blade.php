@php
    /*
     * Nama pemegang hak cipta dibaca dari tabel pengaturan, tidak ditulis di kode,
     * mengikuti aturan yang sama dengan nama perusahaan pada kop dokumen cetak: nama
     * badan hukum adalah hal yang salah kalau dikarang, dan salah di footer berarti
     * salah di setiap halaman sekaligus.
     *
     * Kalau pengaturannya dikosongkan, footer tidak digambar sama sekali. Baris hak
     * cipta tanpa pemiliknya tidak menyatakan apa apa.
     */
    $pemilik = trim((string) \App\Models\Setting::get('aplikasi.hak_cipta', ''));

    // Tahun dihitung, bukan disimpan, supaya tidak ada footer yang tertinggal di tahun
    // lalu hanya karena tidak ada yang ingat menggantinya setiap Januari.
    $tahun = now()->format('Y');
@endphp

@if (filled($pemilik))
    <footer class="gais-footer">
        &copy; {{ $tahun }} {{ $pemilik }}
    </footer>

    <style>
        .gais-footer {
            padding: 12px 0 20px;
            text-align: center;
            font-size: 12px;
            line-height: 18px;
            color: var(--gray-500, #76878E);
            /*
             * Garis tipis di atasnya memisahkan footer dari isi halaman tanpa menarik
             * perhatian. Warnanya mengikuti garis pemisah lain di aplikasi ini.
             */
            border-top: 1px solid var(--gray-200, #D2DADE);
            margin-top: 24px;
        }
    </style>
@endif
