<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dua tabel pendukung modul dokumen.
 *
 * document_saved_filters adalah folder virtual. Pemakai hampir pasti meminta
 * struktur folder karena itu yang mereka kenal, dan permintaannya akan datang
 * berulang meski sudah dijawab dengan kategori. Filter tersimpan adalah jawaban
 * yang tidak merusak modelnya: orang menyimpan kombinasi filter dengan nama
 * sendiri, membagikannya ke rekan, dan di layar terasa seperti folder, tetapi
 * satu dokumen boleh muncul di banyak filter tanpa digandakan.
 *
 * document_access_log mencatat siapa membuka dan mengunduh dokumen. Untuk audit,
 * mencatat siapa mengubah saja sering tidak cukup. Tabel ini tumbuh jauh lebih
 * cepat daripada tabel dokumennya, jadi ia dipartisi per tahun sejak awal.
 * Tanpa partisi, dalam dua tahun ia akan menjadi tabel terbesar di sistem dan
 * memperlambat pencadangan.
 *
 * Penomoran dokumen TIDAK memakai tabel penghitung sendiri. GAIS sudah punya
 * number_sequences beserta penghitung per periode, dan formatnya kebetulan
 * cocok: prefix SOP/GA dengan periode Y menghasilkan SOP/GA/2026/0012 persis
 * seperti rancangannya. Satu sistem penomoran lebih baik daripada dua.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_saved_filters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->jsonb('filters')->default('{}');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_shared')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'sort_order']);
        });

        // Kunci utamanya harus memuat kolom partisi, karena itu (id, accessed_at)
        // dan bukan id saja. Ini aturan PostgreSQL untuk tabel berpartisi.
        DB::statement("
            CREATE TABLE document_access_log (
                id          bigserial,
                document_id bigint NOT NULL,
                version_id  bigint,
                user_id     bigint NOT NULL,
                action      text   NOT NULL,
                ip_address  inet,
                accessed_at timestamptz NOT NULL DEFAULT now(),
                PRIMARY KEY (id, accessed_at),
                CONSTRAINT document_access_log_action_valid
                    CHECK (action IN ('view','preview','download','print','search_hit'))
            ) PARTITION BY RANGE (accessed_at)
        ");

        // Tahun ini dan tahun depan. Partisi tahun berikutnya dibuat perintah
        // terjadwal setiap Desember; tanpa partisi yang cocok, penulisan log
        // akan gagal dan itu ikut menggagalkan permintaan yang sedang berjalan.
        $tahun = (int) date('Y');

        foreach ([$tahun, $tahun + 1] as $t) {
            DB::statement(sprintf(
                "CREATE TABLE document_access_log_%d PARTITION OF document_access_log
                    FOR VALUES FROM ('%d-01-01') TO ('%d-01-01')",
                $t,
                $t,
                $t + 1,
            ));
        }

        DB::statement('CREATE INDEX document_access_log_doc
            ON document_access_log (document_id, accessed_at DESC)');
        DB::statement('CREATE INDEX document_access_log_user
            ON document_access_log (user_id, accessed_at DESC)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS document_access_log CASCADE');
        Schema::dropIfExists('document_saved_filters');
    }
};
