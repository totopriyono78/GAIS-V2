@php
    $versi = $getRecord()->currentVersion ?? $getRecord()->versiTerakhir();
@endphp

@if ($versi === null)
    <div class="gais-pratinjau-kosong">
        <p class="gais-pratinjau-judul">Belum ada berkas yang bisa ditampilkan</p>
        <p>Dokumen ini belum punya satu pun versi. Unggah berkasnya lebih dulu.</p>
    </div>
@elseif (! $versi->bolehDiunduh())
    <div class="gais-pratinjau-kosong">
        <p class="gais-pratinjau-judul">Berkas belum bisa dibuka</p>
        <p>Status pemeriksaannya {{ strtolower($versi->scan_status->label()) }}.</p>
    </div>
@elseif (! $versi->bisaDipratinjau())
    {{--
        Berkas Office belum bisa ditampilkan di tempat karena butuh layanan
        pengubah ke PDF, dan itu pekerjaan tahap berikutnya. Yang ditampilkan
        di sini keterangan apa adanya, bukan kotak kosong yang membuat orang
        mengira pratinjaunya rusak.
    --}}
    <div class="gais-pratinjau-kosong">
        <p class="gais-pratinjau-judul">{{ $versi->original_name }}</p>
        <p>
            Berkas {{ $versi->mime_type }} belum bisa ditampilkan langsung di halaman ini.
            Unduh berkasnya untuk membukanya di aplikasi Anda sendiri.
        </p>
    </div>
@else
    <div class="gais-pratinjau">
        <div class="gais-pratinjau-kepala">
            <span>{{ $versi->original_name }}</span>
            <span class="gais-pratinjau-ukuran">versi {{ $versi->version_number }} · {{ $versi->ukuranTerbaca() }}</span>
        </div>

        @if (str_starts_with($versi->mime_type, 'image/'))
            <img
                src="{{ route('gais.dokumen.pratinjau', [$getRecord(), $versi]) }}"
                alt="Pratinjau {{ $versi->original_name }}"
                class="gais-pratinjau-gambar"
                loading="lazy"
            >
        @else
            {{--
                sandbox tanpa allow-scripts. PDF adalah berkas dari pemakai, dan
                pembaca PDF di peramban bisa menjalankan JavaScript yang ada di
                dalamnya. Tanpa kurungan ini, satu berkas yang disiapkan orang
                bisa bertindak atas nama siapa pun yang membukanya.
            --}}
            <iframe
                src="{{ route('gais.dokumen.pratinjau', [$getRecord(), $versi]) }}"
                title="Pratinjau {{ $versi->original_name }}"
                class="gais-pratinjau-bingkai"
                sandbox="allow-same-origin"
                loading="lazy"
            ></iframe>
        @endif
    </div>
@endif
