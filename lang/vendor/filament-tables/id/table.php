<?php

/*
 * Tambalan bahasa untuk tabel Filament.
 *
 * Paket bahasa Indonesia bawaan Filament belum lengkap. Tiga hal tidak ada di sana,
 * dan ketiganya muncul di layar sebagai bahasa Inggris di tengah halaman berbahasa
 * Indonesia: kolom ikon ya atau tidak, jumlah hasil, dan tulisan saat tabel dimuat.
 *
 * Laravel menumpuk berkas ini di atas berkas milik paket, jadi cukup menulis kunci
 * yang kurang saja. Kunci lain tetap datang dari paketnya, dan ikut terbarui sendiri
 * kalau paketnya nanti melengkapi terjemahannya.
 */

return [

    'columns' => [

        'icon' => [

            'boolean' => [
                'true' => 'Ya',
                'false' => 'Tidak',
            ],

        ],

    ],

    'loading' => 'Sedang dimuat...',

    'result_count' => '{0} Tidak ada hasil|{1} :count hasil|[2,*] :count hasil',

];
