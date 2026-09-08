<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu barang yang dihitung dalam satu sesi opname.
 *
 * Kode, nama, dan satuan barang ikut disalin ke sini, sama seperti opname aset menyalin kode
 * dan nama asetnya. Salinan itu yang membuat lembar opname tahun lalu tetap terbaca apa adanya
 * walau barangnya sudah diganti nama atau dinonaktifkan.
 *
 * Kolom system_quantity adalah stok menurut catatan **saat daftar disusun**, dibekukan. Ia
 * bukan dasar perhitungan koreksi, melainkan pembanding untuk mengetahui apakah stok sempat
 * bergerak selama penghitungan berjalan.
 *
 * Koreksi sendiri dihitung terhadap stok pada saat penyesuaian diterapkan, bukan terhadap
 * angka beku ini, dan itu keputusan yang perlu dijelaskan. Kalau daftar disusun Senin dengan
 * catatan 20, lalu Selasa 5 di antaranya diserahkan ke departemen, lalu Rabu dihitung fisik
 * dan ketemu 14, maka koreksi yang benar adalah kurang 1 dari 15, bukan kurang 6 dari 20.
 * Menghitungnya terhadap angka beku akan menghapus lima penyerahan yang sah dari buku stok.
 * Yang dilakukan aplikasi terhadap pergerakan itu adalah menyebutkannya di layar, bukan
 * diam diam mengabaikannya, karena hitungan fisik yang diambil sebelum barang bergerak memang
 * patut dicurigai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_opname_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_opname_id')->constrained('supply_opnames')->cascadeOnDelete();
            $table->foreignId('supply_item_id')->constrained('supply_items')->cascadeOnDelete();

            $table->string('item_code', 50);
            $table->string('item_name', 150);
            $table->string('unit', 20);

            $table->integer('system_quantity');
            $table->integer('counted_quantity')->nullable();
            $table->boolean('checked')->default(false);

            $table->string('notes', 200)->nullable();

            $table->foreignId('supply_transaction_id')->nullable()
                ->constrained('supply_transactions')->nullOnDelete();

            $table->timestamps();

            $table->unique(['supply_opname_id', 'supply_item_id']);
            $table->index(['supply_opname_id', 'checked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_opname_lines');
    }
};
