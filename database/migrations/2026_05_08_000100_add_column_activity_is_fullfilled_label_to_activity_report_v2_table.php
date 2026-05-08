<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mvw_work_activity_report_v2', function (Blueprint $table) {
            $table->string('activity_is_fulfilled_label')->after('activity_is_fulfilled');
        });
    }

    public function down(): void
    {
        Schema::table('mvw_work_activity_report_v2', function (Blueprint $table) {
            $table->dropColumn('activity_is_fulfilled_label');
        });
    }
};