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
        $tablePrefix = config('pkg-task-ms.table_prefix') . 'task_batch_';
        $taskTablePrefix = config('pkg-tasks.table_prefix');

        Schema::create($tablePrefix . 'department_task_group', function (Blueprint $table) use ($taskTablePrefix) {
            $table->comment('Pivot binding task groups by department and handling mode');
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('task_group_id');
            $table->string('mode');

            $table->foreign('department_id')
                ->references('id')
                ->on('datahub_departments')
                ->cascadeOnDelete();

            $table->foreign('task_group_id')
                ->references('id')
                ->on($taskTablePrefix . 'task_groups')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix') . 'task_batch_';

        Schema::dropIfExists($tablePrefix . 'department_task_group');
    }
};
