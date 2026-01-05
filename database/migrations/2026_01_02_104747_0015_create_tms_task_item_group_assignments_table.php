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
        $taskItemGroupTablePrefix = config('pkg-tasks.table_prefix');

        // task bindings
        Schema::create($tablePrefix . 'task_item_group_assignments', function (Blueprint $table) use ($taskItemGroupTablePrefix) {
            $table->comment("Relations binding task item group to other domains");
            $table->id();
            $table->foreignId('group_id')
                ->nullable(false)
                ->comment('')
                ->constrained($taskItemGroupTablePrefix . 'task_item_groups', 'id');
            // subject
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->comment('Single subject bound to this task. E.g. vehicle model, operation category, ...');
            $table->string('subject_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
        });

        // add indexes for polymorphic relations
        Schema::table($tablePrefix . 'task_item_group_assignments', function (Blueprint $table) use ($taskItemGroupTablePrefix) {
            $table->index(['subject_type', 'subject_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'task_item_group_assignments');
    }
};
