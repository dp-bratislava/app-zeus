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
        $tablePrefix = config('pkg-work-log.table_prefix');

        // alter duration column
        Schema::table($tablePrefix . 'work_intervals', function (Blueprint $table) {
            $table->integer('duration')
                ->nullable()
                ->comment('Manualy inserted real duration in minutes')
                ->change();
        });

        // add computed duration column
        Schema::table($tablePrefix . 'work_intervals', function (Blueprint $table) {
            // computed column with duration in minutes
            $sql = '
                IF(
                    time_from IS NOT NULL AND time_to IS NOT NULL,
                    TIMESTAMPDIFF(MINUTE, time_from, time_to),
                    duration
                )                
            ';
            $table->integer('computed_duration')
                ->nullable()
                ->storedAs($sql)
                ->comment('Computed column with real duration in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-work-log.table_prefix');

        Schema::table($tablePrefix . 'work_intervals', function (Blueprint $table) {
            $table->dropColumn('computed_duration'); 
            $table->integer('duration')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
                ->comment('Computed column with real duration in minutes')
                ->change();
        });
    }
};
