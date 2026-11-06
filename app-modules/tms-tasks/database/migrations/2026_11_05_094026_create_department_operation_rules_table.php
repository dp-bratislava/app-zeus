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

        Schema::create($tablePrefix . 'department_operation_rules', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('Department-specific task item group configuration');
            $table->id();

            $table->unsignedBigInteger('department_task_group_item_id')
                ->comment('TMS department task group');

            $table->unsignedBigInteger('operation_id')
                ->comment('WTF operation');

            $table->string('subject_type')
                ->comment('TMS task assignment subject type morph class. E.g vehicle, building, ...');

            $table->json('conditions');

            $table->timestamps();

            $table->foreign(
                'department_task_group_item_id',
                $tablePrefix . 'department_task_group_item_fk'
            )
                ->references('id')
                ->on($tablePrefix . 'department_task_group_items')
                ->cascadeOnDelete();

            $table->foreign(
                'operation_id',
                $tablePrefix . 'department_operation_fk'
            )
                ->references('id')
                ->on('dpb_worktimefund_model_operation')
                ->cascadeOnDelete();

            $table->index(
                ['department_task_group_item_id', 'subject_type'],
                'operation_rules_group_subject_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix') . 'task_batch_';

        Schema::dropIfExists($tablePrefix . 'department_operation_rules');
    }
};
