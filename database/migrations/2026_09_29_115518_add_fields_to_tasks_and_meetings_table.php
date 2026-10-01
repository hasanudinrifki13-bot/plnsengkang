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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('decision_id')->nullable()->after('issue_id')->constrained('decisions')->nullOnDelete();
            $table->integer('week_number')->nullable()->after('due_date');
            $table->integer('year')->nullable()->after('week_number');
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('leader_id')->nullable()->after('type')->constrained('users')->nullOnDelete();
            $table->integer('week_number')->nullable()->after('leader_id');
            $table->integer('year')->nullable()->after('week_number');
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropColumn(['leader_id', 'week_number', 'year', 'notes']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['decision_id']);
            $table->dropColumn(['decision_id', 'week_number', 'year']);
        });
    }
};
