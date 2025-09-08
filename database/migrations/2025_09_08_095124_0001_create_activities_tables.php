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
        $tablePrefix = config('pkg-activities.table_prefix');

        // standardised activity groups
        Schema::create($tablePrefix . 'activity_template_groups', function (Blueprint $table) {
            $table->comment('Hierarchical groups of activities');
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // activity statuses
        Schema::create($tablePrefix . 'activity_statuses', function (Blueprint $table) {
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
        Schema::create($tablePrefix . 'activity_templates', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of activity templates');
            $table->id();
            $table->string('title');
            $table->integer('duration')
                ->nullable(true)
                ->comment('Expected duration in minutes');
            $table->boolean('is_standardised')
                ->nullable(false)
                ->default(false)
                ->comment('Standardised activity');
            $table->boolean('is_catalogised')
                ->nullable(false)
                ->default(false)
                ->comment('Catalogised activities approved by company');
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
                ->constrained($tablePrefix . 'activity_template_groups', 'id');
            // computed column with duration in minutes
            $table->integer('man_minutes')
                ->nullable()
                ->storedAs('duration * people')
                ->comment('Computed column with man_minutes');

            $table->timestamps();
            $table->softDeletes();
        });

        // activities
        Schema::create($tablePrefix . 'activities', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of activities');
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained($tablePrefix . 'activity_statuses', 'id');
            $table->foreignId('activity_template_id')
                ->nullable()
                ->constrained($tablePrefix . 'activity_templates', 'id');
            $table->text('note')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-activities.table_prefix');

        Schema::dropIfExists($tablePrefix . 'activities');
        Schema::dropIfExists($tablePrefix . 'activity_statuses');
        Schema::dropIfExists($tablePrefix . 'activity_templates');
        Schema::dropIfExists($tablePrefix . 'activity_template_groups');
    }
};
