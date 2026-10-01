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
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('time_start', 20)->nullable()->default('08:00')->after('meeting_date');
            $table->string('time_end', 20)->nullable()->default('10:00')->after('time_start');
            $table->string('location')->nullable()->default('Ruang Rapat Utama')->after('time_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['time_start', 'time_end', 'location']);
        });
    }
};
