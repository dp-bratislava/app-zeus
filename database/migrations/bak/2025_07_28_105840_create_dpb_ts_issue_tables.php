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
        // issue statuses
        Schema::create('dpb_ts_issue_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(false);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('department_group_id')->nullable();
            $table->boolean('is_default')
                ->default(false)
                ->comment('Set as default in GUI pickers');
            $table->timestamps();
            $table->softDeletes();
        });

        // issue types
        Schema::create('dpb_ts_issue_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(true);
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('department_group_id')->nullable();
            $table->foreignId('parent_id')
                ->nullable()
                ->comment('Sets issue type hierarchy')
                ->constrained('dpb_ts_issue_types', 'id');
            $table->timestamps();
            $table->softDeletes();
        });


        // // issue sources
        // Schema::create('dpb_fleet_issue_sources', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('code')
        //         ->nullable(false)
        //         ->unique()
        //         ->comment('Unique code to identify status in application layer');
        //     $table->string('title')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // issues
        Schema::create('dpb_ts_issues', function (Blueprint $table) {
            $table->comment('List of issues');
            $table->id();
            $table->date('date')->nullable(false);
            $table->foreignId('type_id')
                ->nullable(false)
                ->constrained('dpb_ts_issue_types', 'id');
            $table->text('description')->nullable();
            // TO DO make separate history table for statuses
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained('dpb_ts_issue_statuses', 'id');
            // polymorphic binding to specific issue subject id
            $table->unsignedBigInteger('subject_id')
                ->comment('polymorphic binding to specific issue subject id')
                ->nullable()
                ->index();
            // polymorphic binding to specific issue subject class
            $table->string('subject_type')
                ->comment('polymorphic binding to specific issue subject class type')
                ->nullable()
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });

        // // issue status history
        // Schema::create('dpb_ts_issue_status_history', function (Blueprint $table) {
        //     $table->comment('');
        //     $table->id();
        //     $table->datetime('changed_at')->nullable(false);
        //     $table->foreignId('issue_id')
        //         ->nullable(false)
        //         ->constrained('dpb_ts_issues', 'id');
        //     $table->foreignId('status_id')
        //         ->nullable(false)
        //         ->constrained('dpb_ts_issue_statuses', 'id');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_ts_issue_status_history');
        Schema::dropIfExists('dpb_ts_issues');
        Schema::dropIfExists('dpb_ts_issue_types');
        Schema::dropIfExists('dpb_ts_issue_statuses');
    }
};
