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

        Schema::create($tablePrefix . 'operation_rules', function (Blueprint $table) {
            $table->comment('Rules for automated task batch operation assignment');
            $table->id();

            $table->foreignId('task_item_group_id')
                ->comment('TSK task item group')
                ->constrained('tsk_task_item_groups')
                ->cascadeOnDelete();

            $table->foreignId('operation_id')
                ->comment('WTF operation')
                ->constrained('dpb_worktimefund_model_operation')
                ->cascadeOnDelete();

            $table->string('subject_type')
                ->comment('TMS task assignment subject type morph class. E.g vehicle, building, ...');

            $table->json('conditions');

            $table->timestamps();

            $table->index(
                ['task_item_group_id', 'subject_type'],
                'operation_rules_group_subject_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix');

        Schema::dropIfExists($tablePrefix . 'operation_rules');
    }
};
