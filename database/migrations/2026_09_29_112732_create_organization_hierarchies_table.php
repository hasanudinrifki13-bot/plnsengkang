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
        Schema::create('organization_hierarchies', function (Blueprint $table) {
            $table->id();
            $table->string('incumbent'); // e.g. ASMAN OP, TL RENDALOP
            $table->string('parent');    // e.g. MUP, ASMAN OP
            $table->string('incumbent_title')->nullable(); // e.g. Asisten Manajer Operasi
            $table->string('parent_title')->nullable();    // e.g. Manager UP Sengkang
            $table->string('person_name')->nullable();     // e.g. H. Muhammad Ismail
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_hierarchies');
    }
};
