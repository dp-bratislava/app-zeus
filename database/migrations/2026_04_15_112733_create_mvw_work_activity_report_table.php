<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mvw_work_activity_report', function (Blueprint $table) {
            $table->id();

            $table->string('personal_id', 10)->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('department_code', 10)->nullable();

            $table->date('activity_date')->nullable();
            $table->string('activity_title')->nullable();

            $table->integer('activity_expected_duration')
                ->nullable()
                ->comment('Expected duration in seconds');

            $table->integer('activity_real_duration')
                ->nullable()
                ->comment('Real duration in seconds');

            $table->tinyInteger('activity_is_fulfilled')->nullable();

            $table->text('activity_subject_type')->nullable();
            $table->text('activity_subject_label')->nullable();

            $table->date('task_date')->nullable();
            $table->string('task_group_title')->nullable();

            $table->string('task_assigned_to_type', 50)->nullable();
            $table->string('task_assigned_to_label', 50)->nullable();

            $table->string('task_requested_for_type', 50)->nullable();
            $table->string('task_requested_for_label', 50)->nullable();

            $table->string('task_author_lastname', 50)->nullable();
            $table->string('task_item_group_title')->nullable();

            $table->string('task_item_assigned_to_type', 50)->nullable();
            $table->string('task_item_assigned_to_label', 50)->nullable();

            $table->string('task_item_author_lastname', 50)->nullable();

            $table->dateTime('wtf_task_created_at')->nullable();

            $table->unsignedBigInteger('activity_id')
                ->nullable()
                ->comment('Source activity record id');

            $table->unsignedBigInteger('task_id')->nullable();
            $table->dateTime('task_created_at')->nullable();
            $table->unsignedBigInteger('task_item_id')->nullable();

            $table->unsignedBigInteger('department_id')->nullable();

            $table->dateTime('source_updated_at')
                ->nullable()
                ->comment('Source activity record was updated at');

            $table->dateTime('source_deleted_at')
                ->nullable()
                ->comment('Source activity record was deleted at');

            // Indexes
            $table->unique('activity_id', 'mvw_work_activity_report_activity_id_unique');
            $table->index('activity_date', 'idx_activity_date');
            $table->index(['department_id', 'department_code'], 'idx_department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_work_activity_report');
    }
};