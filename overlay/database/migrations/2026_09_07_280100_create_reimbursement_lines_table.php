<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu baris adalah satu struk.
 *
 * Tanggalnya tanggal struk, bukan tanggal pengajuan, karena yang dibebankan ke anggaran
 * adalah kapan uangnya keluar. Struk Desember yang baru diajukan Januari tetap membebani
 * tahun lalu, sama seperti faktur rekanan yang memakai tanggal fakturnya.
 *
 * Berkas buktinya menempel di baris ini, bukan di pengajuannya, karena satu struk adalah
 * satu foto. Menaruhnya di tingkat pengajuan memaksa orang menggabung lima struk jadi satu
 * berkas sebelum bisa mengunggahnya, dan itu pekerjaan yang tidak perlu ada.
 *
 * Kategori dibatasi pada kategori bersumber tagihan lewat layar, bukan lewat basis data,
 * dengan alasan yang sama seperti baris tagihan rekanan: kategori seperti pemeliharaan dan
 * BBM sudah dijumlahkan dari catatan aslinya, dan memasukkannya lagi di sini akan membuat
 * angkanya terhitung dua kali.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursement_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_id')->constrained('reimbursements')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();

            $table->date('expense_date');
            $table->string('description', 200);
            $table->decimal('amount', 15, 2);

            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            $table->timestamps();

            $table->index(['expense_category_id', 'expense_date']);
            $table->index('reimbursement_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursement_lines');
    }
};
