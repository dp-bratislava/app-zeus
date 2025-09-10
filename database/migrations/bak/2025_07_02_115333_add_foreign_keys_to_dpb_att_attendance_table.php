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
        Schema::table('dpb_att_attendance', function (Blueprint $table) {
            $table->foreign(['calendar_group_id'])->references(['id'])->on('dpb_att_calendar_groups')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['employee_contract_id'])->references(['id'])->on('datahub_employee_contracts')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_att_attendance', function (Blueprint $table) {
            $table->dropForeign(['calendar_group_id']);
            $table->dropForeign(['employee_contract_id']);
        });
    }
};
