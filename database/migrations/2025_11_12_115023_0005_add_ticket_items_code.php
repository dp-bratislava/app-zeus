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
        $tablePrefix = config('pkg-tickets.table_prefix');

        // ticket items
        Schema::table($tablePrefix . 'ticket_items', function (Blueprint $table) use ($tablePrefix) {
            $table->unsignedInteger('child_index')
                ->nullable()
                ->comment('Sequential number specifying ticket item order in ticket. Handled by trigger.');
            // Add generated column for code
            $table->string('code')
                ->comment('Generated column for ticket item code')
                ->storedAs('CONCAT(ticket_id, ".", child_index)');

            // Unique constraint to prevent duplicate child_index per ticket
            $table->unique(['ticket_id', 'child_index'], 'ticket_child_unique');

        });
        
        // Drop trigger if exists
        DB::unprepared('DROP TRIGGER IF EXISTS ' . $tablePrefix . 'ticket_items_before_insert;');

        // Create trigger to auto-generate child_index
        DB::unprepared("
            CREATE TRIGGER " . $tablePrefix . "ticket_items_before_insert
            BEFORE INSERT ON " . $tablePrefix . "ticket_items
            FOR EACH ROW
            BEGIN
                DECLARE seq INT;

                -- Find current max child_index for this ticket
                SELECT COALESCE(MAX(child_index), 0) + 1
                INTO seq
                FROM " . $tablePrefix . "ticket_items
                WHERE ticket_id = NEW.ticket_id;

                SET NEW.child_index = seq;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tickets.table_prefix');

        // Drop trigger first
        DB::unprepared('DROP TRIGGER IF EXISTS ' . $tablePrefix . 'ticket_items_before_insert;');

        // Drop table
        Schema::table($tablePrefix . 'ticket_items', function (Blueprint $table) use ($tablePrefix) {
            $table->dropUnique('ticket_child_unique');
            $table->dropColumn([
                'code',
                'child_index'
            ]);
        });
    }
};
