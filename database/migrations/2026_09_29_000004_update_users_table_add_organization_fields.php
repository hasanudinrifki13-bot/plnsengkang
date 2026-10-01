<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_no')->nullable()->unique()->after('id');
            $table->foreignId('position_id')->nullable()->after('password')->constrained('positions')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('position_id')->constrained('organization_units')->nullOnDelete();
            $table->foreignId('role_id')->nullable()->after('unit_id')->constrained('roles')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['employee_no', 'position_id', 'unit_id', 'role_id', 'is_active']);
        });
    }
};
