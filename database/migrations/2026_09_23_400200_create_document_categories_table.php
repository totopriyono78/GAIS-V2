<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tempat menaruh dokumen, maksimal dua tingkat.
 *
 * Kategori menjawab "ditaruh di mana", tipe dokumen menjawab "ini dokumen apa".
 * Keduanya terpisah dan tidak saling menggantikan.
 *
 * Kedalamannya sengaja dibatasi dua tingkat, dan strukturnya sengaja bukan
 * folder. Dokumen yang relevan ke dua tempat, misalnya kontrak vendor yang
 * masuk akal ditaruh di Kontrak maupun di Vendor, memaksa orang menggandakan
 * berkasnya kalau memakai folder, dan salinan itu pasti menyimpang. Permintaan
 * struktur folder dijawab lewat filter tersimpan di document_saved_filters:
 * tampak seperti folder, tetapi satu dokumen boleh muncul di banyak tempat
 * tanpa digandakan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()
                ->constrained('document_categories')->restrictOnDelete();
            $table->string('code', 40)->unique();
            $table->string('name', 120);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_categories');
    }
};
