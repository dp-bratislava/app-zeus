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

        Schema::create(
            $tablePrefix . 'department_task_group_items',
            function (Blueprint $table) use ($tablePrefix, $taskTablePrefix) {
                $table->comment('List task item groups available for batch assignment per deparmtnet.');
                $table->id();

                $table->unsignedBigInteger('department_task_group_id')
                    ->comment('Task batch group for specific department binding');
                $table->unsignedBigInteger('task_item_group_id');
                $table->boolean('is_scalable')
                    ->nullable(false)
                    ->default(false)
                    ->comment('Determines GUI form component type.');

                $table->string('label')
                    ->comment('Display label possibly different than task item group title. For task batch form purpose.');

                $table->timestamps();

                $table->foreign(
                    'department_task_group_id',
                    $tablePrefix . 'department_task_group_fk'
                )
                    ->references('id')
                    ->on($tablePrefix . 'department_task_group')
                    ->cascadeOnDelete();

                $table->foreign(
                    'task_item_group_id',
                    $tablePrefix . 'task_item_group_fk'
                )
                    ->references('id')
                    ->on($taskTablePrefix . 'task_item_groups')
                    ->cascadeOnDelete();

                $table->unique(
                    [
                        'department_task_group_id',
                        'task_item_group_id',
                    ],
                    $tablePrefix . 'group_item_unq'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix') . 'task_batch_';

        Schema::dropIfExists($tablePrefix . 'department_task_group_items');
    }
};
