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
        Schema::table('dpb_wtf_attendance_activities', function (Blueprint $table) {
            $table->foreign(['standardised_activity_id'])->references(['id'])->on('dpb_wtf_standardised_activities')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['attendance_id'])->references(['id'])->on('dpb_att_attendance')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['activity_id'])->references(['id'])->on('dpb_wtf_activities')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['task_id'])->references(['id'])->on('dpb_wtf_tasks')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_attendance_activities', function (Blueprint $table) {
            $table->dropForeign(['standardised_activity_id']);
            $table->dropForeign(['attendance_id']);
            $table->dropForeign(['activity_id']);
            $table->dropForeign(['task_id']);
        });
    }
};
