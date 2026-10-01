<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();
            $table->foreignId('decision_by')->constrained('users')->cascadeOnDelete();
            $table->enum('decision_level', ['TL', 'ASMAN', 'MUP']);
            $table->text('decision');
            $table->text('rationale')->nullable();
            $table->text('follow_up')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
