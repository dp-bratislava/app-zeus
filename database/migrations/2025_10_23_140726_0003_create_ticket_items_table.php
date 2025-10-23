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

        // ticket items
        Schema::create($tablePrefix . 'ticket_items', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->foreignId('ticket_id')
                ->nullable()
                ->comment('Parent ticket')
                ->constrained($tablePrefix . 'tickets', 'id');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('state')
                ->nullable()
                ->comment("Current ticket item state");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('pkg-tickets.table_prefix');

        Schema::dropIfExists($tablePrefix . 'ticket_items');
    }
};
