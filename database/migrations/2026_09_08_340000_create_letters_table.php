<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Buku agenda surat, masuk dan keluar dalam satu tabel.
 *
 * Satu tabel, bukan dua, karena keduanya menyimpan hal yang sama: nomor, tanggal, perihal,
 * lawan bicara, dan pindaian. Yang berbeda hanya arah dan satu dua kolom yang mengikutinya,
 * dan memecahnya menjadi dua tabel berarti menggandakan setiap penyaring, setiap pencarian,
 * dan setiap laporan yang kelak menanyakan "surat apa saja bulan ini".
 *
 * **Dua nomor, dan keduanya berbeda pemilik.**
 *
 * `code` adalah nomor agenda milik GAIS, dibuat otomatis dan tidak pernah kembar. Ia yang
 * dipakai menyebut baris ini di dalam aplikasi.
 *
 * `letter_number` adalah nomor yang tertulis di kertas suratnya. Pada surat keluar itu nomor
 * perusahaan yang formatnya sudah punya aturan sendiri, memuat kode unit dan bulan romawi,
 * dan aplikasi ini tidak memaksakan format apa pun terhadapnya. Keputusan pemilik proyek pada
 * 8 September 2026: nomornya diketik mengikuti format perusahaan. Pada surat masuk, kolom yang
 * sama menyimpan nomor milik pengirimnya, dan itu boleh kosong karena tidak semua surat
 * bernomor.
 *
 * Karena pemiliknya berbeda, keunikannya pun berbeda. Nomor surat keluar tidak boleh kembar,
 * sebab dua surat perusahaan bernomor sama adalah kesalahan arsip. Nomor surat masuk boleh
 * kembar, sebab dua pengirim yang berbeda bebas memakai nomor yang kebetulan sama.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();

            // masuk, keluar
            $table->string('direction', 10);

            // Nomor agenda GAIS.
            $table->string('code', 40)->unique();

            // Nomor yang tertulis di suratnya sendiri.
            $table->string('letter_number', 100)->nullable();

            // Tanggal yang tertulis di suratnya.
            $table->date('letter_date');

            // Tanggal surat diterima atau dikirim, yaitu saat ia melewati meja GA.
            $table->date('logged_date');

            // undangan, penawaran, tagihan, pemberitahuan, permohonan, surat_tugas,
            // kontrak, lainnya
            $table->string('category', 30)->default('lainnya');

            // Pengirim pada surat masuk, tujuan pada surat keluar.
            $table->string('counterparty', 200);

            $table->string('subject', 255);
            $table->text('notes')->nullable();

            // Surat masuk: kepada siapa surat ini ditujukan di dalam kantor.
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            // Surat masuk: jejak serah terimanya.
            $table->timestamp('handed_over_at')->nullable();
            $table->foreignId('handed_over_to_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('handover_note')->nullable();

            // Surat keluar: siapa yang menandatangani.
            $table->foreignId('signer_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            // Pindaian suratnya.
            $table->string('file_path', 255)->nullable();
            $table->string('original_name', 255)->nullable();

            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['direction', 'logged_date']);
            $table->index(['category', 'logged_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
