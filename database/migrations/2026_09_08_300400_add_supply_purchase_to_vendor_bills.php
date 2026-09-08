<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menghubungkan tagihan rekanan ke pesanan pembelian yang ditagihnya.
 *
 * Kolomnya boleh kosong, dan itu disengaja. Sebagian besar tagihan GA tidak punya pesanan
 * pembelian sama sekali: tagihan listrik, sewa gedung, dan jasa kebersihan datang tanpa
 * didahului pesanan barang. Mewajibkan kolom ini akan memaksa orang mengarang pesanan palsu
 * hanya supaya tagihan listrik bisa disimpan.
 *
 * Yang dijawab kolom ini hanya satu pertanyaan, tetapi pertanyaan itu yang paling sering
 * diajukan saat faktur ATK sampai di meja manajer: kenapa tagihannya lebih besar dari barang
 * yang datang. Sebelum ada kolom ini, jawabannya hanya bisa dicari dengan membuka pesanan,
 * membuka catatan penerimaan, lalu menjumlahkan sendiri di kertas.
 *
 * Nilai pesanan, nilai barang yang diterima, dan nilai tagihan tetap tidak disimpan di mana
 * pun. Ketiganya dijumlahkan saat dibaca, sehingga selisih yang ditampilkan tidak pernah bisa
 * basi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_bills', function (Blueprint $table) {
            $table->foreignId('supply_purchase_id')->nullable()->after('vendor_id')
                ->constrained('supply_purchases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendor_bills', function (Blueprint $table) {
            $table->dropForeign(['supply_purchase_id']);
            $table->dropColumn('supply_purchase_id');
        });
    }
};
