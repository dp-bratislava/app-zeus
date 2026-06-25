<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Maps a task item group (podzákazka type, e.g. "Pneumatiky") to an asset type /
     * device group. Drives the conditional "zariadenia" tab on the task item
     * (podzákazka) detail: the tab is shown only when the item's group is mapped to an
     * asset type.
     */
    public function up(): void
    {
        $table = config('pkg-tasks.table_prefix') . 'task_item_groups';

        Schema::table($table, function (Blueprint $blueprint) {
            // Logical reference to asset_types.id. No DB-level FK: cross-package.
            $blueprint->unsignedBigInteger('asset_type_id')
                ->nullable()
                ->after('task_group_id')
                ->comment('Logical reference to asset_types.id (no FK, cross-package)');

            $blueprint->index('asset_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('pkg-tasks.table_prefix') . 'task_item_groups';

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->dropIndex(['asset_type_id']);
            $blueprint->dropColumn('asset_type_id');
        });
    }
};
