<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('issue_no')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('issue_type')->default('PROBLEM'); // PROBLEM, DECISION_REQUIRED, ACTION, INFORMATION
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('current_level')->default('TL'); // TL, ASMAN, MUP
            $table->string('priority')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('status')->default('Open'); // Open, Under Review, Waiting Decision, Need Decision, Escalated, Decided, Resolved, Closed
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
