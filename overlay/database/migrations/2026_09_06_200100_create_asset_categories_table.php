<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            // Potongan awal kode aset, contoh GA-KOM menghasilkan GA-KOM-2026-0001
            $table->string('code_prefix', 20);
            $table->unsignedInteger('useful_life_months')->nullable();
            // garis_lurus, saldo_menurun, tidak_disusutkan
            $table->string('depreciation_method', 30)->default('garis_lurus');
            $table->decimal('residual_percent', 5, 2)->default(0);
            // Nomor akun diisi tim finance, dipakai mulai tahap integrasi jurnal
            $table->string('account_asset', 30)->nullable();
            $table->string('account_accumulated', 30)->nullable();
            $table->string('account_expense', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
