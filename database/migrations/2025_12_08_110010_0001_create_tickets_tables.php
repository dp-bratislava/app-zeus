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

        // ticket  types
        Schema::create($tablePrefix . 'ticket_types', function (Blueprint $table) use ($tablePrefix) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // tickets
        Schema::create($tablePrefix . 'tickets', function (Blueprint $table) use ($tablePrefix) {
            $table->id();
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('type_id')
                ->nullable()
                ->comment('')
                ->constrained($tablePrefix . 'ticket_types', 'id');
            $table->string('state')
                ->nullable()
                ->comment("Current ticket state");                
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

        Schema::dropIfExists($tablePrefix . 'tickets');
        Schema::dropIfExists($tablePrefix . 'ticket_types');
    }
};
