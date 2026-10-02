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
        if (!Schema::hasColumn('issues', 'unit_id')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('reported_by')->index();
            });
        }

        if (!Schema::hasColumn('tasks', 'unit_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('assigned_to')->index();
            });
        }

        // Backfill unit_id pada issues dari pelapor
        \DB::statement("
            UPDATE issues i 
            JOIN users u ON i.reported_by = u.id 
            SET i.unit_id = u.unit_id 
            WHERE i.unit_id IS NULL AND u.unit_id IS NOT NULL
        ");

        // Backfill unit_id pada tasks dari PIC pelaksana
        \DB::statement("
            UPDATE tasks t 
            JOIN users u ON t.assigned_to = u.id 
            SET t.unit_id = u.unit_id 
            WHERE t.unit_id IS NULL AND u.unit_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('issues', 'unit_id')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropColumn('unit_id');
            });
        }

        if (Schema::hasColumn('tasks', 'unit_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('unit_id');
            });
        }
    }
};
