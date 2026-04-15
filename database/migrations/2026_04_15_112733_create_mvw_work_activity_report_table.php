<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mvw_work_activity_report', function (Blueprint $table) {
            $table->id('id');

            $table->unsignedBigInteger('activity_id')->nullable()->unique()->comment('Source activity record id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('department_code', 10)->nullable();

            $table->unsignedBigInteger('task_id')->nullable();
            $table->dateTime('task_created_at')->nullable();
            $table->date('task_date')->nullable();

            $table->string('subject_code', 50)->nullable();
            $table->string('task_group_title', 255)->nullable();

            $table->string('task_maintenance_group', 50)->nullable();
            $table->string('task_maintenance_group_code', 50)->nullable();

            $table->string('task_author_lastname', 50)->nullable();

            $table->string('task_item_group_title', 255)->nullable();
            $table->string('task_item_maintenance_group', 50)->nullable();
            $table->string('task_item_maintenance_group_code', 50)->nullable();

            $table->string('task_item_author_lastname', 50)->nullable();

            $table->dateTime('wtf_task_created_at')->nullable();
            $table->date('activity_date')->nullable();

            $table->string('personal_id', 10)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('first_name', 255)->nullable();

            $table->string('wtf_task_title', 255)->nullable();

            $table->integer('expected_duration')->nullable()->comment('Expected duration in seconds');
            $table->integer('real_duration')->nullable()->comment('Real duration in seconds');

            $table->tinyInteger('is_fulfilled')->nullable();

            $table->dateTime('source_updated_at')->nullable()->comment('Source activity record was updated at');
            $table->dateTime('source_deleted_at')->nullable()->comment('Source activity record was deleted at');

            // indexes
            $table->index('activity_date', 'idx_activity_date');
            $table->index(
                ['department_id', 'department_code'],
                'idx_department'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mvw_work_activity_report');
    }
};