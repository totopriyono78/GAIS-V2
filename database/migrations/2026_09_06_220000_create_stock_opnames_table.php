<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            // Cakupan digabung dengan DAN. Yang dikosongkan berarti tidak membatasi.
            $table->foreignId('scope_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('scope_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('scope_asset_category_id')->nullable()->constrained('asset_categories')->nullOnDelete();
            // draft, berjalan, selesai, dibatalkan
            $table->string('status', 20)->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->foreignId('started_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('finished_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('adjusted_at')->nullable();
            $table->foreignId('adjusted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
