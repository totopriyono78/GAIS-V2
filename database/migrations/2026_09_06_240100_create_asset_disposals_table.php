<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_disposals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            // Satu aset hanya bisa dilepas sekali. Dijaga di tingkat basis data supaya
            // tidak bergantung pada layar saja.
            $table->foreignId('asset_id')->unique()->constrained('assets')->restrictOnDelete();
            $table->date('disposal_date');
            // dijual, dihibahkan, dimusnahkan, hilang, tukar_tambah
            $table->string('method', 30);
            $table->decimal('proceeds', 18, 2)->nullable();
            $table->string('counterparty', 150)->nullable();
            $table->string('reference', 100)->nullable();
            /*
             * Status aset sebelum dilepas disimpan supaya pembatalan pelepasan bisa
             * mengembalikan keadaan yang persis, bukan menebak dengan status aktif.
             */
            $table->string('previous_status', 20)->nullable();
            $table->foreignId('approved_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('document_path', 255)->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('disposal_date');
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
    }
};
