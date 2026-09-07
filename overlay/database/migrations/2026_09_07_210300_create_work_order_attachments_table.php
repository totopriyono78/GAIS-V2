<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lampiran perintah kerja: foto kerusakan, foto hasil perbaikan, nota bengkel, faktur.
 *
 * Bentuknya sama dengan dokumen pendukung aset, dan sengaja dibuat sama: jumlah berkas
 * per pekerjaan tidak tetap, dan tiap berkas perlu jenis serta namanya sendiri supaya
 * masih bisa dicari saat klaim garansi atau audit biaya setahun kemudian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            // foto_kerusakan, foto_hasil, nota, faktur, berita_acara, lainnya
            $table->string('type', 30);
            $table->string('name', 150);
            $table->string('file_path', 255);
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['work_order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_attachments');
    }
};
