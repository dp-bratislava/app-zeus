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
        Schema::table('mvw_work_activity_report_v2', function (Blueprint $table) {
            $table->string('activity_sap_code')
                ->after('activity_is_tolerated')
                ->nullable()
                ->comment('SAP code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mvw_work_activity_report_v2', function (Blueprint $table) {
            $table->dropColumn([
                'activity_sap_code',
            ]);
        });
    }
};
