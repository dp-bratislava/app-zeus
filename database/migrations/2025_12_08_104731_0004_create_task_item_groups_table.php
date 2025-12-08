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
        $tablePrefix = config('pkg-tasks.table_prefix');

        // task item groups
        Schema::create($tablePrefix . 'task_item_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of task item groups e.g. activity templates, inspection templates, ...');
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify group in application layer');
            $table->string('title')->nullable();
            $table->foreignId('task_group_id')
                ->nullable()
                ->comment('Task group for hierarchical strucuring of groups')
                ->constrained($tablePrefix . 'task_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table($tablePrefix . 'task_items', function (Blueprint $table) use ($tablePrefix) {
            $table->foreignId('group_id')
                ->nullable()
                ->after('state')
                ->constrained($tablePrefix . 'task_item_groups', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tasks.table_prefix');

        Schema::table($tablePrefix . 'task_items', function (Blueprint $table) use ($tablePrefix) {
            $table->dropConstrainedForeignId('group_id');
        });
        Schema::dropIfExists($tablePrefix . 'task_item_groups');
    }
};
