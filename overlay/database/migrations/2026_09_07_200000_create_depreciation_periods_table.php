<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Periode penyusutan yang sudah ditutup.
 *
 * Barisnya hanya lahir saat sebuah periode ditutup. Selama belum ditutup, angkanya
 * dihitung ulang tiap kali layarnya dibuka, jadi tidak ada keadaan setengah jadi yang
 * bisa dibaca orang sebagai angka resmi. Membuka kembali periode berarti menghapus
 * barisnya, dan penghapusan itu tercatat di jejak audit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciation_periods', function (Blueprint $table) {
            $table->id();
            // Bentuk YYYY-MM. Bulan, bukan tanggal, dan urutannya benar sebagai teks.
            $table->char('period', 7)->unique();
            $table->timestamp('closed_at');
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            // Angka rekap disimpan supaya daftar periode tidak perlu menjumlah ulang
            // ribuan baris entri hanya untuk menampilkan satu kolom.
            $table->decimal('total_expense', 18, 2)->default(0);
            $table->unsignedInteger('asset_count')->default(0);
            // Jumlah aset yang bebannya mencakup lebih dari satu bulan, yaitu susulan
            // pada penutupan pertama. Ditulis supaya lonjakan angka periode pertama
            // ada penjelasannya di layar, bukan cuma di kepala orang yang menutupnya.
            $table->unsignedInteger('catch_up_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciation_periods');
    }
};
