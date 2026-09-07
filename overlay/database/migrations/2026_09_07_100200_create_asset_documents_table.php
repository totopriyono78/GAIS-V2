<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            // kartu_garansi, manual_book, faktur, kontrak_sewa, sertifikat, foto, lainnya
            $table->string('type', 30);
            $table->string('name', 150);
            $table->string('file_path', 255);
            // Disimpan supaya nama asli berkas tetap terbaca di layar, sementara nama
            // berkas di cakram dibuat acak oleh Filament untuk menghindari tabrakan.
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_documents');
    }
};
