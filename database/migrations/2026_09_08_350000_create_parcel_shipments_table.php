<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengiriman paket keluar lewat kurir atau ekspedisi.
 *
 * **Kurirnya memakai tabel rekanan yang sudah ada, bukan tabel kurir tersendiri.**
 *
 * Sistem referensi memisahkan keduanya, dan itu sengaja tidak diikuti. Kurir adalah rekanan
 * yang mengirimkan faktur, sama seperti rekanan pemeliharaan dan pemasok ATK, dan memberinya
 * tabel kedua berarti satu perusahaan yang sama hidup di dua daftar dengan dua nama yang
 * pelan pelan berbeda. Yang lebih merugikan: tagihan dari kurir tidak akan bisa memakai modul
 * tagihan rekanan yang sudah berjalan sejak kiriman K, padahal itu justru tempat tagihannya
 * seharusnya bermuara.
 *
 * Diskon dan pajak yang di sistem referensi menempel pada kurir ditaruh di sini, per
 * pengiriman. Alasannya bukan penyederhanaan: diskon ekspedisi berubah menurut berat, tujuan,
 * dan kesepakatan bulan itu, jadi angka yang disimpan di kartu kurir akan hampir selalu
 * berbeda dari yang benar benar tertulis di resi.
 *
 * **Perkiraan dan kenyataan disimpan berdampingan**, seperti rencana dan kehadiran pada jadwal
 * jaga di kiriman Q. Perkiraan dipakai saat meminta, kenyataan dicatat saat paketnya benar
 * benar berangkat, dan selisih keduanya adalah kabar yang berguna bagi departemen yang
 * anggarannya dipotong.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcel_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            $table->date('request_date');

            $table->foreignId('requester_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            /*
             * Departemen yang dibebani biayanya. Wajib isi, karena tanpa itu biaya kiriman
             * tidak bisa dibebankan ke pagu siapa pun, dan biaya yang tidak terbebankan
             * adalah cara tercepat membuat laporan anggaran meleset tanpa ada yang tahu
             * sebabnya. Aturan yang sama dipakai barang keluar sejak kiriman C.
             */
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();

            $table->string('recipient_name', 200);
            $table->text('recipient_address');
            $table->string('recipient_phone', 30)->nullable();

            $table->string('contents', 255);
            $table->decimal('weight_kg', 8, 2)->nullable();

            // Perkiraan biaya saat meminta.
            $table->decimal('estimated_cost', 15, 2)->nullable();

            // diminta, dikirim, dibatalkan
            $table->string('status', 20)->default('diminta');

            // Kenyataan, terisi saat paketnya benar benar berangkat.
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('tracking_number', 100)->nullable();
            $table->date('shipped_date')->nullable();

            /*
             * Rincian biaya. Totalnya tidak pernah disimpan sebagai kolom, melainkan
             * dijumlahkan saat dibaca, dengan alasan yang sama seperti nilai tagihan sejak
             * kiriman K: angka yang disimpan akan basi begitu salah satu rinciannya diubah.
             */
            $table->decimal('shipping_cost', 15, 2)->nullable();
            $table->decimal('insurance_cost', 15, 2)->nullable();
            $table->decimal('packing_cost', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();

            $table->text('notes')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'request_date']);
            $table->index(['shipped_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_shipments');
    }
};
