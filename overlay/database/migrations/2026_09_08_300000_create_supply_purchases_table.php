<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pembelian ATK, dalam dua bentuk yang hidup di satu tabel.
 *
 * Keputusan pemilik proyek pada 8 September 2026: perusahaan memakai dua cara. Barang rutin
 * dipesan resmi ke pemasok langganan, barang mendadak dibeli langsung di toko atau lewat
 * marketplace. Kolom `kind` yang memisahkan keduanya, bukan dua tabel, karena pertanyaan yang
 * dibawa orang ke layar ini selalu sama untuk keduanya: apa yang kita beli bulan ini, dari
 * siapa, berapa, dan sudah datang atau belum.
 *
 * Yang membedakan keduanya hanya jalur persetujuannya, dan pembedaan itu ada alasannya.
 * Pesanan resmi disetujui manajer GA sebelum dikirim ke pemasok, karena saat itu uangnya
 * belum keluar dan persetujuan masih bisa mencegah sesuatu. Pembelian langsung dicatat
 * setelah barangnya sudah di tangan dan uangnya sudah keluar, jadi meminta persetujuan di
 * situ hanya sandiwara. Kendali untuk pembelian langsung ada di tagihan rekanan atau
 * penggantian biaya yang tetap perlu disetujui sebelum dibayar.
 *
 * Nilai pesanan tidak pernah disimpan di sini. Ia dijumlahkan dari barisnya, dengan alasan
 * yang sama seperti tagihan rekanan dan penggantian biaya: pesanan yang totalnya tidak sama
 * dengan jumlah rinciannya adalah cacat yang paling mahal ditemukan belakangan.
 *
 * Pemasok disimpan di dua kolom yang saling melengkapi. Pesanan resmi menunjuk rekanan
 * terdaftar lewat `vendor_id`. Pembelian langsung menulis nama tokonya begitu saja di
 * `supplier_name`, sesuai keputusan pemilik proyek pada tanggal yang sama, karena tidak ada
 * yang akan mendaftarkan sebuah marketplace sebagai rekanan hanya untuk membeli satu box
 * pulpen, dan memaksanya hanya akan melahirkan data induk berisi nama toko sekali pakai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supply_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();

            // pesanan, langsung
            $table->string('kind', 20)->default('pesanan');

            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('supplier_name', 150)->nullable();

            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->string('description', 200);
            $table->text('notes')->nullable();

            // draft, diajukan, disetujui, diterima_sebagian, selesai, ditolak, dibatalkan
            $table->string('status', 25)->default('draft');
            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            // Diisi untuk pembelian langsung, yang memang tidak melewati meja manajer.
            $table->text('approval_skipped_reason')->nullable();

            $table->text('rejection_reason')->nullable();

            // Diisi saat pesanan ditutup padahal sisanya belum datang seluruhnya. Sisa yang
            // tidak pernah dikirim pemasok adalah kejadian biasa, dan tanpa jalan menutupnya
            // daftar pesanan terbuka akan penuh selamanya oleh dua box yang tidak akan datang.
            $table->timestamp('closed_at')->nullable();
            $table->text('closing_reason')->nullable();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'order_date']);
            $table->index(['kind', 'status']);
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_purchases');
    }
};
