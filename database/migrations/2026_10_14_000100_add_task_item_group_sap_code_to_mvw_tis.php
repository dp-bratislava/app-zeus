<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mvw_task_item_snapshots', function (Blueprint $table) {
            $table->string('task_item_group_sap_code')->nullable()->after('task_item_group_title');
        });
    }

    public function down(): void
    {
        Schema::table('mvw_task_item_snapshots', function (Blueprint $table) {
            $table->dropColumn('task_item_group_sap_code');
        });
    }
};