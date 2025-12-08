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
        Schema::create($tablePrefix . 'task_item_assignments', function (Blueprint $table) use ($taskTablePrefix) {
            $table->comment("Relations binding task items to other domains");
            $table->id();
            $table->foreignId('task_item_id')
                ->nullable(false)
                ->comment('')
                ->constrained($taskTablePrefix . 'task_items', 'id');
            // entity responsible e.g. maintenance group, department, ...
            $table->unsignedBigInteger('assigned_to_id')
                ->nullable()
                ->comment('Entity responsible for this task item e.g. maintenance group, department, ...');
            $table->string('assigned_to_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('User that created task item')
                ->constrained('users');
            $table->foreignId('supervised_by')
                ->nullable()
                ->comment("User responsible for task item")
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // add indexes for polymorphic relations
        Schema::table($tablePrefix . 'task_item_assignments', function (Blueprint $table) use ($taskTablePrefix) {
            $table->index(['assigned_to_id', 'assigned_to_type'], 'idx_task_item_assigned_to');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'task_item_assignments');
    }
};
