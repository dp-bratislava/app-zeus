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

        Schema::create($tablePrefix . 'batches', function (Blueprint $table) use ($taskTablePrefix) {
            $table->id();

            $table->date('date');
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('task_group_id');
            $table->unsignedBigInteger('author_id');
            $table->json('configuration');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('batch_id')
                ->references('id')
                ->on('dpb_batchable_batches')
                ->cascadeOnDelete();

            $table->foreign('task_group_id')
                ->references('id')
                ->on($taskTablePrefix . 'task_groups')
                ->cascadeOnDelete();

            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();                

            $table->unique('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-task-ms.table_prefix') . 'task_batch_';

        Schema::dropIfExists($tablePrefix . 'batches');
    }
};
