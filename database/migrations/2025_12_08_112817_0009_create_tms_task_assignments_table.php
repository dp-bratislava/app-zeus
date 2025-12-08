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
        $taskTablePrefix = config('pkg-tasks.table_prefix');

        // task bindings
        Schema::create($tablePrefix . 'task_assignments', function (Blueprint $table) use ($taskTablePrefix) {
            $table->comment("Relations binding task to other domains");
            $table->id();
            $table->foreignId('task_id')
                ->nullable(false)
                ->comment('')
                ->constrained($taskTablePrefix . 'tasks', 'id');
            // subject
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this task. E.g. vehicle, building, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            // source
            $table->unsignedBigInteger('source_id')
                ->nullable(false)
                ->comment('Source of task. E.g. daily maintenance, crash report, manual ...');
            $table->string('source_type')
                ->nullable(false)
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            // department
            $table->foreignId('department_id')
                ->nullable()
                ->comment('Department this task will be hadled for.')
                ->constrained('datahub_departments');
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('User that created task')
                ->constrained('users');
            // entity responsible e.g. maintenance group, department, ...
            $table->unsignedBigInteger('assigned_to_id')
                ->nullable()
                ->comment('Entity responsible for this task item e.g. maintenance group, department, ...');
            $table->string('assigned_to_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");

            $table->timestamps();
            $table->softDeletes();
        });

        // add indexes for polymorphic relations
        Schema::table($tablePrefix . 'task_assignments', function (Blueprint $table) use ($taskTablePrefix) {
            $table->index(['source_type', 'source_id']);
            $table->index(['subject_type', 'subject_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'task_assignments');
    }
};
