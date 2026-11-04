<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mvw_work_activity_report_v2', function (Blueprint $table) {
            $table->string('group_sap_code')->nullable()->after('activity_sap_code');
        });
    }

    public function down(): void
    {
        Schema::table('mvw_work_activity_report_v2', function (Blueprint $table) {
            $table->dropColumn('group_sap_code');
        });
    }
};