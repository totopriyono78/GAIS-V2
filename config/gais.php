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

    /*
     * Dibaca dengan FILTER_VALIDATE_BOOLEAN, bukan dengan (bool) biasa.
     *
     * Alasannya sepele tetapi nyata: (bool) menganggap teks apa pun yang tidak kosong
     * sebagai benar, termasuk kata "no" dan "off", sedangkan skrip pra-deploy di
     * railway/init-app.sh hanya menerima daftar ejaan tertentu. Dua tempat yang membaca
     * satu saklar dengan aturan yang berbeda adalah cara termudah membuat akun demonya
     * ada tetapi daftarnya tidak muncul, atau sebaliknya. Filter ini menerima true, 1,
     * yes, dan on, sama seperti skrip itu.
     */
    'demo_login' => filter_var(env('GAIS_DEMO_LOGIN', false), FILTER_VALIDATE_BOOLEAN),

    'demo_password' => env('GAIS_DEMO_PASSWORD', 'demo1234'),

];
