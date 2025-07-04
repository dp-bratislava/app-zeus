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
        Schema::table('dpb_wtf_activities', function (Blueprint $table) {
            $table->foreign(['task_id'])
                ->references(['id'])
                ->on('dpb_wtf_tasks')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign(['template_id'])
                ->references(['id'])
                ->on('dpb_wtf_activity_templates')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign(['status_id'])
                ->references(['id'])
                ->on('dpb_wtf_activity_statuses')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign(['employee_contract_id'])
                ->references(['id'])
                ->on('datahub_employee_contracts')
                ->onUpdate('no action')
                ->onDelete('no action');                
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_activities', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['template_id']);
            $table->dropForeign(['status_id']);
            $table->dropForeign(['employee_contract_id']);
        });
    }
};
