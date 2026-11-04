<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Copy the structure from the existing table
        DB::statement('CREATE TABLE mvw_work_activity_report_v3 LIKE mvw_work_activity_report_v2');
        
        Schema::table('mvw_work_activity_report_v3', function (Blueprint $table) {
            $table->string('subject_type')->nullable();
            $table->string('subject_label')->nullable();
            $table->string('subject_department')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mvw_work_activity_report_v3');
    }
};