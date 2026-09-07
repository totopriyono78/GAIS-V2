<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            // Aset tidak boleh terhapus selama masih punya dokumen serah terima,
            // karena dokumen itu yang menjelaskan kenapa lokasinya berubah.
            $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
            $table->date('transfer_date');
            // mutasi_ruangan, ganti_pemegang, mutasi_departemen, perbaikan, lainnya
            $table->string('reason', 30);

            /*
             * Kolom from_* adalah salinan keadaan aset pada detik dokumen ini dibuat,
             * bukan ketikan orang. Kalau aset dipindah lagi besok, dokumen hari ini
             * harus tetap menunjukkan dari mana barangnya berangkat.
             */
            $table->foreignId('from_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('from_custodian_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('from_department_id')->nullable()->constrained('departments')->nullOnDelete();

            $table->foreignId('to_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('to_custodian_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->nullOnDelete();

            // Dua nama yang membuat dokumen ini menjadi serah terima, bukan sekadar catatan.
            $table->foreignId('handed_over_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('received_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_id', 'transfer_date']);
            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
