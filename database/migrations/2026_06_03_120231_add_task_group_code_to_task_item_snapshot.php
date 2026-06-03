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
        Schema::table('mvw_task_item_snapshots', function (Blueprint $table) {
            $table->string('task_group_code')
                ->after('task_description')
                ->nullable()
                ->comment('SAP code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mvw_task_item_snapshots', function (Blueprint $table) {
            $table->dropColumn([
                'task_group_code',
            ]);
        });
    }
};
