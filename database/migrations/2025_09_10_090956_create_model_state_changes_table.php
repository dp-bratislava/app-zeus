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

        Schema::create($tablePrefix . 'model_state_changes', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('from_state')
                ->nullable()
                ->comment("Original state we're changing from, or null if new model");
            $table->string('to_state')
                ->comment("New state we're changing to.");
            $table->foreignId('user_id')
                ->nullable()
                ->comment('User that made the state change.')
                ->constrained()
                ->nullOnDelete();
            $table->string('source')
                ->default('http')
                ->comment('Source of change. E.g. user action, system, job, ...'); // optional
            $table->timestamp('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablePrefix = config('database.table_prefix');

        Schema::dropIfExists($tablePrefix . 'model_state_changes');
    }
};
