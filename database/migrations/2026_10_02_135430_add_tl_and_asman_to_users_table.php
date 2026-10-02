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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tl_id')->nullable()->constrained('users')->nullOnDelete()->after('unit_id');
            $table->foreignId('asman_id')->nullable()->constrained('users')->nullOnDelete()->after('tl_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tl_id']);
            $table->dropForeign(['asman_id']);
            $table->dropColumn(['tl_id', 'asman_id']);
        });
    }
};
