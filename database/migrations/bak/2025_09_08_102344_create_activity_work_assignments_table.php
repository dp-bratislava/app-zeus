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
        $actTablePrefix = config('pkg-activities.table_prefix');
        $wlTablePrefix = config('pkg-work-log.table_prefix');

        Schema::create($tablePrefix . 'activity_work_assignments', function (Blueprint $table) use ($tablePrefix, $actTablePrefix, $wlTablePrefix) {
            $table->comment('Pivot connecting actual work to planned activity.');
            $table->id();
            $table->foreignId('activity_id')
                ->nullable(false)
                ->comment('Specific activity instance the work is being done on.')
                ->constrained($actTablePrefix . 'activities', 'id');
            $table->foreignId('work_interval_id')
                ->nullable()
                ->comment('Work interval done on this activity instance.')
                ->constrained($wlTablePrefix . 'work_intervals', 'id');
            $table->foreignId('employee_contract_id')
                ->nullable(false)
                ->comment('Contract of employee that did the work')
                ->constrained('datahub_employee_contracts', 'id');
            $table->text('note')
                ->nullable()
                ->comment('Optional note');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'activity_work_assignments');
    }
};
