<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            /*
             * Kepemilikan dipisah dari cara perolehan. Sebelumnya "sewa" ikut menjadi
             * salah satu pilihan sumber perolehan, padahal keduanya menjawab pertanyaan
             * berbeda: sumber menjawab dari mana barangnya datang, kepemilikan menjawab
             * apakah barangnya milik perusahaan. Barang sewaan yang datang lewat proyek
             * tidak bisa diungkapkan kalau keduanya dijadikan satu kolom.
             */
            $table->string('ownership_type', 20)->default('milik')->after('asset_type');
            $table->date('lease_start_date')->nullable()->after('ownership_type');
            $table->date('lease_end_date')->nullable()->after('lease_start_date');
            $table->string('lessor', 150)->nullable()->after('lease_end_date');
            $table->string('lease_contract_number', 100)->nullable()->after('lessor');

            // baru, bekas
            $table->string('acquisition_condition', 20)->nullable()->after('acquisition_source');
            $table->string('project_name', 150)->nullable()->after('acquisition_condition');

            /*
             * warranty_until sudah ada sejak Tahap 2. Yang ditambah di sini tanggal
             * mulainya dan sakelar bergaransi atau tidak, supaya "belum diisi" bisa
             * dibedakan dari "memang tidak bergaransi".
             */
            $table->boolean('has_warranty')->default(false)->after('warranty_until');
            $table->date('warranty_from')->nullable()->after('has_warranty');

            $table->string('land_certificate_number', 100)->nullable()->after('warranty_from');
            $table->string('building_certificate_number', 100)->nullable()->after('land_certificate_number');

            $table->index('ownership_type');
            $table->index('lease_end_date');
            $table->index('warranty_until');
        });

        // Aset lama yang sumbernya tercatat sewa memang barang sewaan.
        DB::table('assets')->where('acquisition_source', 'sewa')->update(['ownership_type' => 'sewa']);

        // Aset lama yang tanggal garansinya sudah terisi berarti memang bergaransi.
        DB::table('assets')->whereNotNull('warranty_until')->update(['has_warranty' => true]);
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['ownership_type']);
            $table->dropIndex(['lease_end_date']);
            $table->dropIndex(['warranty_until']);

            $table->dropColumn([
                'ownership_type',
                'lease_start_date',
                'lease_end_date',
                'lessor',
                'lease_contract_number',
                'acquisition_condition',
                'project_name',
                'has_warranty',
                'warranty_from',
                'land_certificate_number',
                'building_certificate_number',
            ]);
        });
    }
};
