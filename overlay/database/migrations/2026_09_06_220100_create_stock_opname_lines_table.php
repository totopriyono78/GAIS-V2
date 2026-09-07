<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            // Salinan kode dan nama saat daftar disusun. Kalau aset berganti nama
            // setelah opname berjalan, lembar hasilnya tetap menunjukkan apa yang
            // dibawa petugas ke lapangan.
            $table->string('asset_code', 80);
            $table->string('asset_name', 200);
            $table->foreignId('expected_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('expected_condition', 20);
            $table->string('expected_status', 20);
            $table->boolean('checked')->default(false);
            $table->boolean('found')->nullable();
            $table->foreignId('found_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('found_condition', 20)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->foreignId('checked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['stock_opname_id', 'asset_id']);
            $table->index(['stock_opname_id', 'checked']);
            $table->index('asset_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_lines');
    }
};
