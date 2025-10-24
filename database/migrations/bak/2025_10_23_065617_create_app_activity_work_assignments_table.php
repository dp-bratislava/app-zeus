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
        $activityTablePrefix = config('pkg-activities.table_prefix');

        Schema::create($tablePrefix . 'activity_work_assignments', function (Blueprint $table) use ($activityTablePrefix) {
            $table->id();
            $table->date('date')
                ->nullable()
                ->comment('');
            $table->dateTime('time_from')
                ->nullable()
                ->comment('');
            $table->dateTime('time_to')
                ->nullable()
                ->comment('');
            $table->text('description')->nullable();
            // computed column with duration in minutes
            $table->integer('duration')
                ->nullable()
                ->comment('Duration in minutes');
            // ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
            // ->comment('Computed column with real duration in minutes');
            $table->text('note')
                ->nullable()
                ->comment('Optional note');
            $table->foreignId('activity_id')
                ->nullable(false)
                ->comment('Activity this work belongs to')
                ->constrained($activityTablePrefix . 'activities');
            $table->foreignId('employee_contract_id')
                ->nullable(false)
                ->comment('Contract of employee that did the work')
                ->constrained('datahub_employee_contracts', 'id');

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
