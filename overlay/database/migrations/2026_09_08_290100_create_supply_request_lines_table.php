<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris adalah satu jenis barang yang diminta.
 *
 * Ada dua kolom jumlah, dan keduanya memang perlu ada. Yang diminta ditulis pemohon dan
 * tidak pernah berubah lagi setelah diajukan. Yang diserahkan ditulis tim GA saat barangnya
 * benar benar keluar dari lemari.
 *
 * Menyatukan keduanya menjadi satu kolom akan menghapus kejadian yang paling sering terjadi
 * di gudang ATK: yang diminta sepuluh, yang ada tujuh, yang diserahkan tujuh. Kalau angkanya
 * ditimpa, tidak ada lagi yang tahu bahwa tiga sisanya pernah diminta dan tidak terpenuhi,
 * dan itu justru angka yang paling berguna saat menyusun pesanan berikutnya.
 *
 * Jumlah yang diserahkan boleh nol untuk baris yang stoknya benar benar kosong. Barisnya
 * tetap tersimpan sebagai catatan permintaan yang tidak terpenuhi, dan tidak melahirkan
 * mutasi stok apa pun.
 *
 * Mutasi stok yang lahir dari baris ini ditunjuk lewat supply_transaction_id, bukan dicari
 * lewat kolom reference yang berisi teks. Dengan begitu satu baris tidak pernah bisa
 * melahirkan dua mutasi, dan pertanyaan "mutasi ini datang dari permintaan yang mana"
 * dijawab basis data, bukan pencocokan teks.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_request_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_request_id')->constrained('supply_requests')->cascadeOnDelete();
            $table->foreignId('supply_item_id')->constrained('supply_items')->cascadeOnDelete();

            $table->unsignedInteger('quantity_requested');
            $table->unsignedInteger('quantity_issued')->nullable();

            $table->string('notes', 200)->nullable();

            $table->foreignId('supply_transaction_id')->nullable()
                ->constrained('supply_transactions')->nullOnDelete();

            $table->timestamps();

            // Satu jenis barang cukup sekali dalam satu permintaan. Dua baris untuk barang
            // yang sama hanya membuat pemeriksaan stok dan penyerahannya sulit dibaca,
            // dan jumlahnya memang bisa digabung jadi satu angka.
            $table->unique(['supply_request_id', 'supply_item_id']);
            $table->index('supply_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_request_lines');
    }
};
