<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Berapa banyak satu barang datang pada satu kali penerimaan.
 *
 * Baris ini yang menjembatani tiga hal yang selama ini terpisah di GAIS: pesanan ke pemasok,
 * mutasi stok, dan nanti tagihan yang dibayar. Sebelum kiriman N, stok ATK bertambah karena
 * seseorang mengetik baris barang masuk, dan tidak ada apa pun yang menghubungkan angka itu
 * dengan pesanan maupun dengan faktur. Tiga angka yang seharusnya sama hidup di tiga tempat
 * tanpa saling memeriksa.
 *
 * Mutasi stok yang lahir dari baris ini ditunjuk lewat supply_transaction_id, bukan dicari
 * lewat kolom reference yang berisi teks, dengan alasan yang sama seperti pada baris
 * permintaan pemakaian: satu baris tidak pernah bisa melahirkan dua mutasi, dan pertanyaan
 * "mutasi ini datang dari penerimaan yang mana" dijawab basis data, bukan pencocokan teks.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_receipt_id')->constrained('supply_receipts')->cascadeOnDelete();
            $table->foreignId('supply_purchase_line_id')->constrained('supply_purchase_lines')->cascadeOnDelete();

            $table->unsignedInteger('quantity');

            $table->foreignId('supply_transaction_id')->nullable()
                ->constrained('supply_transactions')->nullOnDelete();

            $table->timestamps();

            $table->unique(['supply_receipt_id', 'supply_purchase_line_id']);
            $table->index('supply_purchase_line_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_receipt_lines');
    }
};
