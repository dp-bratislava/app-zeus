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
        Schema::table('dpb_wtf_task_materials', function (Blueprint $table) {
            $table->foreign(['task_id'])
                ->references(['id'])
                ->on('dpb_wtf_tasks')
                ->onUpdate('no action')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpb_wtf_task_materials', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
        });
    }
};
