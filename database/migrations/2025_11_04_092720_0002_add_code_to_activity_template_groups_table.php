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
        $tablePrefix = config('pkg-activities.table_prefix');

        // standardised activity groups
        Schema::table($tablePrefix . 'activity_template_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->string('code')
                ->after('id')
                ->nullable()
                ->comment('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-activities.table_prefix');

        Schema::table($tablePrefix . 'activity_template_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->dropColumn('code');
        });
    }
};
