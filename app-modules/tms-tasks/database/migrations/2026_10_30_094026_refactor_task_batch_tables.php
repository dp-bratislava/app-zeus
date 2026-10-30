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

        // clean up tables
        Schema::dropIfExists($tablePrefix . 'contracts');
        Schema::dropIfExists($tablePrefix . 'operations');
        Schema::dropIfExists($tablePrefix . 'task_item_groups');
        Schema::dropIfExists($tablePrefix . 'task_subjects');
        Schema::dropIfExists($tablePrefix . 'tig_operations');
        Schema::dropIfExists($tablePrefix . 'department_tigs');

        // Schema::create($tablePrefix . 'groups', function (Blueprint $table) {
        //     $table->comment('Grups of task item groups by department and handling mode');
        //     $table->id();
        //     $table->string('code')->unique();
        //     $table->string('title');
        // });

        Schema::create($tablePrefix . 'department_group', function (Blueprint $table) use ($tablePrefix, $taskTablePrefix) {
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

        Schema::create(
            $tablePrefix . 'department_group_items',
            function (Blueprint $table) use ($tablePrefix, $taskTablePrefix) {
                $table->comment('List task item groups available for batch assignment per deparmtnet.');
                $table->id();

                $table->unsignedBigInteger('tbdg_id')
                    ->comment('Task batch group for specific department binding');
                $table->unsignedBigInteger('task_item_group_id');
                $table->boolean('is_scalable')
                    ->nullable(false)
                    ->default(false)
                    ->comment('Determines GUI form component type.');

                $table->string('label')
                    ->comment('Display label possibly different than task item group title. For task batch form purpose.');

                $table->timestamps();

                $table->foreign('tbdg_id')
                    ->references('id')
                    ->on($tablePrefix . 'department_group')
                    ->cascadeOnDelete();

                $table->foreign('task_item_group_id')
                    ->references('id')
                    ->on($taskTablePrefix . 'task_item_groups')
                    ->cascadeOnDelete();

                $table->unique(
                    [
                        'tbdg_id',
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
        $taskTablePrefix = config('pkg-tasks.table_prefix');

        Schema::dropIfExists($tablePrefix . 'department_group_items');
        Schema::dropIfExists($tablePrefix . 'department_group');
        // Schema::dropIfExists($tablePrefix . 'groups');

        Schema::create(
            $tablePrefix . 'department_tigs',
            function (Blueprint $table) use ($tablePrefix, $taskTablePrefix) {
                $table->comment('List task item groups available for batch assignment per deparmtnet.');
                $table->id();

                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('task_item_group_id');
                $table->boolean('is_scalable')
                    ->nullable(false)
                    ->default(false)
                    ->comment('Determines GUI form component type.');

                $table->string('label')
                    ->comment('Display label possibly different than task item group title. For task batch form purpose.');

                $table->timestamps();

                $table->foreign('department_id')
                    ->references('id')
                    ->on('datahub_departments')
                    ->cascadeOnDelete();

                $table->foreign('task_item_group_id')
                    ->references('id')
                    ->on($taskTablePrefix . 'task_item_groups')
                    ->cascadeOnDelete();

                $table->unique(
                    [
                        'department_id',
                        'task_item_group_id',
                    ],
                    $tablePrefix . 'tig_operations_unq'
                );
            }
        );
    }
};
