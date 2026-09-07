<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jenis permintaan perbaikan: listrik, AC, kebocoran, dan seterusnya.
 *
 * Dipisah jadi data induk, bukan daftar tetap di kode, karena dua hal yang melekat
 * padanya memang keputusan perusahaan dan bukan keputusan pemrogram: berapa lama
 * pekerjaan jenis ini seharusnya selesai, dan seberapa mendesak bawaannya.
 *
 * Target waktu sengaja dibiarkan kosong saat pertama kali diisi. Mengarang angka SLA
 * berarti menaruh janji yang tidak pernah dibuat siapa pun ke layar, dan janji itu akan
 * dikutip orang saat menilai kinerja tim GA.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_request_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();

            // rendah, normal, tinggi, mendesak. Dipakai sebagai bawaan saat tiket dibuat,
            // dan pemohon masih boleh menaikkannya.
            $table->string('default_priority', 20)->default('normal');

            // Berapa jam sejak tiket diterima tim GA sampai seharusnya selesai.
            // Kosong berarti belum ada target, dan layar mengatakannya apa adanya.
            $table->unsignedInteger('sla_hours')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_categories');
    }
};
