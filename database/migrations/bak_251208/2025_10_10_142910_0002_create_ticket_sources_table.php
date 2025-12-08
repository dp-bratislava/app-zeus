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

        // ticket sources
        Schema::create($tablePrefix . 'ticket_sources', function (Blueprint $table) {
            $table->id();
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->comment('Unique code to identify source in application layer');
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // add source to ticket
        Schema::table($tablePrefix . 'tickets', function (Blueprint $table) use ($tablePrefix) {
            $table->foreignId('source_id')
                ->nullable()
                ->comment('Source of the ticket')
                ->after('state')
                ->constrained($tablePrefix . 'ticket_sources', 'id');
        });            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tickets.table_prefix');

        Schema::table($tablePrefix . 'tickets', function (Blueprint $table) use ($tablePrefix) {
            $table->dropConstrainedForeignId('source_id');
        });

        Schema::dropIfExists($tablePrefix . 'ticket_sources');
    }
};
