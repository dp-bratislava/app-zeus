<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tablePrefix = config('pkg-tasks.table_prefix');

        Schema::table($tablePrefix . 'task_item_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->string('sap_code')
                ->nullable()
                ->after('code')
                ->comment('SAP code of task item for further reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tasks.table_prefix');

        Schema::table($tablePrefix . 'task_item_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->dropColumn([
                'sap_code',
            ]);
        });
    }
};
