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
        Schema::create('dpb_ts_standardised_activity_groups', function (Blueprint $table) {
            $table->comment('Hierarchical groups of standardised activities');
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('dpb_ts_standardised_activity_groups', function (Blueprint $table) {
            $table->foreign(['department_id'])
                ->references(['id'])
                ->on('datahub_departments')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign(['parent_id'])
                ->references(['id'])
                ->on('dpb_ts_standardised_activity_groups')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // standardised activity tempaltes
        Schema::create('dpb_ts_standardised_activity_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->nullable(false)
                ->constrained('dpb_ts_standardised_activity_groups', 'id');
            $table->string('title');
            $table->integer('duration')
                ->nullable(false)
                ->comment('Expected duration in minutes');
            $table->boolean('is_divisible')
                ->default(false)
                ->comment('Divisible between multiple contracts');
            $table->integer('people')
                ->nullable(false)
                ->default(1)
                ->comment('Number of participants expected');
            // computed column with duration in minutes
            $table->integer('man_minutes')
                ->nullable()
                ->storedAs('duration * people')
                ->comment('Computed column with man_minutes');

            $table->timestamps();
            $table->softDeletes();
        });

        // standardised activities
        Schema::create('dpb_ts_standardised_activities', function (Blueprint $table) {
            $table->comment('Instances of standardised activity templates');
            $table->id();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->constrained('dpb_ts_tickets', 'id');
            $table->foreignId('template_id')
                ->nullable(false)
                ->constrained('dpb_ts_standardised_activity_templates', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_ts_standardised_activities');
        Schema::dropIfExists('dpb_ts_standardised_activity_templates');
        Schema::dropIfExists('dpb_ts_standardised_activity_groups');
    }
};
