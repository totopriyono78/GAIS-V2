<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu kali barang datang.
 *
 * Satu pesanan bisa punya beberapa penerimaan, karena pemasok sering mengirim bertahap.
 * Menyimpan penerimaan sebagai dokumen tersendiri, bukan sebagai kolom "sudah diterima" di
 * pesanan, adalah yang membuat pertanyaan "kiriman kedua datang tanggal berapa dan surat
 * jalannya nomor berapa" bisa dijawab sama sekali.
 *
 * Nomor surat jalan disimpan apa adanya sebagai teks dan tidak dijadikan kunci unik. Pemasok
 * kecil kadang tidak memberi nomor, dan memaksakan keunikan akan membuat penerimaan kedua
 * dari pemasok yang sama tertolak hanya karena keduanya sama sama tidak bernomor.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('supply_purchase_id')->constrained('supply_purchases')->cascadeOnDelete();

            $table->date('receipt_date');
            $table->string('delivery_note_number', 80)->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['supply_purchase_id', 'receipt_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_receipts');
    }
};
