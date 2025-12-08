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
        $tablePrefix = config('database.table_prefix');
        $ticketTablePrefix = config('pkg-tickets.table_prefix');

        // ticket bindings
        Schema::create($tablePrefix . 'ticket_item_assignments', function (Blueprint $table) use ($ticketTablePrefix) {
            $table->comment("Relations binding ticket items to other domains");
            $table->id();
            $table->foreignId('ticket_item_id')
                ->nullable(false)
                ->comment('')
                ->constrained($ticketTablePrefix . 'ticket_items', 'id');
            // entity responsible e.g. maintenance group, department, ...
            $table->unsignedBigInteger('assigned_to_id')
                ->nullable()
                ->comment('Entity responsible for this ticket item e.g. maintenance group, department, ...');
            $table->string('assigned_to_type')
                ->nullable()
                ->comment("Class of related polymorphic record. Determines respective database table holding records of this type.");
            $table->foreignId('author_id')
                ->nullable(false)
                ->comment('User that created ticket item')
                ->constrained('users');
            $table->foreignId('supervised_by')
                ->nullable()
                ->comment("User responsible for ticket item")
                ->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // add indexes for polymorphic relations
        Schema::table($tablePrefix . 'ticket_item_assignments', function (Blueprint $table) use ($ticketTablePrefix) {
            $table->index(['assigned_to_id', 'assigned_to_type'], 'idx_ticket_item_assigned_to');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'ticket_item_assignments');
    }
};
