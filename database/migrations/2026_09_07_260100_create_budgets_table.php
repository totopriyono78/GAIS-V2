<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pagu anggaran per departemen, per kategori biaya, per tahun.
 *
 * Hanya pagunya yang disimpan. Realisasi tidak pernah punya kolom di tabel ini, karena
 * angka realisasi yang disimpan adalah angka yang bisa basi: satu perintah kerja ditutup
 * hari ini dan seluruh baris anggaran yang menyimpan realisasinya langsung salah sampai
 * ada yang menghitung ulang. Realisasi selalu dijumlahkan saat dibaca.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->unsignedSmallInteger('fiscal_year');

            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Satu departemen hanya punya satu pagu untuk satu kategori dalam satu tahun.
            // Tanpa kunci ini, dua baris untuk pasangan yang sama akan membuat pertanyaan
            // "pagunya berapa" punya dua jawaban yang sama sahnya.
            $table->unique(['department_id', 'expense_category_id', 'fiscal_year'], 'budgets_unik');
            $table->index(['fiscal_year', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
