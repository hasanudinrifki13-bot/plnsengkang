<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tasks', function (Blueprint $table) {
        if (!Schema::hasColumn('tasks', 'completion_attachment')) {
            $table->string('completion_attachment')->nullable()->after('status');
        }
        if (!Schema::hasColumn('tasks', 'completion_notes')) {
            $table->text('completion_notes')->nullable()->after('status');
        }
    });
}

public function down()
{
    Schema::table('tasks', function (Blueprint $table) {
        $table->dropColumn(['completion_attachment', 'completion_notes']);
    });
}
};
