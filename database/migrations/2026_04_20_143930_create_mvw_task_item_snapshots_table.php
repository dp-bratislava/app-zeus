<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mvw_task_item_snapshots', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('task_item_id');

            // task
            $table->unsignedBigInteger('task_id')->nullable();
            $table->datetime('task_date')->nullable();
            $table->string('task_title', 255)->nullable();
            $table->text('task_description')->nullable();
            $table->string('task_group_title', 255)->nullable();
            $table->string('task_assigned_to_type', 255)->nullable()
                ->comment('Which subject will be handling the task. E.g. maintenance group, department.');
            $table->string('task_assigned_to_label', 255)->nullable();
            $table->string('task_requested_for_type', 255)->nullable()
                ->comment('Which subject will be handling the task. E.g. maintenance group, department.');
            $table->string('task_requested_for_label', 255)->nullable();
            $table->string('task_author_lastname', 100)->nullable();
            $table->string('task_place_of_origin', 255)->nullable();
            $table->timestamp('task_created_at')->nullable();

            // task item
            $table->datetime('task_item_date')->nullable();
            $table->string('task_item_title', 255)->nullable();
            $table->text('task_item_description')->nullable();
            $table->string('task_item_group_title', 255)->nullable();
            $table->string('task_item_assigned_to_type', 255)->nullable()
                ->comment('Which subject will be handling the task item. E.g. maintenance group, department.');
            $table->string('task_item_assigned_to_label', 255)->nullable();
            $table->string('task_item_author_lastname', 100)->nullable();
            $table->timestamp('task_item_created_at')->nullable();

            $table->timestamps();

            $table->unique('task_item_id', 'uniq_task_item_snapshot');
            $table->index('task_item_id');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_task_item_snapshots');
    }
};
