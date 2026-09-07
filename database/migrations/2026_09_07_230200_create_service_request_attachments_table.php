<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foto yang dilampirkan pemohon saat membuka tiket.
 *
 * Bentuknya sama dengan lampiran perintah kerja dan dokumen aset, sengaja. Foto dari
 * pemohon adalah bukti keadaan sebelum diperbaiki, dan itu yang dipakai saat vendor
 * menagih pekerjaan yang tidak dikerjakan atau saat kerusakan disangkal sudah ada
 * sejak awal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('file_path', 255);
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('service_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_attachments');
    }
};
