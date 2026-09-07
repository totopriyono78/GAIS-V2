<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('supply_item_id')->constrained('supply_items')->cascadeOnDelete();
            // masuk, keluar, koreksi_tambah, koreksi_kurang
            $table->string('type', 20);
            /*
             * Jumlah disimpan bertanda: masuk dan koreksi tambah bernilai positif,
             * keluar dan koreksi kurang bernilai negatif. Dengan begitu stok barang
             * adalah SUM(quantity) belaka, satu kueri, dan tidak mungkin melenceng
             * dari riwayatnya. Layar tetap meminta angka positif, tandanya dipasang
             * model dari jenis mutasinya.
             */
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->date('transaction_date');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('supplier', 150)->nullable();
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['supply_item_id', 'transaction_date']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_transactions');
    }
};
