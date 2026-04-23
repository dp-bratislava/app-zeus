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
            $table->unsignedBigInteger('task_id')->nullable();

            $table->date('task_date')->nullable();
            $table->string('task_title')->nullable();
            $table->text('task_description')->nullable();
            $table->string('task_group_title')->nullable();

            $table->string('task_assigned_to_type', 100)->nullable();
            $table->string('task_assigned_to_label', 100)->nullable();

            $table->string('task_requested_for_type', 100)->nullable();
            $table->string('task_requested_for_label', 100)->nullable();

            $table->string('task_subject_type', 255)->nullable();
            $table->text('task_subject_label')->nullable();

            $table->string('task_author_lastname', 100)->nullable();
            $table->string('task_place_of_origin')->nullable();

            $table->timestamp('task_created_at')->nullable();

            $table->date('task_item_date')->nullable();
            $table->string('task_item_title')->nullable();
            $table->text('task_item_description')->nullable();
            $table->string('task_item_group_title')->nullable();

            $table->string('task_item_assigned_to_type', 100)->nullable();
            $table->string('task_item_assigned_to_label', 100)->nullable();
            $table->string('task_item_author_lastname', 100)->nullable();

            $table->timestamp('task_item_created_at')->nullable();

            $table->timestamps(); // created_at, updated_at

            // Indexes
            $table->unique('task_item_id', 'uniq_task_item_snapshot');
            $table->index('task_item_id', 'mvw_task_item_snapshots_task_item_id_index');
            $table->index('updated_at', 'mvw_task_item_snapshots_updated_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_task_item_snapshots');
    }
};