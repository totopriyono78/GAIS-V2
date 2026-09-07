<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->foreignId('asset_category_id')->constrained('asset_categories')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('custodian_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            // tetap, bergerak
            $table->string('asset_type', 20)->default('bergerak');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('acquisition_date')->nullable();
            // pembelian, hibah, sewa, data_lama
            $table->string('acquisition_source', 30)->default('pembelian');
            $table->decimal('acquisition_cost', 18, 2)->default(0);
            $table->decimal('residual_value', 18, 2)->default(0);
            $table->unsignedInteger('useful_life_months')->nullable();
            $table->string('depreciation_method', 30)->nullable();
            // aktif, dipinjam, perbaikan, tidak_dipakai, dilepas
            $table->string('status', 20)->default('aktif');
            // baik, perlu_perbaikan, rusak
            $table->string('condition', 20)->default('baik');
            $table->date('warranty_until')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('serial_number');
            $table->index(['status', 'condition']);
            $table->index(['asset_category_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
