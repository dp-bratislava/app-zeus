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
        $tablePrefix = config('database.table_prefix');

        // inspection template groups
        Schema::table($tablePrefix . 'activity_templatables', function (Blueprint $table) {
            $table->renameColumn('subject_id', 'templatable_id');
            $table->renameColumn('subject_type', 'templatable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::table($tablePrefix . 'activity_templatables', function (Blueprint $table) {
            $table->renameColumn('templatable_id', 'subject_id');
            $table->renameColumn('templatable_type', 'subject_type');
        });
    }
};
