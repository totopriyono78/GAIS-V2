<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foto kendaraan.
 *
 * Dipisah dari tabel dokumen, walaupun keduanya sama sama berkas, karena keduanya
 * menjawab pertanyaan yang berbeda dan punya umur yang berbeda. Dokumen punya masa
 * berlaku dan perlu diingatkan; foto tidak pernah kedaluwarsa, tetapi perlu diketahui
 * kapan diambil dan pada odometer berapa.
 *
 * Yang paling sering dicari dari tabel ini adalah foto keadaan sebelum kendaraan
 * dipinjam dan sesudah dikembalikan, karena di situlah lecet baru diperdebatkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            // depan, belakang, samping, interior, mesin, kerusakan, lainnya
            $table->string('type', 30)->default('lainnya');
            $table->string('name', 150);

            $table->string('file_path', 255);
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            // Kapan fotonya diambil, bukan kapan diunggah. Foto lecet yang diunggah
            // sebulan kemudian tidak membuktikan apa pun tanpa tanggal pengambilannya.
            $table->date('taken_date')->nullable();
            // Odometer saat foto diambil, kalau diketahui. Ini yang menghubungkan foto
            // keadaan dengan perjalanan tertentu saat log perjalanan dibangun nanti.
            $table->unsignedInteger('odometer_km')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['vehicle_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_photos');
    }
};
