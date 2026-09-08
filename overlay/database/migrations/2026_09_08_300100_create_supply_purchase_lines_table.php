<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris adalah satu jenis barang yang dibeli.
 *
 * Jumlah yang sudah diterima sengaja tidak disimpan sebagai kolom di sini. Ia dijumlahkan
 * dari baris penerimaan yang menunjuk baris ini, sama seperti stok yang dijumlahkan dari
 * mutasi sejak kiriman C. Kalau disimpan sebagai kolom, ia bisa melenceng dari riwayat
 * penerimaannya, dan pesanan yang mengaku sudah lengkap padahal barangnya belum datang
 * adalah cacat yang baru ketahuan saat orang mencari barang di gudang.
 *
 * Harga satuan ada di sini, bukan di kartu barang, karena harga adalah kesepakatan pada satu
 * pesanan tertentu. Harga di kartu barang hanyalah salinan harga pembelian terakhir, dipakai
 * untuk menilai persediaan, dan salinan itu diperbarui sendiri oleh mutasi barang masuk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_purchase_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_purchase_id')->constrained('supply_purchases')->cascadeOnDelete();
            $table->foreignId('supply_item_id')->constrained('supply_items')->cascadeOnDelete();

            $table->unsignedInteger('quantity_ordered');
            $table->decimal('unit_price', 15, 2);

            $table->string('notes', 200)->nullable();

            $table->timestamps();

            // Satu jenis barang cukup sekali dalam satu pesanan, dengan alasan yang sama
            // seperti pada permintaan pemakaian: dua baris untuk barang yang sama membuat
            // penerimaan sebagian mustahil dibaca, dan jumlahnya memang bisa digabung.
            $table->unique(['supply_purchase_id', 'supply_item_id']);
            $table->index('supply_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_purchase_lines');
    }
};
