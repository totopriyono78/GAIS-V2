<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tagihan dari rekanan.
 *
 * Satu tagihan adalah satu lembar faktur yang datang ke meja GA. Nilainya tidak disimpan
 * sebagai kolom di sini, melainkan dijumlahkan dari baris alokasinya, karena tagihan yang
 * totalnya tidak sama dengan jumlah rinciannya adalah cacat yang paling mahal ditemukan
 * belakangan, saat finance sudah terlanjur membayar.
 *
 * Tanggal faktur yang menentukan tahun anggaran mana yang terbebani, bukan tanggal bayar.
 * Faktur Desember yang dibayar Januari tetap beban tahun lalu, dan itu yang dipakai
 * tim finance saat menutup buku.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_bills', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('invoice_number', 80)->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            $table->text('description');

            // draft, diajukan, disetujui, dibayar, ditolak, dibatalkan
            $table->string('status', 20)->default('draft');

            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->date('paid_date')->nullable();
            $table->string('payment_reference', 80)->nullable();
            $table->foreignId('paid_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Pindaian fakturnya. Satu berkas, karena satu tagihan adalah satu lembar.
            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'invoice_date']);
            $table->index(['vendor_id', 'invoice_date']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bills');
    }
};
