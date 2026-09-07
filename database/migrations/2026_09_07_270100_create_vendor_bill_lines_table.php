<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Baris alokasi satu tagihan.
 *
 * Inilah yang membuat tagihan berguna untuk anggaran, bukan sekadar arsip faktur. Satu
 * tagihan listrik bisa dibebankan ke lima departemen sekaligus, dan tanpa baris alokasi
 * angka itu hanya bisa dilihat sebagai total kantor yang tidak menjawab siapa pemakainya.
 *
 * Kategori pada baris ini sengaja dibatasi pada kategori yang sumber realisasinya manual.
 * Kategori seperti pemeliharaan dan BBM sudah dijumlahkan dari catatan aslinya, dan
 * memasukkan fakturnya lagi di sini akan membuat angkanya terhitung dua kali. Pembatasan
 * itu dijaga di layar, bukan di basis data, supaya data lama tetap bisa dimasukkan kalau
 * suatu saat kebijakannya berubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bill_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_bill_id')->constrained('vendor_bills')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            // Boleh kosong untuk biaya yang memang milik kantor bersama, misalnya listrik
            // koridor. Angkanya lalu muncul sebagai biaya yang belum terbebankan.
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();

            $table->string('description', 200)->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->index(['expense_category_id', 'department_id']);
            $table->index('vendor_bill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bill_lines');
    }
};
