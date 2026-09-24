<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Induk untuk penolakan yang sah saat mengesahkan atau menutup versi dokumen.
 *
 * Bedanya dengan galat biasa: ini bukan kerusakan sistem melainkan keadaan
 * yang memang tidak boleh terjadi, dan pemakainya bisa memperbaikinya sendiri.
 * Karena itu layar yang memanggil cukup menangkap satu kelas ini, lalu
 * menampilkan pesannya apa adanya. Tidak ada satu pun keadaan di sini yang
 * pantas berakhir menjadi layar 500.
 */
abstract class MasalahVersiDokumen extends RuntimeException {}
