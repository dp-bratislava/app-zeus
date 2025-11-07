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
        Schema::create('dpb_wtf_activities', function (Blueprint $table) {
            $table->comment('List of activities ');
            $table->id();

            $table->date('date')->nullable();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('template_id');
            $table->dateTime('time_from')->nullable();
            $table->dateTime('time_to')->nullable();
            // computed column with duration in minutes
            $table->integer('duration')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(MINUTE, time_from, time_to)')
                ->comment('Computed column with real duration in minutes');
            $table->unsignedBigInteger('employee_contract_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('standardised_activity_id')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_activities');
    }
};
