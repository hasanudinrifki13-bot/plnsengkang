<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('information', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('category', ['ARAHAN', 'KEBIJAKAN', 'PENGUMUMAN', 'REGULASI'])->default('PENGUMUMAN');
            $table->enum('target_level', ['ALL', 'MUP', 'ASMAN', 'TL', 'STAFF'])->default('ALL');
            $table->foreignId('target_unit_id')->nullable()->constrained('organization_units')->nullOnDelete();
            $table->foreignId('publisher_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('published_at')->useCurrent();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information');
    }
};
