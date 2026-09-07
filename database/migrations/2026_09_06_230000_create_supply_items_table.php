<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            // Kategori dipakai untuk mengelompokkan dan menyaring saja, jadi cukup
            // daftar tetap di App\Models\SupplyItem::CATEGORIES. Tabel kategori sendiri
            // berarti satu modul dan satu layar CRUD lagi, tanpa manfaat yang sepadan.
            $table->string('category', 30);
            $table->string('unit', 20);
            $table->integer('minimum_stock')->default(0);
            // Tempat barang disimpan, biasanya gudang. Boleh kosong kalau belum ditentukan.
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('last_price', 15, 2)->nullable();
            // Akun beban dipakai nanti saat integrasi ke finance. Ditulis di sini supaya
            // tim finance bisa mengisinya lebih dulu, bukan supaya ada kolom kosong.
            $table->string('account_expense', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_items');
    }
};
