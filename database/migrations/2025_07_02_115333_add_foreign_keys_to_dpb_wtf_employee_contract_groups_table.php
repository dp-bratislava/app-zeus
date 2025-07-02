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
        Schema::table('dpb_wtf_employee_contract_groups', function (Blueprint $table) {
            $table->foreign(['department_id'])->references(['id'])->on('datahub_departments')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_employee_contract_groups', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
};
