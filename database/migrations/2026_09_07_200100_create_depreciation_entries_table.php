<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Beban penyusutan satu aset pada satu periode.
 *
 * Angkanya dibekukan saat periode ditutup, tidak dihitung ulang saat dibaca. Kalau
 * nanti umur ekonomis atau nilai perolehan aset diubah, periode yang sudah ditutup
 * tetap menunjukkan angka yang dulu dilaporkan, dan itu memang yang dibutuhkan: buku
 * yang sudah ditutup tidak berubah sendiri di belakang punggung orang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depreciation_period_id')->constrained('depreciation_periods')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            // Disalin dari periodenya supaya riwayat satu aset bisa dibaca tanpa join.
            $table->char('period', 7);

            $table->decimal('expense', 18, 2);
            $table->decimal('accumulated_after', 18, 2);
            $table->decimal('book_value_after', 18, 2);

            // Keadaan aset saat dihitung, dibekukan bersama angkanya.
            $table->string('method', 30);
            $table->unsignedInteger('useful_life_months');
            $table->decimal('acquisition_cost', 18, 2);
            $table->decimal('residual_value', 18, 2)->default(0);

            // Lebih dari 1 berarti beban susulan: bulan bulan yang lewat sebelum periode
            // ini ikut dihitung sekaligus. Hanya terjadi pada penutupan pertama, atau
            // pada aset yang baru dicatat dengan tanggal perolehan mundur.
            $table->unsignedInteger('months_covered')->default(1);
            $table->char('covers_from', 7)->nullable();

            $table->timestamps();

            // Satu aset satu baris per periode. Dijaga di tingkat basis data, bukan hanya
            // di layar, supaya penutupan yang tidak sengaja dijalankan dua kali tidak
            // pernah bisa menggandakan bebannya.
            $table->unique(['asset_id', 'period']);
            $table->index(['period']);
            $table->index(['depreciation_period_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciation_entries');
    }
};
