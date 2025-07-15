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
        // activity statuses
        Schema::create('dpb_ts_activity_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify status in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // activities
        Schema::create('dpb_ts_activities', function (Blueprint $table) {
            $table->comment('List of activities ');
            $table->id();

            $table->date('date')->nullable();
            $table->foreignId('ticket_id')
                ->nullable(false)
                ->constrained('dpb_ts_tickets', 'id');
            $table->dateTime('time_from')->nullable();
            $table->dateTime('time_to')->nullable();
            $table->text('description')->nullable();
            // computed column with duration in minutes
            $table->integer('duration')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
                ->comment('Computed column with real duration in minutes');
            $table->foreignId('employee_contract_id')
                ->nullable(false)
                ->constrained('datahub_employee_contracts', 'id');
            $table->foreignId('status_id')
                ->nullable(false)
                ->constrained('dpb_ts_activity_statuses', 'id');
            $table->foreignId('standardised_activity_id')
                ->nullable()
                ->constrained('dpb_ts_standardised_activities', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_ts_activities');
        Schema::dropIfExists('dpb_ts_activity_statuses');
    }
};
