<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dokumen kendaraan yang punya masa berlaku: pajak tahunan, perpanjangan STNK lima
 * tahunan, uji KIR, dan asuransi.
 *
 * Satu baris adalah satu masa berlaku, bukan satu jenis dokumen. Perpanjangan tahun ini
 * tidak menimpa yang tahun lalu, melainkan menambah baris baru. Itu yang membuat
 * pertanyaan "pajak tahun lalu dibayar berapa dan kapan" bisa dijawab, dan yang membuat
 * kenaikan biaya perpanjangan dari tahun ke tahun terbaca tanpa membuka map fisik.
 *
 * Yang berlaku sekarang adalah baris dengan tanggal berakhir paling jauh untuk jenis
 * itu, dihitung saat dibaca. Tidak ada kolom penanda "aktif" yang harus dijaga tetap
 * benar, karena kolom seperti itu selalu berakhir salah pada baris yang terlupa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();

            // pajak_tahunan, stnk_lima_tahun, kir, asuransi, lainnya
            $table->string('type', 30);
            $table->string('document_number', 60)->nullable();

            $table->date('issued_date')->nullable();
            /*
             * Tanggal berakhir wajib isi. Dokumen kendaraan tanpa tanggal berakhir tidak
             * bisa diingatkan, dan dokumen yang tidak bisa diingatkan adalah alasan
             * paling umum perusahaan membayar denda pajak kendaraan.
             */
            $table->date('expires_at');

            // Siapa yang menerbitkan: Samsat mana, atau perusahaan asuransi mana.
            $table->string('issuer', 120)->nullable();
            $table->decimal('cost', 15, 2)->nullable();

            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Penyaring yang paling sering dipakai adalah "yang jatuh tempo bulan depan",
            // dan itu selalu menyapu tabel ini menurut tanggal berakhir.
            $table->index('expires_at');
            $table->index(['vehicle_id', 'type', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
