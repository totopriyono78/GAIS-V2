<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tingkat kewenangan melihat dokumen rahasia.
 *
 * Ini dimensi yang berdiri sendiri dari izin, dan itu bukan kerumitan yang
 * dibuat buat. Seseorang bisa punya izin documents.read dan tetap tidak boleh
 * membuka satu dokumen tertentu, karena yang menghalanginya bukan haknya atas
 * modul melainkan klasifikasi dokumen itu. Melebur keduanya berarti melahirkan
 * nama izin seperti documents.read.confidential, dan jumlah kombinasinya akan
 * terus bertambah tanpa pernah selesai.
 *
 * Angkanya sejajar dengan kolom confidentiality di tabel documents:
 * 0 public, 1 internal, 2 confidential, 3 restricted. Bawaannya 1, yang berarti
 * karyawan biasa membaca dokumen publik dan internal, tidak lebih.
 *
 * Sementara ini melekat langsung pada pengguna. Kalau nanti diturunkan dari
 * jabatan, cukup ubah User::clearanceLevel() dan kolom ini boleh dilepas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('clearance_level')->default(1)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('clearance_level');
        });
    }
};
