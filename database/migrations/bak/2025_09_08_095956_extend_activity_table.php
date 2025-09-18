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
        $tablePrefix = config('pkg-activities.table_prefix');
        $tsTablePrefix = config('pkg-tickets.table_prefix');

        Schema::table($tablePrefix . 'activities', function (Blueprint $table) use ($tablePrefix, $tsTablePrefix) {
            $table->foreignId('ticket_id')
                ->nullable()
                ->comment('Binds this activity instance to ticket.')
                ->constrained($tsTablePrefix . 'tickets', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-activities.table_prefix');

        Schema::table($tablePrefix . 'activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_id');
        });
    }
};
