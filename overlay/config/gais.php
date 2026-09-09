<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun demo di halaman masuk
    |--------------------------------------------------------------------------
    |
    | Kalau dinyalakan, halaman masuk menampilkan daftar akun demo yang bisa
    | diklik untuk langsung masuk tanpa mengetik. Ini memudahkan orang mencoba
    | sistem dari sudut pandang peran yang berbeda dalam satu sesi demo.
    |
    | Bawaannya mati, dan itu disengaja. Menyalakannya berarti menempelkan
    | alamat surel dan kata sandi empat akun di halaman yang bisa dibuka siapa
    | pun yang tahu alamatnya, termasuk akun berperan administrator. Ia hanya
    | pantas menyala pada pemasangan yang memang berisi data contoh, dan perlu
    | dimatikan lagi sebelum sistemnya dipakai sungguhan.
    |
    */

    'demo_login' => (bool) env('GAIS_DEMO_LOGIN', false),

    'demo_password' => env('GAIS_DEMO_PASSWORD', 'demo1234'),

];
