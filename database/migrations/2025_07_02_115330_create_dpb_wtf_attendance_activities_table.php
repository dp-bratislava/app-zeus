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
        Schema::create('dpb_wtf_attendance_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('activity_id');
            $table->dateTime('time_from')->nullable();
            $table->integer('duration')->nullable()->comment('Real duration in minutes');
            $table->dateTime('time_to')->nullable();
            $table->boolean('is_fulfileld')->nullable();
            $table->unsignedBigInteger('standardised_activity_id')->nullable();
            $table->unsignedBigInteger('attendance_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpb_wtf_attendance_activities');
    }
};
