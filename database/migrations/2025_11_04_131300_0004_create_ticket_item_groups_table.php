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
        $tablePrefix = config('pkg-tickets.table_prefix');

        // ticket item groups
        Schema::create($tablePrefix . 'ticket_item_groups', function (Blueprint $table) use ($tablePrefix) {
            $table->comment('List of ticket item groups e.g. activity templates, inspection templates, ...');
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify group in application layer');
            $table->string('title')->nullable();
            $table->foreignId('ticket_group_id')
                ->nullable()
                ->comment('Ticket group for hierarchical strucuring of groups')
                ->constrained($tablePrefix . 'ticket_groups', 'id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table($tablePrefix . 'ticket_items', function (Blueprint $table) use ($tablePrefix) {
            $table->foreignId('group_id')
                ->nullable()
                ->after('state')
                ->constrained($tablePrefix . 'ticket_item_groups', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tickets.table_prefix');

        Schema::table($tablePrefix . 'ticket_items', function (Blueprint $table) use ($tablePrefix) {
            $table->dropConstrainedForeignId('group_id');
        });
        Schema::dropIfExists($tablePrefix . 'ticket_item_groups');
    }
};
