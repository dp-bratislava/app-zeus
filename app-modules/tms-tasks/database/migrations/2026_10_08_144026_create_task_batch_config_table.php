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

        Schema::create(
            $tablePrefix . 'task_batch_config',
            function (Blueprint $table) use ($tablePrefix, $taskTablePrefix) {
                $table->comment('List of subjects and task item group configurations for specific task batch.');

                $table->id();
                $table->unsignedBigInteger('task_batch_id');

                $table->unsignedBigInteger('subject_id');
                $table->string('subject_type');
                $table->unsignedBigInteger('task_item_group_id');
                $table->integer('quantity')
                    ->comment('Quantity reference for scalable opperations');

                $table->timestamps();

                $table->foreign('task_batch_id')
                    ->references('id')
                    ->on($tablePrefix . 'task_batches')
                    ->cascadeOnDelete();

                $table->foreign('task_item_group_id')
                    ->references('id')
                    ->on($taskTablePrefix . 'task_item_groups')
                    ->cascadeOnDelete();

                $table->index(
                    [
                        'subject_type',
                        'subject_id',
                    ],
                    $tablePrefix . 'task_batch_task_subjects_subject_idx'
                );
            }
        );

        Schema::create(
            $tablePrefix . 'task_batch_tig_operations',
            function (Blueprint $table) use ($tablePrefix, $taskTablePrefix) {
                $table->comment('List of worktime fund operations available for specific task item group.');
                $table->id();

                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('task_item_group_id');
                $table->unsignedBigInteger('wtf_operation_id');

                $table->timestamps();

                $table->foreign('department_id')
                    ->references('id')
                    ->on('datahub_departments')
                    ->cascadeOnDelete();

                $table->foreign('task_item_group_id')
                    ->references('id')
                    ->on($taskTablePrefix . 'task_item_groups')
                    ->cascadeOnDelete();

                $table->foreign('wtf_operation_id')
                    ->references('id')
                    ->on('dpb_worktimefund_model_operation')
                    ->cascadeOnDelete();

                $table->unique(
                    [
                        'department_id',
                        'task_item_group_id',
                        'wtf_operation_id',
                    ],
                    $tablePrefix . 'task_batch_tig_operations_unq'
                );
            }
        );

        Schema::create(
            $tablePrefix . 'task_batch_department_tigs',
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
                    $tablePrefix . 'task_batch_tig_operations_unq'
                );
            }
        );        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'task_batch_config');
        Schema::dropIfExists($tablePrefix . 'task_batch_department_tigs');
        Schema::dropIfExists($tablePrefix . 'task_batch_tig_operations');
    }
};
