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
        $tablePrefix = config('pkg-task-ms.table_prefix');
        $activityTablePrefix = config('pkg-activities.table_prefix');

        Schema::create($tablePrefix . 'activity_assignments', function (Blueprint $table) use ($activityTablePrefix) {
            $table->id();
            $table->foreignId('activity_id')
                ->nullable(false)
                ->comment('')
                ->constrained($activityTablePrefix . 'activities');
            $table->unsignedBigInteger('subject_id')
                ->nullable(false)
                ->comment('');
            $table->string('subject_type')
                ->nullable(false)
                ->comment('');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'activity_assignments');
    }
};
