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
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'attachment')) {
                $table->string('attachment')->nullable()->after('description');
            }
        });

        // Backfill existing attachments from description if any
        $issues = \Illuminate\Support\Facades\DB::table('issues')->get();
        foreach ($issues as $issue) {
            if (preg_match('/\[Lampiran:\s*([^\]]+)\]/i', $issue->description, $matches)) {
                $filename = trim($matches[1]);
                \Illuminate\Support\Facades\DB::table('issues')
                    ->where('id', $issue->id)
                    ->update(['attachment' => $filename]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
