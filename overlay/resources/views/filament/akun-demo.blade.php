@php
    /**
     * Daftar akun demo di bawah formulir masuk.
     *
     * Hanya tergambar kalau GAIS_DEMO_LOGIN menyala dan akunnya memang ada di basis
     * data. Keduanya diperiksa di App\Support\AkunDemo.
     */
    $akunDemo = \App\Support\AkunDemo::tersedia();
@endphp

@if (filled($akunDemo))
    <div
        class="gais-demo"
        x-data="{
            /*
             * Kotak isian diisi lalu peristiwa input dibangkitkan, persis seperti yang
             * terjadi saat orang mengetik. Menyetel nilainya saja tidak cukup: Livewire
             * mendengarkan peristiwanya, bukan perubahan nilai, jadi tanpa baris itu
             * formulir terkirim dengan isian yang menurut server masih kosong.
             */
            tulis(kotak, nilai) {
                kotak.value = nilai
                kotak.dispatchEvent(new Event('input', { bubbles: true }))
                kotak.dispatchEvent(new Event('change', { bubbles: true }))
            },
            masuk(surel, sandi) {
                const formulir = document.querySelector('form')

                if (! formulir) {
                    return
                }

                const kotakSurel = document.getElementById('data.email')
                    ?? formulir.querySelector('input[type=email]')
                const kotakSandi = document.getElementById('data.password')
                    ?? formulir.querySelector('input[type=password]')

                if (! kotakSurel || ! kotakSandi) {
                    return
                }

                this.tulis(kotakSurel, surel)
                this.tulis(kotakSandi, sandi)

                /*
                 * Jeda seperempat detik sebelum mengirim. Livewire menyusun perubahan
                 * isian ke dalam antrean, dan mengirim formulir pada detik yang sama
                 * kadang mendahului antrean itu. Kalau pengirimannya gagal, isiannya
                 * tetap terisi dan tombol Masuk masih bisa ditekan sendiri.
                 */
                setTimeout(() => formulir.requestSubmit(), 250)
            },
        }"
    >
        <div class="gais-demo-kepala">
            <h2 class="gais-demo-judul">Demo Accounts</h2>
            <p class="gais-demo-catatan">
                Klik salah satu untuk masuk tanpa mengetik. Tiap akun memperlihatkan sistem yang sama
                dari batas wewenang yang berbeda.
            </p>
        </div>

        <ul class="gais-demo-daftar">
            @foreach ($akunDemo as $akun)
                <li>
                    <button
                        type="button"
                        class="gais-demo-akun"
                        x-on:click="masuk(@js($akun['email']), @js(\App\Support\AkunDemo::kataSandi()))"
                    >
                        <span class="gais-demo-peran">{{ $akun['peran_label'] }}</span>
                        <span class="gais-demo-ringkas">{{ $akun['ringkas'] }}</span>
                        <span class="gais-demo-surel">{{ $akun['email'] }}</span>
                    </button>
                </li>
            @endforeach
        </ul>

        <p class="gais-demo-sandi">
            Kata sandi keempatnya sama: <code>{{ \App\Support\AkunDemo::kataSandi() }}</code>
        </p>
    </div>
@endif
