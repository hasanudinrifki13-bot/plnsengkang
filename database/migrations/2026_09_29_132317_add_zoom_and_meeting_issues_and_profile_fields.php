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
        // 1. Tambah kolom zoom dan mode pertemuan pada tabel meetings
        Schema::table('meetings', function (Blueprint $table) {
            if (!Schema::hasColumn('meetings', 'meeting_mode')) {
                $table->string('meeting_mode')->default('Offline')->after('location');
            }
            if (!Schema::hasColumn('meetings', 'zoom_link')) {
                $table->text('zoom_link')->nullable()->after('meeting_mode');
            }
            if (!Schema::hasColumn('meetings', 'zoom_meeting_id')) {
                $table->string('zoom_meeting_id')->nullable()->after('zoom_link');
            }
            if (!Schema::hasColumn('meetings', 'zoom_passcode')) {
                $table->string('zoom_passcode')->nullable()->after('zoom_meeting_id');
            }
        });

        // 2. Buat tabel pivot meeting_issues untuk pemilihan isu spesifik yang diagendakan pada rapat
        if (!Schema::hasTable('meeting_issues')) {
            Schema::create('meeting_issues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
                $table->foreignId('issue_id')->constrained('issues')->cascadeOnDelete();
                $table->string('status')->default('Agenda'); // Agenda, Dibahas, Diputuskan, Ditunda
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['meeting_id', 'issue_id']);
            });
        }

        // 3. Tambah kolom phone pada users jika belum ada
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_issues');

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['meeting_mode', 'zoom_link', 'zoom_meeting_id', 'zoom_passcode']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
