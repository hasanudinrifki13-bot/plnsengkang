<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();
            $table->enum('from_level', ['STAFF', 'TL', 'ASMAN']);
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('to_level', ['TL', 'ASMAN', 'MUP']);
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason');
            $table->enum('status', ['Pending', 'Accepted', 'Decided', 'Closed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalations');
    }
};
