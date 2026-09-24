@php
    $dokumen = $getRecord();

    /*
     * Log akses dibatasi 50 baris terakhir. Tabelnya dipartisi dan tumbuh jauh
     * lebih cepat daripada tabel dokumennya, jadi memuat seluruhnya di halaman
     * detail adalah cara yang pasti melambat seiring waktu tanpa ada yang
     * menyadarinya sampai dokumennya ramai dibuka.
     */
    $akses = $dokumen->accessLogs()
        ->with('user')
        ->orderByDesc('accessed_at')
        ->limit(50)
        ->get();

    $namaAksi = [
        'view' => 'Membuka',
        'preview' => 'Melihat pratinjau',
        'download' => 'Mengunduh',
        'print' => 'Mencetak',
        'search_hit' => 'Menemukan lewat pencarian',
    ];
@endphp

<div class="gais-riwayat">
    <div class="gais-riwayat-bagian">
        <h4 class="gais-riwayat-judul">Riwayat akses</h4>

        @if ($akses->isEmpty())
            <p class="gais-riwayat-kosong">
                Belum ada yang membuka atau mengunduh dokumen ini. Baris pertama akan muncul
                begitu ada yang menekan tombol unduh atau membuka pratinjaunya.
            </p>
        @else
            <table class="gais-riwayat-tabel">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Siapa</th>
                        <th>Melakukan apa</th>
                        <th>Dari alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($akses as $a)
                        <tr>
                            <td>{{ $a->accessed_at?->translatedFormat('d M Y, H:i') }}</td>
                            <td>{{ $a->user?->name ?? 'Pengguna terhapus' }}</td>
                            <td>{{ $namaAksi[$a->action] ?? $a->action }}</td>
                            <td>{{ $a->ip_address ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($akses->count() === 50)
                <p class="gais-riwayat-kosong">Menampilkan 50 kejadian terakhir.</p>
            @endif
        @endif
    </div>
</div>
