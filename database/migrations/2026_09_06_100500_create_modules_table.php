<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->string('group', 50)->nullable();
            $table->string('icon', 80)->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->json('available_actions');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
