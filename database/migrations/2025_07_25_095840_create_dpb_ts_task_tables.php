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
        // standardised activity groups
        Schema::create('dpb_ts_task_template_groups', function (Blueprint $table) {
            $table->comment('Hierarchical groups of task');
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // task statuses
        Schema::create('dpb_ts_task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify status in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::table('dpb_ts_activity_groups', function (Blueprint $table) {
        //     $table->foreign(['department_id'])
        //         ->references(['id'])
        //         ->on('datahub_departments')
        //         ->onUpdate('no action')
        //         ->onDelete('no action');
        //     $table->foreign(['parent_id'])
        //         ->references(['id'])
        //         ->on('dpb_ts_activity_groups')
        //         ->onUpdate('no action')
        //         ->onDelete('no action');
        // });

        // activity templates
        Schema::create('dpb_ts_task_templates', function (Blueprint $table) {
            $table->comment('List of task templates');
            $table->id();
            $table->string('title');
            $table->integer('duration')
                ->nullable(true)
                ->comment('Expected duration in minutes');
            $table->boolean('is_standardised')
                ->nullable(false)
                ->default(false)
                ->comment('Standardised task');
            $table->boolean('is_catalogised')
                ->nullable(false)
                ->default(false)
                ->comment('Catalogised task approved by company');
            $table->boolean('is_divisible')
                ->nullable(false)
                ->default(false)
                ->comment('Divisible between multiple contracts');
            $table->integer('people')
                ->nullable(false)
                ->default(1)
                ->comment('Number of participants expected');
            $table->foreignId('template_group_id')
                ->nullable(false)
                ->constrained('dpb_ts_task_template_groups', 'id');
            // computed column with duration in minutes
            $table->integer('man_minutes')
                ->nullable()
                ->storedAs('duration * people')
                ->comment('Computed column with man_minutes');

            $table->timestamps();
            $table->softDeletes();
        });

        // tasks
        Schema::create('dpb_ts_tasks', function (Blueprint $table) {
            $table->comment('List of tasks');
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->constrained('dpb_ts_tickets', 'id');
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained('dpb_ts_task_statuses', 'id');
            $table->foreignId('task_template_id')
                ->nullable()
                ->constrained('dpb_ts_task_templates', 'id');
            $table->text('note')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // task activities
        Schema::create('dpb_ts_task_activities', function (Blueprint $table) {
            $table->comment('List of task activities');
            $table->id();
            $table->foreignId('task_id')
                ->nullable(false)
                ->constrained('dpb_ts_tasks', 'id');
            $table->foreignId('employee_contract_id')
                ->nullable(false)
                ->constrained('datahub_employee_contracts', 'id');
            $table->date('date')->nullable();
            $table->dateTime('time_from')->nullable();
            $table->dateTime('time_to')->nullable();
            $table->text('note')->nullable();
            // computed column with duration in minutes
            $table->integer('duration')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
                ->comment('Computed column with real duration in minutes');            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_ts_task_activities');
        Schema::dropIfExists('dpb_ts_tasks');
        Schema::dropIfExists('dpb_ts_task_templates');
        Schema::dropIfExists('dpb_ts_task_template_groups');
        Schema::dropIfExists('dpb_ts_task_statuses');
    }
};
