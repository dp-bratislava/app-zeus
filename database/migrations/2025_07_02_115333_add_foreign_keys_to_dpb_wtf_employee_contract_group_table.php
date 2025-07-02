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
        Schema::table('dpb_wtf_employee_contract_group', function (Blueprint $table) {
            $table->foreign(['group_id'])->references(['id'])->on('dpb_wtf_employee_contract_groups')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['contract_id'])->references(['id'])->on('datahub_employee_contracts')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_employee_contract_group', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropForeign(['contract_id']);
        });
    }
};
