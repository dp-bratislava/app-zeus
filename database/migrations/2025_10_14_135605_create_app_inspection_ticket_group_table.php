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
        $tickeTablePrefix = config('pkg-tickets.table_prefix');
        $inspectionTablePrefix = config('pkg-inspections.table_prefix');

        Schema::create($tablePrefix . 'inspection_ticket_group', function (Blueprint $table) use ($tickeTablePrefix, $inspectionTablePrefix) {
            $table->comment('Pivot for inspection to ticket group mapping');

            $table->id();

            $table->foreignId('ticket_group_id')
                ->nullable(false)
                ->comment('')
                ->constrained($tickeTablePrefix . 'ticket_groups', 'id');
            $table->foreignId('inspection_id')
                ->nullable(false)
                ->comment('')
                ->constrained($inspectionTablePrefix . 'inspection_templates', 'id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'inspection_ticket_group');
    }
};
