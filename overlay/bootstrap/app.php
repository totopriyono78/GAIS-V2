<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /*
         * Proxy di depan aplikasi dipercaya.
         *
         * Di Railway, dan di hampir semua penyedia lain, HTTPS-nya diselesaikan oleh
         * proxy mereka, lalu permintaannya diteruskan ke aplikasi ini lewat HTTP biasa.
         * Tanpa baris ini Laravel membaca permintaan itu apa adanya, yaitu http, dengan
         * dua akibat yang keduanya terlihat langsung oleh pemakai:
         *
         * 1. Setiap alamat yang dibuat sendiri oleh aplikasi berawalan http, padahal
         *    halamannya dibuka lewat https. Peramban menolak memuatnya sebagai isi
         *    campuran, dan berkas gaya, ikon, serta unggahan berhenti tampil.
         * 2. Alamat asal yang tercatat di log masuk adalah alamat proxy, sama untuk
         *    semua orang, sehingga catatan itu berhenti menjawab pertanyaan siapa
         *    masuk dari mana.
         *
         * Nilainya '*' karena alamat proxy Railway tidak tetap dan tidak diumumkan.
         * Di jaringan sendiri yang alamat proxy-nya diketahui, alamat itu yang pantas
         * ditulis, bukan bintang.
         */
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
